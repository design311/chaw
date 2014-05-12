<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;


class AjaxController extends Controller
{
    public function likeRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

        $response = $recipe->getLikedBy()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addLikedBy($this->getUser());
        }
        else{
            $recipe->removeLikedBy($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new JsonResponse(array(
            'status' => $response
        ));
    }

    public function saveRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

        $response = $recipe->getSavedBy()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addSavedBy($this->getUser());
        }
        else{
            $recipe->removeSavedBy($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new JsonResponse(array(
            'status' => $response
        ));
    }

    public function shopRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

        $response = $recipe->getShoppinglistFrom()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addShoppinglistFrom($this->getUser());
        }
        else{
            $recipe->removeShoppinglistFrom($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        //return new Response(count($this->getUser()->getShoppinglist()));
        return new JsonResponse(array(
            'status' => $response,
            'shoppinglistcount' => count($this->getUser()->getShoppinglist())
        ));
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
