<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Design311\WebsiteBundle\Form\Type\MessageType;
use Design311\WebsiteBundle\Form\Type\MailType;
use Design311\WebsiteBundle\Form\Type\DinnerType;
use Design311\WebsiteBundle\Entity\Dinner;
use Design311\WebsiteBundle\Entity\Address;
use Design311\WebsiteBundle\Entity\DinnerParticipantRequest;
use Design311\WebsiteBundle\Entity\DinnerInvite;


class DinnerController extends BaseController
{
    private function getDinnersJson($dinners){
        if (count($dinners) == 0) {
            return '{}';
        }

        $counter = 0;
        foreach ($dinners as $dinner) {
            $dinnersSimple[$counter]['id'] = $dinner->getId();
            $dinnersSimple[$counter]['date'] = $dinner->getDate();
            $dinnersSimple[$counter]['title'] = $dinner->getTitle();
            $dinnersSimple[$counter]['permalink'] = $dinner->getPermalink();
            $dinnersSimple[$counter]['diet'] = $dinner->getDiet()->getValue();
            $dinnersSimple[$counter]['price'] = $dinner->getPrice();
            $dinnersSimple[$counter]['lat'] = $dinner->getAddress()->getLat();
            $dinnersSimple[$counter]['lng'] = $dinner->getAddress()->getLng();
            $counter++;
        }

        $serializer = $this->container->get('serializer');
        $dinnersSimple = $serializer->serialize($dinnersSimple, 'json');

        return $dinnersSimple;
    }

    public function indexAction()
    {
        //TODO only get dinners not fully booked
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb
            ->from('Design311WebsiteBundle:Dinner', 'd')
            ->select('d')
            ->where($qb->expr()->gte('d.date', ':today'))
            ->setParameter('today', new \DateTime())
            ->leftJoin('d.participants', 'p')
            ->groupBy('d.id')
            ->having($qb->expr()->count('d.id') . '< d.maxInvitees')
            ->getQuery();

        $dinners = $query->execute();

        $dinnersJSON = $this->getDinnersJson($dinners);

        $filters = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsFilter(1);
        $diets = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Diet')->findAll();

        return $this->render(
            'Design311WebsiteBundle:Dinner:index.html.twig',
            array(
                'dinners' => $dinnersJSON,
                'filters' => $filters,
                'diets' => $diets
                )
        );
    }

    public function filterDinnersAction(Request $request)
    {
        $maxprice = $request->get('maxprice') ? $request->get('maxprice') : 50;

        //todo only select what's needed
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $qb
            ->from('Design311WebsiteBundle:Dinner', 'd')
            ->select('d')
            ->andWhere($qb->expr()->gte('d.date', ':today'))
            ->andWhere($qb->expr()->lte('d.price', $maxprice))
            ->setParameter('today', new \DateTime())
            ->leftJoin('d.participants', 'p')
            ->groupBy('d.id')
            ->having($qb->expr()->count('d.id') . '< d.maxInvitees');

        if ($request->get('diet') != null && $request->get('diet') != 0) {
            $qb = $qb->andWhere($qb->expr()->in('d.diet', $request->get('diet')));
        }

        $query = $qb->getQuery();
        $dinners = $query->execute();

        $dinnersJSON = $this->getDinnersJson($dinners);

        return new Response($dinnersJSON);
    }

    public function detailAction(Request $request, $permalink)
    {
        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink($permalink);
        if (!$dinner) {
            throw $this->createNotFoundException('Dinner niet gevonden');
        }

        $data = array();
        $form = $this->createForm(new MessageType(), $data, array(
            'action' => $this->generateUrl('design311website_dinners_detail', array('permalink' => $permalink))
            ));

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
                ->setSubject($this->getUser()->getDisplayName() . ' wil graag deelnemen aan je dinner')
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

            $this->get('session')->getFlashBag()->add('success','Je deelnameverzoek is verzonden!');
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:detail.html.twig', array(
                'dinner' => $dinner,
                'form' => $form->createView()
            ));
    }

    public function inviteAction(Request $request, $permalink)
    {
        $invalidMails = array();

        $data = array();
        $form = $this->createForm(new MailType(), $data, array(
            'action' => $this->generateUrl('design311website_dinners_invite', array('permalink' => $permalink))
            ));

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
                    $invite->setEmail(trim($email));

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


                $count = count($emails);
                $this->get('session')->getFlashBag()->add('success',($count == 1 ? 'Er is ' :'Er zijn '). $count . ' uitnodiging'. ($count > 1 ? 'en' :'').' verzonden.' );

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
        $metacategories = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsCalculated(0);
        $form = $this->createForm(new DinnerType($metacategories), $dinner, array(
            'action' => $this->generateUrl('design311website_dinners_add')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $dinner = $form->getData();

            /*$metafields = $form->get('metafields')->getData();
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
            }*/

            if ($form->get('change_address')->getData() == 1) {
                //only geocode if new address
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

                $user = $this->getUser()->setAddress($dinner->getAddress());
                $em->persist($user);
            }
            else{
                $dinner->setAddress($this->getUser()->getAddress());
            }

            $dinner->setUser($this->getUser());
            $dinner->setPermalink($this->getPermalink($dinner->getTitle(), 'Dinner'));

            $em->persist($dinner);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:add.html.twig',
            array('form' => $form->createView())
        );
    }

    public function editAction(Request $request, $permalink)
    {
        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink($permalink);
        if (!$dinner) {
            throw $this->createNotFoundException('Dinner niet gevonden');
        }

        if ($this->getUser() != $dinner->getUser()) {
            $this->get('session')->getFlashBag()->add('error','Dit dinner mag je niet bewerken');
            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }

        $metacategories = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsCalculated(0);
        $form = $this->createForm(new DinnerType($metacategories), $dinner, array(
            'action' => $this->generateUrl('design311website_dinners_edit', array('permalink' => $dinner->getPermalink()) )
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $dinner = $form->getData();

            /*$metafields = $form->get('metafields')->getData();
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
*/
            if ($form->get('change_address')->getData() == 1) {
                //only geocode if new address
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
            }

            $em->persist($dinner);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:add.html.twig',array(
                'form' => $form->createView(),
                'dinner' => $dinner
            )
        );
    }

    public function dinnersBeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb
            ->from('Design311WebsiteBundle:Dinner', 'd')
            ->select('d')
            ->leftJoin('d.address', 'a')
            ->where($qb->expr()->gte('d.date', ':today'))
            ->andWhere($qb->expr()->like('a.country', $qb->expr()->literal('Be%')))
            ->orderBy('d.date')
            ->setParameter('today', new \DateTime())
            ->getQuery();

        $dinners = $query->execute();

        return $this->render(
            'Design311WebsiteBundle:Dinner:dinnerInfo.html.twig',
            array(
                'country' => 'be',
                'dinners' => $dinners
                )
        );
    }

    public function dinnersNlAction()
    {
        $country = array('Belgium', 'Belgie', 'België', 'BE');
        $country = array_merge($country, array_map('strtolower', $country));

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb
            ->from('Design311WebsiteBundle:Dinner', 'd')
            ->select('d')
            ->leftJoin('d.address', 'a')
            ->where($qb->expr()->gte('d.date', ':today'))
            ->andWhere($qb->expr()->orX(
                        $qb->expr()->like('a.country', $qb->expr()->literal('Ne%')),
                        $qb->expr()->like('a.country', $qb->expr()->literal('nl'))
                    ))
            ->orderBy('d.date')
            ->setParameter('today', new \DateTime())
            ->getQuery();

        $dinners = $query->execute();

        return $this->render(
            'Design311WebsiteBundle:Dinner:dinnerInfo.html.twig',
            array(
                'country' => 'nl',
                'dinners' => $dinners
                )
        );
    }
}
