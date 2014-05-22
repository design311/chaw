<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\MessageType;
use Design311\WebsiteBundle\Form\Type\MailType;
use Design311\WebsiteBundle\Form\Type\DinnerType;
use Design311\WebsiteBundle\Entity\Dinner;
use Design311\WebsiteBundle\Entity\DinnerParticipantRequest;
use Design311\WebsiteBundle\Entity\DinnerInvite;


class DinnerController extends GeocodeController
{
    public function indexAction()
    {
        //TODO only get dinners in the future & not fully booked
        $dinners = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findAll();
        $filters = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsFilter(1);

        foreach ($dinners as $dinner) {
            $locations[$dinner->getId()][0] = $dinner->getAddress()->getLat();
            $locations[$dinner->getId()][1] = $dinner->getAddress()->getLng();
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:index.html.twig',
            array(
                'dinners' => $dinners,
                'locations' => $locations,
                'filters' => $filters
                )
        );
    }

    public function detailAction($permalink)
    {
        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink($permalink);

        return $this->render(
            'Design311WebsiteBundle:Dinner:detail.html.twig',
            array('dinner' => $dinner)
        );
    }

    public function participateAction(Request $request, $permalink)
    {
        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink($permalink);

        $data = array();
        $form = $this->createForm(new MessageType(), $data);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $participantRequest = new DinnerParticipantRequest();

            $participantRequest->setUser($this->getUser());
            $participantRequest->setDinner($dinner);

            $em = $this->getDoctrine()->getManager();
            $em->persist($participantRequest);
            $em->flush();

            $mail = \Swift_Message::newInstance()
                ->setSubject($this->getUser()->getDisplayName() . ' wil graag deelnemen aan je dinner.')
                ->setFrom($this->getUser()->getEmail())
                ->setReplyTo($this->getUser()->getEmail())
                ->setTo($dinner->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'Design311WebsiteBundle:Mail:participate.html.twig',
                        array(
                            'message' => $data['message'],
                            'name' => $this->getUser()->getDisplayName(),
                            'dinner' => $dinner,
                            'participant' => $participantRequest
                            )
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($mail);

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink())));
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:participate.html.twig',
            array('form' => $form->createView())
        );
    }

    public function inviteAction(Request $request, $permalink)
    {
        $invalidMails = [];

        $data = array();
        $form = $this->createForm(new MailType(), $data);

        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink($permalink);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();

            $emails = explode(',', $data['mails']);

            foreach ($emails as $email) {
                if (!\Swift_Validate::email($email)) {
                    $invalidMails[] = $email;
                }
            }

            if (count($invalidMails) == 0) {

                foreach ($emails as $email) {

                    $invite = new DinnerInvite();
                    $invite->setDinner($dinner);
                    $invite->setEmail($email);

                    $em->persist($invite);
                    $em->flush();

                    $mail = \Swift_Message::newInstance()
                        ->setSubject('Persoonlijke uitnodiging voor het dinner van ' . $this->getUser()->getDisplayName())
                        ->setFrom($this->getUser()->getEmail())
                        ->setReplyTo($this->getUser()->getEmail())
                        ->setTo($email)
                        ->setBody(
                            $this->renderView(
                                'Design311WebsiteBundle:Mail:invite.html.twig',
                                array(
                                    'message' => $data['message'],
                                    'name' => $this->getUser()->getDisplayName(),
                                    'dinner' => $dinner,
                                    'invite' => $invite
                                    )
                            ),
                            'text/html'
                        )
                    ;
                    $this->get('mailer')->send($mail);

                }

                return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink())));
            }
        }

        return $this->render('Design311WebsiteBundle:Dinner:invite.html.twig', array(
            'form' => $form->createView(),
            'invalidMails' => $invalidMails
        ));
    }

    public function addAction(Request $request)
    {
        $dinner = new Dinner();
        $dinner->setAddress($this->getUser()->getAddress());

        $metacategories = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsCalculated(0);
        $form = $this->createForm(new DinnerType($metacategories), $dinner);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $dinner = $form->getData();

            $metafields = $form->get('metafields')->getData();
            foreach ($metafields as $category => $meta) {
                
                foreach ($metacategories as $metacat) {
                    if ($metacat->getName() == $category) {

                        $qb = $em->createQueryBuilder();
                        $query = $qb
                            ->from('Design311WebsiteBundle:DinnerMeta', 'dm')
                            ->select('dm')
                            ->where($qb->expr()->eq('dm.category', $metacat->getId()))
                            ->andWhere($qb->expr()->in('dm.id', $meta))
                            ->getQuery();

                        $dinnerMetas = $query->execute();

                        foreach ($dinnerMetas as $dinnerMeta) {
                            $dinner->addMetum($dinnerMeta);
                        }

                        break; //category found, break for each loop
                    }
                }
            }

            $latLng = $this->geocode($dinner->getAddress());
            if (is_object($latLng)) {
                $dinner->getAddress()->setLat($latLng->lat);
                $dinner->getAddress()->setLng($latLng->lng);
            }
            else{
                //coords could not be found
                $dinner->getAddress()->setLat(0);
                $dinner->getAddress()->setLng(0);
            }

            $dinner->setUser($this->getUser());

            $em->persist($dinner);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_dinners'));
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:add.html.twig',
            array('form' => $form->createView())
        );
    }
}
