<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DinnerInvite
 *
 * @ORM\Table(name="dinnerinvite")
 * @ORM\Entity
 */
class DinnerInvite
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
     * @ORM\ManyToOne(targetEntity="Dinner", inversedBy="invitees")
     **/
    private $dinner;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

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
     * Set email
     *
     * @param string $email
     * @return DinnerInvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dinner
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinner
     * @return DinnerInvite
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
}
