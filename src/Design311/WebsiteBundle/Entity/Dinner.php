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
     * @var decimal
     *
     * @ORM\Column(name="price", type="decimal", precision=5, scale=2)
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
     * @ORM\ManyToOne(targetEntity="Diet")
     */
    private $diet;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="dinners")
    **/
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="DinnerParticipants", mappedBy="dinner" ,cascade={"persist"})
    **/
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity="DinnerParticipantRequest", mappedBy="dinner" ,cascade={"persist"})
    **/
    private $participantRequests;

    /**
     * @ORM\OneToMany(targetEntity="DinnerInvite", mappedBy="dinner" ,cascade={"persist"})
    **/
    private $invitees;


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

    /**
     * Add participantRequests
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerParticipantRequest $participantRequests
     * @return Dinner
     */
    public function addParticipantRequest(\Design311\WebsiteBundle\Entity\DinnerParticipantRequest $participantRequests)
    {
        $this->participantRequests[] = $participantRequests;

        return $this;
    }

    /**
     * Remove participantRequests
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerParticipantRequest $participantRequests
     */
    public function removeParticipantRequest(\Design311\WebsiteBundle\Entity\DinnerParticipantRequest $participantRequests)
    {
        $this->participantRequests->removeElement($participantRequests);
    }

    /**
     * Get participantRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipantRequests()
    {
        return $this->participantRequests;
    }

    /**
     * Add invitees
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerInvite $invitees
     * @return Dinner
     */
    public function addInvitee(\Design311\WebsiteBundle\Entity\DinnerInvite $invitees)
    {
        $this->invitees[] = $invitees;

        return $this;
    }

    /**
     * Remove invitees
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerInvite $invitees
     */
    public function removeInvitee(\Design311\WebsiteBundle\Entity\DinnerInvite $invitees)
    {
        $this->invitees->removeElement($invitees);
    }

    /**
     * Get invitees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitees()
    {
        return $this->invitees;
    }

    /**
     * Set diet
     *
     * @param \Design311\WebsiteBundle\Entity\Diet $diet
     * @return Dinner
     */
    public function setDiet(\Design311\WebsiteBundle\Entity\Diet $diet = null)
    {
        $this->diet = $diet;

        return $this;
    }

    /**
     * Get diet
     *
     * @return \Design311\WebsiteBundle\Entity\Diet 
     */
    public function getDiet()
    {
        return $this->diet;
    }

    /**
     * Set price
     *
     * @param string $price
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
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }
}
