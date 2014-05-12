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
                foreach ($recept->getRecipeIngredients() as $key => $recipeIngredient) {
                    foreach ($currentIngredients as $currentIngredient) {
                        if ($recipeIngredient->getIngredient()->getName() == $currentIngredient->getName()) {
                            $ingredient = $recipeIngredient->getIngredient();
                            $ingredient = $currentIngredient;
                            //print_r(json_decode($serializer->serialize($ingredient, 'json')));die;
                            $recipeIngredient->setIngredient($ingredient);
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
                //$amount = $recipeIngredient->getAmount();
                $amount = $this->smartDivide($recipeIngredient->getAmount(), $recipe->getAantalPersonen() / $this->getUser()->getAantalPersonen());
                if (array_key_exists($ingredient, $ingredients)) {
                    $ingredients[$ingredient] = $this->smartAdd(array($ingredients[$ingredient], $amount));
                }
                else{
                    $ingredients[$ingredient] = $this->smartAdd(array($amount));
                }
            }
        }

        //die;

        return $this->render('Design311WebsiteBundle:Recipe:shoppinglist.html.twig', array(
            'shoppinglist' => $shoppinglist,
            'ingredients' => $ingredients
        ));
    }

    private $units = array(
        'weight' => array(
            '0.001' => array('mg', 'miligram'),
            '1' => array('g', 'gram'),
            '1000' => array('kg', 'kilogram'),
            '1000000' => array('ton'),
        ),
        'length' => array(
            '0.001' => array('mm', 'milimeter'),
            '0.01' => array('cm', 'centimeter'),
            '0.1' => array('dm', 'decimeter'),
            '1' => array('m', 'meter'),
            '1000' => array('km', 'kilometer'),
        ),
        'liquid' => array(
            '0.001' => array('ml', 'mililiter'),
            '0.01' => array('cl', 'centiliter'),
            '0.1' => array('dl', 'deciliter'),
            '1' => array('l', 'liter'),
        )
    );

    private function smartDivide($value, $divider){
        $numberAndUnit = $this->getNumberAndUnit($value);
        $number = $numberAndUnit[0];
        $unit = $numberAndUnit[1];
        $result = $number / $divider;

        if ($unit == 'g') {
            $result = round($result);
        }

        return $result . $unit;
    }

    private function smartAdd($values){
        $total = '';
        $unittype = false;

        foreach ($values as $key => $value) {
            
            $numberAndUnit = $this->getNumberAndUnit($value);
            $number = $numberAndUnit[0];
            $currentUnit = $numberAndUnit[1];
            
            if ($currentUnit) {
                if ($unittype == '') {
                    foreach ($this->units as $type => $unit) {
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
                    foreach ($this->units[$unittype] as $key => $scale) {
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
            if (array_key_exists(1000, $this->units[$unittype])) {
                $total = $total / 1000 . $this->units[$unittype][1000][0];
            }
        }*/

        if ($unittype) {
            return $total . $this->units[$unittype][1][0];
        }
        else{
            return $total;
        }
    }

    private function getNumberAndUnit($value){
        preg_match_all('/([\d]+)/', $value, $numberparts);

        $number = '';
        preg_match('/'.$numberparts[0][count($numberparts[0])-1].'(.+)/',$value, $unit);
        if (array_key_exists(1, $unit)) {
            $unit = $unit[1];
        }
        else{
            $unit = false;
        }

        foreach ($numberparts[0] as $key => $part) {
            if (count($numberparts[0])-1 === $key && $key !== 0) {
                $number.= localeconv()['decimal_point'];
            }
            $number .= $part;
        }

        return array($number, $unit);
    }
}
