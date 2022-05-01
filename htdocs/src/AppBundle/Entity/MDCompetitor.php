<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MDCompetitor
 *
 * @ORM\Table(name="md_competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MDCompetitorRepository")
 */
class MDCompetitor
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=255, nullable=false)
     */
    private $product_name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('', 'Aniseed', 'Bitters', 'Brandy non Cognac', 'Cognac', 'Gin', 'Intl Champagne', 'Liqueurs', 'Non-Scotch Whisky', 'RTD/RTS', 'Rum', 'Scotch Whisky', 'Tequila', 'Vodka', 'Wine', 'Other')")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=false)
     */
    private $brand;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="md_competitor_tag")
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="periode", type="string", length=255, nullable=false)
     */
    private $periode;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id.
     *
     * @return int
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
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return MDCompetitor
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set productName.
     *
     * @param string $productName
     *
     * @return MDCompetitor
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get productName.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set category.
     *
     * @param string $category
     *
     * @return MDCompetitor
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set brand.
     *
     * @param string $brand
     *
     * @return MDCompetitor
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set periode.
     *
     * @param string $periode
     *
     * @return MDCompetitor
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode.
     *
     * @return string
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Add tag.
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return MDCompetitor
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @return Innovation
     */
    public function removeAllTags()
    {
        $this->tags = [];
        return $this;
    }

    /**
     * Remove tag.
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTag(\AppBundle\Entity\Tag $tag)
    {
        return $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get tags to a serializable array form.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTagsArray()
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            array_push($tags, $tag->toSelect2Array());
        }
        return $tags;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return MDCompetitor
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'picture' => ($this->getPicture()) ? $this->getPicture()->toArray() : null,
            'product_name' => $this->getProductName(),
            'category' => $this->getCategory(),
            'brand' => $this->getBrand(),
            'tags' => $this->getTagsArray(),
            'periode' => $this->getPeriode(),
            'user' => $this->getUser()->toArray(),
        );
    }

    /**
     * Set picture.
     *
     * @param \AppBundle\Entity\Picture|null $picture
     *
     * @return MDCompetitor
     */
    public function setPicture(\AppBundle\Entity\Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
