<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Recipe
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="recipe", type="text")
     */
    private $recipe;

    /**
     * @var smallint
     *
     * @ORM\Column(name="cooking_time", type="smallint")
     */
    private $cookingTime;

    /**
     * @var smallint
     *
     * @ORM\Column(name="ready_time", type="smallint")
     */
    private $readyTime;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="recipe", cascade={"persist"})
    **/
    private $photos;

    /**
     * @ORM\ManyToOne(targetEntity="RecipeCategory", inversedBy="recipe")
    **/
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="recipes" ,cascade={"persist"})
    **/
    private $recipeIngredients;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="recipes")
    **/
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="likedrecipes")
     */
    protected $likedby;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Recipe
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set recipe
     *
     * @param string $recipe
     * @return Recipe
     */
    public function setRecipe($recipe)
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * Get recipe
     *
     * @return string 
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set cookingTime
     *
     * @param integer $cookingTime
     * @return Recipe
     */
    public function setCookingTime($cookingTime)
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    /**
     * Get cookingTime
     *
     * @return integer 
     */
    public function getCookingTime()
    {
        return $this->cookingTime;
    }

    /**
     * Set readyTime
     *
     * @param integer $readyTime
     * @return Recipe
     */
    public function setReadyTime($readyTime)
    {
        $this->readyTime = $readyTime;

        return $this;
    }

    /**
     * Get readyTime
     *
     * @return integer 
     */
    public function getReadyTime()
    {
        return $this->readyTime;
    }

    /**
     * Add photos
     *
     * @param \Design311\WebsiteBundle\Entity\Photo $photos
     * @return Recipe
     */
    public function addPhoto(\Design311\WebsiteBundle\Entity\Photo $photos)
    {
        $photos->setRecipe($this);
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \Design311\WebsiteBundle\Entity\Photo $photos
     */
    public function removePhoto(\Design311\WebsiteBundle\Entity\Photo $photos)
    {
        $this->photos->removeElement($photos);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add recipeIngredients
     *
     * @param \Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients
     * @return Recipe
     */
    public function addRecipeIngredient(\Design311\WebsiteBundle\Entity\RecipeIngredient $recipeIngredients)
    {
        $recipeIngredients->setRecipes($this);
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
     * Get recipeIngredients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipeIngredients()
    {
        return $this->recipeIngredients;
    }

    /**
     * Set category
     *
     * @param \Design311\WebsiteBundle\Entity\RecipeCategory $category
     * @return Recipe
     */
    public function setCategory(\Design311\WebsiteBundle\Entity\RecipeCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Design311\WebsiteBundle\Entity\RecipeCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Design311\WebsiteBundle\Entity\User $user
     * @return Recipe
     */
    public function setUser(\Design311\WebsiteBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Design311\WebsiteBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add likedby
     *
     * @param \Design311\WebsiteBundle\Entity\User $likedby
     * @return Recipe
     */
    public function addLikedby(\Design311\WebsiteBundle\Entity\User $likedby)
    {
        $this->likedby[] = $likedby;

        return $this;
    }

    /**
     * Remove likedby
     *
     * @param \Design311\WebsiteBundle\Entity\User $likedby
     */
    public function removeLikedby(\Design311\WebsiteBundle\Entity\User $likedby)
    {
        $this->likedby->removeElement($likedby);
    }

    /**
     * Get likedby
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikedby()
    {
        return $this->likedby;
    }
}
