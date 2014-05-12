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
                'recipes' => $recipes,
                'shoppinglistcount' => count($this->getUser()->getShoppinglist())
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

    public function shoppinglistAction()
    {
        $shoppinglist = $this->getUser()->getShoppinglist();

        $ingredients = [];

        foreach ($shoppinglist as $recipe) {
            $recipeIngredients = $recipe->getRecipeIngredients();

            foreach ($recipeIngredients as $recipeIngredient) {
                $ingredient = $recipeIngredient->getIngredient()->getName();
                if (array_key_exists($ingredient, $ingredients)) {
                    $ingredients[$ingredient] = $this->smartadd(array($ingredients[$ingredient], $recipeIngredient->getAmount()));
                }
                else{
                    $ingredients[$ingredient] = $recipeIngredient->getAmount();
                }
            }
        }

        return $this->render('Design311WebsiteBundle:Recipe:shoppinglist.html.twig', array(
            'shoppinglist' => $shoppinglist,
            'ingredients' => $ingredients
        ));
    }

    function smartAdd($values){
        $units = array(
            'weight' => array(
                0.001 => array('mg', 'miligram'),
                1 => array('g', 'gram'),
                1000 => array('kg', 'kilogram'),
                1000000 => array('ton'),
            ),
            'length' => array(
                0.001 => array('mm', 'milimeter'),
                0.01 => array('cm', 'centimeter'),
                0.1 => array('dm', 'decimeter'),
                1 => array('m', 'meter'),
                1000 => array('km', 'kilometer'),
            ),
            'liquid' => array(
                0.001 => array('ml', 'mililiter'),
                0.01 => array('cl', 'centiliter'),
                0.1 => array('dl', 'deciliter'),
                1 => array('l', 'liter'),
            )
        );

        $total = '';
        $unittype = false;

        foreach ($values as $key => $value) {
            preg_match_all('/([\d]+)/', $value, $numberparts);

            $number = '';
            preg_match('/'.$numberparts[0][count($numberparts[0])-1].'(.+)/',$value, $unit);
            if (array_key_exists(1, $unit)) {
                $currentUnit = $unit[1];
            }
            else{
                $currentUnit = false;
            }

            foreach ($numberparts[0] as $key => $part) {
                if (count($numberparts[0])-1 === $key && $key !== 0) {
                    $number.= localeconv()['decimal_point'];
                }
                $number .= $part;
            }

            echo $number . $currentUnit;
            echo "\r\n";

            if ($currentUnit) {
                if ($unittype == '') {
                    foreach ($units as $type => $unit) {
                        foreach ($unit as $key => $scale) {
                            if (array_search($currentUnit, $scale) !== FALSE) {
                                $unittype = $type;
                                $number *= $key;
                            }
                        }
                    }
                }
                else{
                    $found = false;
                    foreach ($units[$unittype] as $key => $scale) {
                        if (array_search($currentUnit, $scale) !== FALSE) {
                            $found = true;
                            $number *= $key;
                        }
                    }
                    if (!$found) {
                        throw new Exception('Unit mismatch');
                    }
                }
            }

            $total += $number;
        }

        //echo ((strlen((int)$total) + 1) / 3);die;
        /*  if (strlen((int)$total) > 5) {
            if (array_key_exists(1000, $units[$unittype])) {
                $total = $total / 1000 . $units[$unittype][1000][0];
            }
        }*/
        if ($unittype) {
            return $total . $units[$unittype][1][0];
        }
        else{
            return $total;
        }

    }
}
