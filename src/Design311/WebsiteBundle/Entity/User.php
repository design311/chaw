<?php

namespace Design311\WebsiteBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", unique=true, length=255)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;
    
    /**
     * @var smallint
     *
     * @ORM\Column(name="aantal_personen", type="smallint")
     */
    private $aantalPersonen;

    /**
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Recipe", mappedBy="user")
     */
    protected $recipes;

    /**
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="likedBy")
     */
    protected $likedrecipes;

    /**
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="savedBy")
     */
    protected $savedrecipes;

    /**
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="shoppinglistFrom")
     */
    protected $shoppinglist;

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return User
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return User
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return User
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set address
     *
     * @param \Design311\WebsiteBundle\Entity\Address $address
     * @return User
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

    public function __sleep(){
        return array('id', 'username', 'displayName', 'password', 'email');
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipes
     * @return User
     */
    public function addRecipe(\Design311\WebsiteBundle\Entity\Recipe $recipes)
    {
        $this->recipes[] = $recipes;

        return $this;
    }

    /**
     * Remove recipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipes
     */
    public function removeRecipe(\Design311\WebsiteBundle\Entity\Recipe $recipes)
    {
        $this->recipes->removeElement($recipes);
    }

    /**
     * Get recipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * Add likedrecipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $likedrecipes
     * @return User
     */
    public function addLikedrecipe(\Design311\WebsiteBundle\Entity\Recipe $likedrecipes)
    {
        $this->likedrecipes[] = $likedrecipes;

        return $this;
    }

    /**
     * Remove likedrecipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $likedrecipes
     */
    public function removeLikedrecipe(\Design311\WebsiteBundle\Entity\Recipe $likedrecipes)
    {
        $this->likedrecipes->removeElement($likedrecipes);
    }

    /**
     * Get likedrecipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikedrecipes()
    {
        return $this->likedrecipes;
    }

    /**
     * Add savedrecipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $savedrecipes
     * @return User
     */
    public function addSavedrecipe(\Design311\WebsiteBundle\Entity\Recipe $savedrecipes)
    {
        $this->savedrecipes[] = $savedrecipes;

        return $this;
    }

    /**
     * Remove savedrecipes
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $savedrecipes
     */
    public function removeSavedrecipe(\Design311\WebsiteBundle\Entity\Recipe $savedrecipes)
    {
        $this->savedrecipes->removeElement($savedrecipes);
    }

    /**
     * Get savedrecipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSavedrecipes()
    {
        return $this->savedrecipes;
    }

    /**
     * Add shoppinglist
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $shoppinglist
     * @return User
     */
    public function addShoppinglist(\Design311\WebsiteBundle\Entity\Recipe $shoppinglist)
    {
        $this->shoppinglist[] = $shoppinglist;

        return $this;
    }

    /**
     * Remove shoppinglist
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $shoppinglist
     */
    public function removeShoppinglist(\Design311\WebsiteBundle\Entity\Recipe $shoppinglist)
    {
        $this->shoppinglist->removeElement($shoppinglist);
    }

    /**
     * Get shoppinglist
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShoppinglist()
    {
        return $this->shoppinglist;
    }

    /**
     * Set aantalPersonen
     *
     * @param integer $aantalPersonen
     * @return User
     */
    public function setAantalPersonen($aantalPersonen)
    {
        $this->aantalPersonen = $aantalPersonen;

        return $this;
    }

    /**
     * Get aantalPersonen
     *
     * @return integer 
     */
    public function getAantalPersonen()
    {
        return $this->aantalPersonen;
    }
}
