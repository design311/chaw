<?php

namespace Design311\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Photo
{

    private $temp;

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
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @Assert\Image(maxSize="4M")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image filename
        if (isset($this->filename)) {
            // store the old name to delete after the update
            $this->temp = $this->getPath();
            $this->filename = null;
        } else {
            $this->filename = 'initial';
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
     * @return Photo
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

    private function getPath(){
        return $this->getFilename().'-'.$this->id.'.'.$this->getExtension();
    }

    public function getAbsolutePath()
    {
        return null === $this->filename
            ? null
            : $this->getUploadRootDir().'/'.$this->getPath();
    }

    public function getWebPath()
    {
        return null === $this->filename ? null : $this->getUploadDir().'/'.$this->getPath();
    }

    private function getUploadRootDir()
    {
        return __DIR__.'/../../../../public_html/'.$this->getUploadDir();
    }

    private function getUploadDir()
    {
        return 'uploads';
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
            $this->filename = $fileparts[0];
            $this->extension = $fileparts[1];
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

        if (isset($this->temp)) {
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
     * Set filename
     *
     * @param string $filename
     * @return Photo
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set recipe
     *
     * @param \Design311\WebsiteBundle\Entity\Recipe $recipe
     * @return Photo
     */
    public function setRecipe(\Design311\WebsiteBundle\Entity\Recipe $recipe = null)
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * Get recipe
     *
     * @return \Design311\WebsiteBundle\Entity\Recipe 
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Photo
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Photo
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
