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
            //print_r($recept);die;

            $currentIngredients = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Ingredient')->findAll();

            $serializer = $this->container->get('jms_serializer');
            //$currentIngredients = json_decode($serializer->serialize($currentIngredients, 'json'));
            //print_r($currentIngredients);die;


            foreach ($recept->getRecipeIngredients() as $key => $recipeingredient) {
                foreach ($currentIngredients as $currentIngredient) {
                    if ($recipeingredient->getIngredient()->getName() == $currentIngredient->getName()) {
                        $ingredient = $recipeingredient->getIngredient();
                        $ingredient = $currentIngredient;
                        //print_r(json_decode($serializer->serialize($ingredient, 'json')));die;
                        $recipeingredient->setIngredient($ingredient);
                    }
                }
            }

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
