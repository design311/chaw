<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DinnerParticipants
 *
 * @ORM\Table(name="dinnerparticipants")
 * @ORM\Entity
 */
class DinnerParticipants
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
     * @ORM\ManyToOne(targetEntity="Dinner", inversedBy="participants")
     **/
    private $dinner;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="dinnersParticipated", cascade={"persist"})
     **/
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accepted", type="boolean")
     */
    private $accepted = false; //default value

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
     * Set accepted
     *
     * @param boolean $accepted
     * @return DinnerParticipants
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set dinner
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinner
     * @return DinnerParticipants
     */
    public function setDinner(\Design311\WebsiteBundle\Entity\Dinner $dinner = null)
    {
        $this->dinner = $dinner;

        return $this;
    }

    /**
     * Get dinner
     *
     * @return \Design311\WebsiteBundle\Entity\Dinner 
     */
    public function getDinner()
    {
        return $this->dinner;
    }

    /**
     * Set user
     *
     * @param \Design311\WebsiteBundle\Entity\User $user
     * @return DinnerParticipants
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
}
