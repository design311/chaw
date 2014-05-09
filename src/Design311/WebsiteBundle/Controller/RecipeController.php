<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;


class RecipeController extends GeocodeController
{
    public function indexAction(Request $request)
    {
        //TODO only get recepten in the future & not fully booked
        $recipes = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findAll();

        return $this->render(
            'Design311WebsiteBundle:Recipe:index.html.twig',
            array(
                'recipes' => $recipes
                )
        );
    }

    public function addAction(Request $request)
    {
        $form = $this->createForm(new RecipeType(), new Recipe());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $recept = $form->getData();

            $em->persist($recept);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_recepten'));
        }

        return $this->render(
            'Design311WebsiteBundle:Recipe:add.html.twig',
            array('form' => $form->createView())
        );
    }
}
