<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;


class AjaxController extends Controller
{
    public function likeRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

        $response = $recipe->getLikedBy()->contains($this->getUser()) ? 0 : 1; // 1 if will like, 0 if will dislike

        if ($recipe->getLikedBy()->contains($this->getUser())) {
            $recipe->removeLikedBy($this->getUser());
        }
        else{
            $recipe->addLikedBy($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new Response($response); // 1 for like, 0 for dislike
    }
/*
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

            if ($recept->getRecipeIngredients()) {
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
            }

            $recept->setUser($this->getUser());

            $em->persist($recept);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_recepten'));
        }

        return $this->render(
            'Design311WebsiteBundle:Recipe:add.html.twig',
            array('form' => $form->createView())
        );
    }*/
}
