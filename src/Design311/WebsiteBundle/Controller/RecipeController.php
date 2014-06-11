<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Form\Type\SearchRecipeType;
use Design311\WebsiteBundle\Entity\Recipe;


class RecipeController extends BaseController
{
    private function searchRecipeForm(){
        $form = $this->createForm(new SearchRecipeType(), array(), array(
            'action' => $this->generateUrl('design311website_recepten_zoeken'),
        ));

        return $form;
    }

    public function indexAction()
    {
        $recipes = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findAll();
        $newestrecipes = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findBy(array(), array('id' => 'DESC'), 5);
        $categories = $this->getDoctrine()->getRepository('Design311WebsiteBundle:RecipeCategory')->findAll();

        $form = $this->searchRecipeForm();

        return $this->render(
            'Design311WebsiteBundle:Recipe:index.html.twig',
            array(
                'recipes' => $recipes,
                'form' => $form->createView(),
                'newestrecipes' => $newestrecipes,
                'categories' => $categories,
                )
        );
    }

    public function categoryAction($category)
    {
        //TODO pagination
        $category = $this->getDoctrine()->getRepository('Design311WebsiteBundle:RecipeCategory')->findOneByPlural(ucfirst($category));
        if (!$category) {
            throw $this->createNotFoundException('Categorie niet gevonden');
        }

        $recipes = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findBy(array('category' => $category->getId()), array('id' => 'DESC'));

        $form = $this->searchRecipeForm();

        return $this->render(
            'Design311WebsiteBundle:Recipe:category.html.twig',
            array(
                'recipes' => $recipes,
                'form' => $form->createView(),
                'category' => $category,
                )
        );
    }

    public function detailAction($category, $permalink)
    {
        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);
        if (!$recipe) {
            throw $this->createNotFoundException('Recept niet gevonden');
        }

        $form = $this->searchRecipeForm();

        return $this->render(
            'Design311WebsiteBundle:Recipe:detail.html.twig',
            array(
                'recipe' => $recipe,
                'form' => $form->createView(),
                )
        );
    }

    public function searchInfoAction()
    {
        $form = $this->searchRecipeForm();

        return $this->render(
            'Design311WebsiteBundle:Recipe:searchInfo.html.twig',
            array(
                'form' => $form->createView(),
                )
        );
    }

    public function searchAction(Request $request)
    {
        $form = $this->searchRecipeForm();
        $form->handleRequest($request);

        //TODO form validation ?
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $searchData = $form->getData();

            $qb = $em->createQueryBuilder();
            $qb = $qb
                ->from('Design311WebsiteBundle:Recipe', 'r')
                ->select('r');

            if (count($searchData['ingredients']) > 0) {

                $ingredientIds = array();

                foreach ($searchData['ingredients'] as $ingredient) {
                    $ingredientIds[] = $ingredient->getId();
                }

                $qb = $qb
                    ->andWhere($qb->expr()->in('ri.ingredient', $ingredientIds))
                    ->leftJoin('Design311WebsiteBundle:RecipeIngredient', 'ri', 'WITH', 'r.id = ri.recipes')
                    ->groupBy('r.id')
                    ->having('count(r.id) = ' . count($ingredientIds));
            }

            if ($searchData['category'] != null ) {
                $qb->andWhere($qb->expr()->eq('r.category', $searchData['category']->getId()));
            }

            if ($searchData['diet'] != null ) {
                $qb->andWhere($qb->expr()->eq('r.diet', $searchData['diet']->getId()));
            }

            $recipes = $qb->getQuery()->execute();

            $form = $this->searchRecipeForm();

            return $this->render(
                'Design311WebsiteBundle:Recipe:search.html.twig',
                array(
                    'recipes' => $recipes,
                    'form' => $form->createView(),
                    )
            );
        }

    }

    public function userAction($username)
    {
        $form = $this->searchRecipeForm();
        $user = $this->getDoctrine()->getRepository('Design311WebsiteBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('Gebruiker niet gevonden');
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $qb
            ->from('Design311WebsiteBundle:Recipe', 'r')
            ->select('r')
            ->where($qb->expr()->eq('r.user', $user->getId()));

        $recipes = $qb->getQuery()->execute();

        return $this->render(
            'Design311WebsiteBundle:Recipe:user.html.twig',
            array(
                'recipes' => $recipes,
                'user' => $user,
                'form' => $form->createView(),
                )
        );
    }

    public function addAction(Request $request)
    {
        $form = $this->createForm(new RecipeType(), new Recipe(), array(
            'action' => $this->generateUrl('design311website_recepten_add')
            ));
        $form->handleRequest($request);
    
        $currentIngredients = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Ingredient')->findAll();

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $recipe = $form->getData();

            if ($recipe->getRecipeIngredients()) {
                foreach ($recipe->getRecipeIngredients() as $key => $recipeIngredient) {
                    foreach ($currentIngredients as $currentIngredient) {
                        if ($recipeIngredient->getIngredient()->getName() == $currentIngredient->getName()) {
                            $ingredient = $recipeIngredient->getIngredient();
                            $ingredient = $currentIngredient;
                            $recipeIngredient->setIngredient($ingredient);
                            break;
                        }
                    }
                }
            }
            
            $recipe->setUser($this->getUser());
            $recipe->setPermalink($this->getPermalink($recipe->getTitle(), 'Recipe'));

            $em->persist($recipe);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_recepten_detail', array('category' => strtolower($recipe->getCategory()->getPlural()), 'permalink' => $recipe->getPermalink()) ));
        }

        $ingredients = [];
        foreach ($currentIngredients as $currentIngredient) {
            $ingredients[] = $currentIngredient->getName();
        }

        return $this->render(
            'Design311WebsiteBundle:Recipe:add.html.twig', array(
                'form' => $form->createView(),
                'ingredients' => $ingredients
            )
        );
    }

    public function editAction($permalink, Request $request)
    {
        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);
        if (!$recipe) {
            throw $this->createNotFoundException('Recept niet gevonden');
        }

        if ($this->getUser() != $recipe->getUser()) {
            $this->get('session')->getFlashBag()->add('error','Dit recept mag je niet bewerken');
            return $this->redirect($this->generateUrl('design311website_recepten_detail', array('category' => strtolower($recipe->getCategory()->getPlural()), 'permalink' => $recipe->getPermalink()) ));
        }

        $originalPhotos = new ArrayCollection();
        foreach ($recipe->getPhotos() as $photo) {
            $originalPhotos->add($photo);
        }
        $originalIngredients = new ArrayCollection();
        foreach ($recipe->getRecipeIngredients() as $ingredient) {
            $originalIngredients->add($ingredient);
        }


        $form = $this->createForm(new RecipeType(), $recipe, array(
            'action' => $this->generateUrl('design311website_recepten_edit', array('permalink' => $permalink))
            ));
        $form->handleRequest($request);
    
        $currentIngredients = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Ingredient')->findAll();

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $recipe = $form->getData();

            $photos = $recipe->getPhotos();
            $ingredients = $recipe->getRecipeIngredients();

            if ($recipe->getRecipeIngredients()) {
                foreach ($recipe->getRecipeIngredients() as $key => $recipeIngredient) {
                    foreach ($currentIngredients as $currentIngredient) {
                        if ($recipeIngredient->getIngredient()->getName() == $currentIngredient->getName()) {
                            $ingredient = $recipeIngredient->getIngredient();
                            $ingredient = $currentIngredient;
                            $recipeIngredient->setIngredient($ingredient);
                            break;
                        }
                    }
                }
            }

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

            return $this->redirect($this->generateUrl('design311website_recepten_detail', array('category' => strtolower($recipe->getCategory()->getPlural()), 'permalink' => $recipe->getPermalink()) ));
        }

        $ingredients = [];
        foreach ($currentIngredients as $currentIngredient) {
            $ingredients[] = $currentIngredient->getName();
        }

        return $this->render(
            'Design311WebsiteBundle:Recipe:add.html.twig',array(
                'form' => $form->createView(),
                'recipe' => $recipe,
                'ingredients' => $ingredients
            )
        );
    }

    public function shoppinglistAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $qb
            ->from('Design311WebsiteBundle:Recipe', 'r')
            ->select('r')
            ->leftJoin('r.shoppinglistFrom','s')
            ->where('s.id = :userId')
            ->setParameter("userId", $this->getUser()->getId())
            ->orderBy('r.category');

        $shoppinglist = $qb->getQuery()->execute();

        $ingredients = array();

        foreach ($shoppinglist as $recipe) {
            $recipeIngredients = $recipe->getRecipeIngredients();

            foreach ($recipeIngredients as $recipeIngredient) {
                $ingredient = $recipeIngredient->getIngredient()->getName();
                if ($recipeIngredient->getAmount() != null) {
                    $amount = $this->smartDivide($recipeIngredient->getAmount(), $recipe->getAantalPersonen() / $this->getUser()->getAantalPersonen());
                    if (array_key_exists($ingredient, $ingredients)) {
                        $ingredients[$ingredient] = $this->smartAdd(array($ingredients[$ingredient], $amount));
                    }
                    else{
                        $ingredients[$ingredient] = $this->smartAdd(array($amount));
                    }
                }
                else{
                    $ingredients[$ingredient] = null;
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
        else{
            $result = round($result,2);
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

        $decimalPoint = localeconv();
        $decimalPoint = $decimalPoint['decimal_point'];
        foreach ($numberparts[0] as $key => $part) {
            if (count($numberparts[0])-1 === $key && $key !== 0) {
                $number.= $decimalPoint;
            }
            $number .= $part;
        }
        return array($number, $unit);
    }
}
