<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\DinnerType;
use Design311\WebsiteBundle\Entity\Dinner;


class DinnerController extends GeocodeController
{
    public function indexAction(Request $request)
    {
        //TODO only get dinners in the future
        $dinners = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Dinner')->findAll();

        foreach ($dinners as $key => $dinner) {
            $locations[$key][0] = $dinner->getAddress()->getLat();
            $locations[$key][1] = $dinner->getAddress()->getLng();
        }

        return $this->render(
            'Design311WebsiteBundle:Dinner:index.html.twig',
            array(
                'dinners' => $dinners,
                'locations' => $locations
                )
        );
    }

    public function addAction(Request $request)
    {
        $form = $this->createForm(new DinnerType(), new Dinner(), array(
            'action' => $this->generateUrl('design311website_dinners_add_create'),
        ));

        return $this->render(
            'Design311WebsiteBundle:Dinner:add.html.twig',
            array('form' => $form->createView())
        );
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new DinnerType(), new Dinner());

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
