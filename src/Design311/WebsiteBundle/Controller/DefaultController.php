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

            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $query = $qb
                ->from('Design311WebsiteBundle:Dinner', 'd')
                ->select('d')
                ->leftJoin('d.participants', 'p')
                ->where($qb->expr()->gte('d.date', ':today'))
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->eq('d.user', ':user'),
                        $qb->expr()->eq('p.user', ':user')
                    ))
                ->groupBy('d.id')
                ->orderBy('d.date')
                ->setParameter('today', new \DateTime())
                ->setParameter('user', $this->getUser()->getId())
                ->getQuery();

            $dinners = $query->execute();

        	return $this->render('Design311WebsiteBundle:User:index.html.twig', array(
                'dinners' => $dinners
                ));
        }
        else{
            //logged out

            $newestrecipes = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findBy(array(), array('id' => 'DESC'), 2);

            return $this->render('Design311WebsiteBundle:Default:index.html.twig', array(
                'newestrecipes' => $newestrecipes
                )); 
		}
    }

}