<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Classification
 *
 * @ORM\Table(name="classification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClassificationRepository")
 */
class Classification
{
    const CLASSIFICATION_ID_PRODUCT = 2;
    const CLASSIFICATION_ID_SERVICE = 3;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;


    /**
     * @var string
     *
     * @ORM\Column(name="css_class", type="string", length=255, nullable=true)
     */
    private $css_class;
    
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
     * Set id.
     *
     * @param int $id
     *
     * @return Classification
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Classification
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set cssClass.
     *
     * @param string|null $cssClass
     *
     * @return Classification
     */
    public function setCssClass($cssClass = null)
    {
        $this->css_class = $cssClass;

        return $this;
    }

    /**
     * Get cssClass.
     *
     * @return string|null
     */
    public function getCssClass()
    {
        return $this->css_class;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'css_class' => $this->getCssClass()
        );
    }

    /**
     * Get url libelle.
     *
     * @return string
     */
    public function getUrlLibelle()
    {
        if($this->getId() === self::CLASSIFICATION_ID_SERVICE){
            return "service";
        }
        return "product";
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New classification';
    }
}
