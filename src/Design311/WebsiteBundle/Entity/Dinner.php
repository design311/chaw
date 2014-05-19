<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dinner
 *
 * @ORM\Table(name="dinner")
 * @ORM\Entity
 */
class Dinner
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
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="permalink", type="string", length=255)
     */
    protected $permalink;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="menu", type="text")
     */
    private $menu;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="max_invitees", type="smallint")
     */
    private $maxInvitees;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="smallint")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Address",cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="DinnerMeta", inversedBy="dinners")
     * @ORM\JoinTable(name="dinner_has_meta")
     */
    private $meta;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="dinners")
    **/
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="DinnerParticipants", mappedBy="dinner" ,cascade={"persist"})
    **/
    private $participants;


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
        $this->meta = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Dinner
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $permalink = $title;
        $permalink = preg_replace("/[^a-z0-9\s\-]/i", "", $permalink); // Remove special characters
        $permalink = preg_replace("/\s\s+/", " ", $permalink); // Replace multiple spaces with one space
        $permalink = trim($permalink); // Remove trailing spaces
        $permalink = preg_replace("/\s/", "-", $permalink); // Replace all spaces with hyphens
        $permalink = preg_replace("/\-\-+/", "-", $permalink); // Replace multiple hyphens with one hyphen
        $permalink = preg_replace("/^\-|\-$/", "", $permalink); // Remove leading and trailing hyphens
        $permalink = strtolower($permalink);

        $this->setPermalink($permalink);

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
     * Set date
     *
     * @param \DateTime $date
     * @return Dinner
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set menu
     *
     * @param string $menu
     * @return Dinner
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set maxInvitees
     *
     * @param integer $maxInvitees
     * @return Dinner
     */
    public function setMaxInvitees($maxInvitees)
    {
        $this->maxInvitees = $maxInvitees;

        return $this;
    }

    /**
     * Get maxInvitees
     *
     * @return integer 
     */
    public function getMaxInvitees()
    {
        return $this->maxInvitees;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Dinner
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set address
     *
     * @param \Design311\WebsiteBundle\Entity\Address $address
     * @return Dinner
     */
    public function setAddress(\Design311\WebsiteBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Design311\WebsiteBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add meta
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerMeta $meta
     * @return Dinner
     */
    public function addMetum(\Design311\WebsiteBundle\Entity\DinnerMeta $meta)
    {
        $this->meta[] = $meta;

        return $this;
    }

    /**
     * Remove meta
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerMeta $meta
     */
    public function removeMetum(\Design311\WebsiteBundle\Entity\DinnerMeta $meta)
    {
        $this->meta->removeElement($meta);
    }

    /**
     * Get meta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set user
     *
     * @param \Design311\WebsiteBundle\Entity\User $user
     * @return Dinner
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
     * Add participants
     *
     * @param \Design311\WebsiteBundle\Entity\User $participants
     * @return Dinner
     */
    public function addParticipant(\Design311\WebsiteBundle\Entity\User $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \Design311\WebsiteBundle\Entity\User $participants
     */
    public function removeParticipant(\Design311\WebsiteBundle\Entity\User $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set permalink
     *
     * @param string $permalink
     * @return Dinner
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * Get permalink
     *
     * @return string 
     */
    public function getPermalink()
    {
        return $this->permalink;
    }
}
