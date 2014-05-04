<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\UserType;
use Design311\WebsiteBundle\Entity\User;


class UserController extends Controller
{
    public function loginAction(Request $request)
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
                'error'         => $error,
            )
        );
    }

    public function registerAction()
    {
    	$user = new User();
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('design311website_register_create'),
        ));

	    return $this->render(
	        'Design311WebsiteBundle:User:register.html.twig',
	        array('form' => $form->createView())
	    );
    }

    public function createAction(Request $request)
	{
	    $em = $this->getDoctrine()->getManager();

	    $form = $this->createForm(new UserType(), new User());

	    $form->handleRequest($request);

	    if ($form->isValid()) {
	        $user = $form->getData();

	        //set default display name
	        $user->setDisplayName($user->getUsername());
	        
	        //hash password
	        $factory = $this->get('security.encoder_factory');
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			$user->setPassword($password);

	        $em->persist($user);
	        $em->flush();

	        return $this->redirect($this->generateUrl('design311website_homepage'));
	    }

	    return $this->render(
	        'Design311WebsiteBundle:User:register.html.twig',
	        array('form' => $form->createView())
	    );
	}
}
