<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;


class RecipeController extends Controller
{
    public function indexAction()
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
    }

    public function editAction($recipeId, Request $request)
    {
        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

        $originalPhotos = new ArrayCollection();
        foreach ($recipe->getPhotos() as $photo) {
            $originalPhotos->add($photo);
        }
        $originalIngredients = new ArrayCollection();
        foreach ($recipe->getRecipeIngredients() as $ingredient) {
            $originalIngredients->add($ingredient);
        }


        $form = $this->createForm(new RecipeType(), $recipe);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $recipe = $form->getData();

            $photos = $recipe->getPhotos();
            $ingredients = $recipe->getRecipeIngredients();

            foreach ($originalIngredients as $ingredient) {
                if (false === $recipe->getRecipeIngredients()->contains($ingredient)) {
                    $recipe->getRecipeIngredients()->removeElement($ingredient);
                    $em->remove($ingredient);
                }
            }

            foreach ($originalPhotos as $photo) {
                if (false === $recipe->getPhotos()->contains($photo)) {
                    $recipe->getPhotos()->removeElement($photo);
                    $em->remove($photo);
                }
            }

            $em->persist($recipe);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_recepten'));
        }

        return $this->render(
            'Design311WebsiteBundle:Recipe:add.html.twig',
            array('form' => $form->createView())
        );
    }
}
