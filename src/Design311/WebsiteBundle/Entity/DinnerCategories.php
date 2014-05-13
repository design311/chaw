<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DinnerCategories
 *
 * @ORM\Table(name="dinnercategories")
 * @ORM\Entity
 */
class DinnerCategories
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
     * @var boolean
     *
     * @ORM\Column(name="is_filter", type="boolean")
     */
    private $isFilter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_calculated", type="boolean")
     */
    private $isCalculated;

    /**
     * @ORM\OneToMany(targetEntity="DinnerMeta", mappedBy="category")
     **/
    private $children;


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
     * @return DinnerCategories
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
     * Set isFilter
     *
     * @param boolean $isFilter
     * @return DinnerCategories
     */
    public function setIsFilter($isFilter)
    {
        $this->isFilter = $isFilter;

        return $this;
    }

    /**
     * Get isFilter
     *
     * @return boolean 
     */
    public function getIsFilter()
    {
        return $this->isFilter;
    }

    /**
     * Set children
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerMeta $children
     * @return DinnerCategories
     */
    public function setChildren(\Design311\WebsiteBundle\Entity\DinnerMeta $children = null)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return \Design311\WebsiteBundle\Entity\DinnerMeta 
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add children
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerMeta $children
     * @return DinnerCategories
     */
    public function addChild(\Design311\WebsiteBundle\Entity\DinnerMeta $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerMeta $children
     */
    public function removeChild(\Design311\WebsiteBundle\Entity\DinnerMeta $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Set isCalculated
     *
     * @param boolean $isCalculated
     * @return DinnerCategories
     */
    public function setIsCalculated($isCalculated)
    {
        $this->isCalculated = $isCalculated;

        return $this;
    }

    /**
     * Get isCalculated
     *
     * @return boolean 
     */
    public function getIsCalculated()
    {
        return $this->isCalculated;
    }
}
