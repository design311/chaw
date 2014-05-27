<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends BaseController
{
    public function indexAction()
    {
    	$securityContext = $this->container->get('security.context');
		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            //logged in
        	return $this->render('Design311WebsiteBundle:User:index.html.twig');
        }
        else{
            //logged out
            return $this->render('Design311WebsiteBundle:Default:index.html.twig'); 
		}
    }

}