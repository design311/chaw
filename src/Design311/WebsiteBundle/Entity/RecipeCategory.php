<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecipeCategory
 *
 * @ORM\Table(name="recipecategory")
 * @ORM\Entity
 */
class RecipeCategory
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
     * @var string
     *
     * @ORM\Column(name="plural", type="string", length=255)
     */
    private $plural;

    /**
     * @ORM\OneToMany(targetEntity="Recipe", mappedBy="category")
    **/
    private $recipe;


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
     * @return RecipeCategory
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
     * Constructor
     */
    public function __construct()
    {
        $this->recipe = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recipe
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipe
     * @return RecipeCategory
     */
    public function addRecipe(\Design311\WebsiteBundle\Entity\Recipe $recipe)
    {
        $this->recipe[] = $recipe;

        return $this;
    }

    /**
     * Remove recipe
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipe
     */
    public function removeRecipe(\Design311\WebsiteBundle\Entity\Recipe $recipe)
    {
        $this->recipe->removeElement($recipe);
    }

    /**
     * Get recipe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    public function __tostring()
    {
        return $this->name;
    }

    /**
     * Set plural
     *
     * @param string $plural
     * @return RecipeCategory
     */
    public function setPlural($plural)
    {
        $this->plural = $plural;

        return $this;
    }

    /**
     * Get plural
     *
     * @return string 
     */
    public function getPlural()
    {
        return $this->plural;
    }
}
