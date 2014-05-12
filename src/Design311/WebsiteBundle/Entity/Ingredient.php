<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ingredient
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ingredient
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="ingredient")
     **/
    private $recipeIngredient;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipeIngredients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __tostring(){
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Ingredient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add recipeIngredients
     *
     * @param \Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients
     * @return Ingredient
     */
    public function addRecipeIngredient(\Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients)
    {
        $this->recipeIngredients[] = $recipeIngredients;

        return $this;
    }

    /**
     * Remove recipeIngredients
     *
     * @param \Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients
     */
    public function removeRecipeIngredient(\Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients)
    {
        $this->recipeIngredients->removeElement($recipeIngredients);
    }

    /**
     * Get recipeIngredient
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipeIngredient()
    {
        return $this->recipeIngredient;
    }
}
