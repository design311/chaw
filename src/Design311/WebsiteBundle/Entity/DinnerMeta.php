<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DinnerMeta
 *
 * @ORM\Table(name="dinnermeta")
 * @ORM\Entity
 */
class DinnerMeta
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
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity="Dinner", mappedBy="meta")
     */
    private $dinners;

    /**
     * @ORM\ManyToOne(targetEntity="DinnerCategories", inversedBy="children")
     **/
    private $category;


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
     * Constructor
     */
    public function __construct()
    {
        $this->dinners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set category
     *
     * @param string $category
     * @return DinnerMeta
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return DinnerMeta
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add dinners
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinners
     * @return DinnerMeta
     */
    public function addDinner(\Design311\WebsiteBundle\Entity\Dinner $dinners)
    {
        $this->dinners[] = $dinners;

        return $this;
    }

    /**
     * Remove dinners
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinners
     */
    public function removeDinner(\Design311\WebsiteBundle\Entity\Dinner $dinners)
    {
        $this->dinners->removeElement($dinners);
    }

    /**
     * Get dinners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDinners()
    {
        return $this->dinners;
    }

    /**
     * Add categories
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerCategories $categories
     * @return DinnerMeta
     */
    public function addCategory(\Design311\WebsiteBundle\Entity\DinnerCategories $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerCategories $categories
     */
    public function removeCategory(\Design311\WebsiteBundle\Entity\DinnerCategories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
