<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecipeIngredient
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RecipeIngredient
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
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="RecipeIngredients")
     **/
    private $recipes;

    /**
     * @ORM\ManyToOne(targetEntity="Ingredient", inversedBy="RecipeIngredient", cascade={"persist"})
     **/
    private $ingredient;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255, nullable=true)
     */
    private $amount;

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
     * Set amount
     *
     * @param string $amount
     * @return RecipeIngredient
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set recipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipes
     * @return RecipeIngredient
     */
    public function setRecipes(\Design311\WebsiteBundle\Entity\Recipe $recipes = null)
    {
        $this->recipes = $recipes;

        return $this;
    }

    /**
     * Get recipes
     *
     * @return \Design311\WebsiteBundle\Entity\Recipe 
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * Set ingredient
     *
     * @param \Design311\WebsiteBundle\Entity\Ingredient $ingredient
     * @return RecipeIngredient
     */
    public function setIngredient(\Design311\WebsiteBundle\Entity\Ingredient $ingredient = null)
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    /**
     * Get ingredient
     *
     * @return \Design311\WebsiteBundle\Entity\Ingredient 
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }
}
