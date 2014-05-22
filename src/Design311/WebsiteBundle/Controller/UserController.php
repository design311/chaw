<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Design311\WebsiteBundle\Form\Type\UserType;
use Design311\WebsiteBundle\Entity\User;


class UserController extends GeocodeController
{
    public function loginAction(Request $request, $auth = false)
    {
    	$session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'Design311WebsiteBundle:User:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $error,
                'auth' => $auth
            )
        );
    }

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();

            $latLng = $this->geocode($user->getAddress());
            if (is_object($latLng)) {
                $user->getAddress()->setLat($latLng->lat);
                $user->getAddress()->setLng($latLng->lng);
            }
            else{
                //coords could not be found
                $user->getAddress()->setLat(0);
                $user->getAddress()->setLng(0);
            }

            //set default display name
            $user->setDisplayName($user->getUsername());
            
            //hash password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            //set aantal personen
            $user->setAantalPersonen(4);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_homepage'));
        }

        return $this->render(
            'Design311WebsiteBundle:User:register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function editAction(Request $request)
    {
        $form = $this->createForm(new UserType(), $this->getUser());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();

            if ($user->getAddress() != null) {
                $latLng = $this->geocode($user->getAddress());
                if (is_object($latLng)) {
                    $user->getAddress()->setLat($latLng->lat);
                    $user->getAddress()->setLng($latLng->lng);
                }
                else{
                    //coords could not be found
                    $user->getAddress()->setLat(0);
                    $user->getAddress()->setLng(0);
                }
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_homepage'));
        }

        return $this->render('Design311WebsiteBundle:User:edit.html.twig',array(
            'form' => $form->createView()
        ));
    }

    public function changeAantalAction(Request $request)
    {

        $aantal = (int)$request->get('aantal');

        //prevent injection
        if (is_int($aantal)) {
            if ($aantal <= 0) {
                $aantal = 1;
            }
            $this->getUser()->setAantalPersonen($aantal);

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
        }

        if ($request->headers->get('referer')) {
            return new RedirectResponse($request->headers->get('referer'));
        }
        return new RedirectResponse($this->generateUrl('design311website_homepage'));
    }

    public function testingAction(Request $request)
    {
        echo 'testing';

        $dinner = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findOneByPermalink('eenvoudige-soep');

        ladybug_dump($dinner); die;

        $r = \Swift_Validate::email('asfsafdksja@asdfkj.com');

        var_dump($r);

        /*$mail = \Swift_Message::newInstance()
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
        $this->get('mailer')->send($mail);*/

        die;

        return new RedirectResponse($this->generateUrl('design311website_homepage'));
    }
}
