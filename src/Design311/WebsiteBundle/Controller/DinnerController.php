<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\DinnerType;
use Design311\WebsiteBundle\Entity\Dinner;


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

    public function addAction(Request $request)
    {
        $metadata = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerCategories')->findByIsCalculated(0);
        $form = $this->createForm(new DinnerType($metadata), new Dinner());

        $form->handleRequest($request);

        if ($form->isValid()) {

            $dinner = $form->getData();

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

            $em = $this->getDoctrine()->getManager();
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
