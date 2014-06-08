<?php

namespace Design311\WebsiteBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    private $temp;

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
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    protected $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="googleplus", type="string", length=255, nullable=true)
     */
    protected $googleplus;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512, nullable=true)
     */
    protected $description;
    
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
     * @ORM\OneToMany(targetEntity="Dinner", mappedBy="user")
     */
    protected $dinners;

    /**
     * @ORM\OneToMany(targetEntity="DinnerParticipants", mappedBy="user" ,cascade={"persist"})
    **/
    private $dinnersParticipated;

    /**
     * @ORM\OneToMany(targetEntity="DinnerParticipantRequest", mappedBy="user" ,cascade={"persist"})
    **/
    private $dinnersRequested;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar = 'default.png';

    /**
     * @Assert\File(maxSize="1M")
     */
    private $file;

    public function __sleep(){
        return array('id', 'username', 'displayName', 'email', 'avatar');
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image filename
        if (isset($this->avatar)) {
            // store the old name to delete after the update
            $this->temp = $this->avatar;
            $this->avatar = null;
        } else {
            $this->avatar = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    private function getPath(){
        return $this->avatar;
    }

    public function getAbsolutePath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadRootDir().'/'.$this->getPath();
    }

    public function getWebPath()
    {
        return null === $this->avatar ? null : $this->getUploadDir().'/'.$this->getPath();
    }

    private function getUploadRootDir()
    {
        return __DIR__.'/../../../../public_html/'.$this->getUploadDir();
    }

    private function getUploadDir()
    {
        return 'uploads/avatars';
    }

    private function getFileParts(){
        $filename = iconv("UTF-8", "ISO-8859-1//IGNORE", $this->getFile()->getClientOriginalName());
        $filepieces = explode( '.', $filename);
        $extension = array_pop($filepieces);
        $filename = implode('-', $filepieces);

        $filename = preg_replace("/[^a-z0-9\s\-]/i", "", $filename); // Remove special characters
        $filename = preg_replace("/\s\s+/", " ", $filename); // Replace multiple spaces with one space
        $filename = trim($filename); // Remove trailing spaces
        $filename = preg_replace("/\s/", "-", $filename); // Replace all spaces with hyphens
        $filename = preg_replace("/\-\-+/", "-", $filename); // Replace multiple hyphens with one hyphen
        $filename = preg_replace("/^\-|\-$/", "", $filename); // Remove leading and trailing hyphens
        $filename = strtolower($filename);

        return array($filename, $extension);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $fileparts = $this->getFileParts();
            $this->avatar = time() .'-'. $this->getUsername() .'.'. $fileparts[1];
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if (isset($this->temp) && $this->temp != 'default.png') {
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }

        $this->getFile()->move($this->getUploadRootDir(), $this->getPath());
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

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

    /**
     * Add dinners
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinners
     * @return User
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
     * Add dinnersParticipated
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinnersParticipated
     * @return User
     */
    public function addDinnersParticipated(\Design311\WebsiteBundle\Entity\Dinner $dinnersParticipated)
    {
        $this->dinnersParticipated[] = $dinnersParticipated;

        return $this;
    }

    /**
     * Remove dinnersParticipated
     *
     * @param \Design311\WebsiteBundle\Entity\Dinner $dinnersParticipated
     */
    public function removeDinnersParticipated(\Design311\WebsiteBundle\Entity\Dinner $dinnersParticipated)
    {
        $this->dinnersParticipated->removeElement($dinnersParticipated);
    }

    /**
     * Get dinnersParticipated
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDinnersParticipated()
    {
        return $this->dinnersParticipated;
    }

    /**
     * Add dinnersRequested
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerParticipantRequest $dinnersRequested
     * @return User
     */
    public function addDinnersRequested(\Design311\WebsiteBundle\Entity\DinnerParticipantRequest $dinnersRequested)
    {
        $this->dinnersRequested[] = $dinnersRequested;

        return $this;
    }

    /**
     * Remove dinnersRequested
     *
     * @param \Design311\WebsiteBundle\Entity\DinnerParticipantRequest $dinnersRequested
     */
    public function removeDinnersRequested(\Design311\WebsiteBundle\Entity\DinnerParticipantRequest $dinnersRequested)
    {
        $this->dinnersRequested->removeElement($dinnersRequested);
    }

    /**
     * Get dinnersRequested
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDinnersRequested()
    {
        return $this->dinnersRequested;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return User
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return User
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set googleplus
     *
     * @param string $googleplus
     * @return User
     */
    public function setGoogleplus($googleplus)
    {
        $this->googleplus = $googleplus;

        return $this;
    }

    /**
     * Get googleplus
     *
     * @return string 
     */
    public function getGoogleplus()
    {
        return $this->googleplus;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->getWebPath();
    }
}
