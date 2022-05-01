<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortfolioProfile
 *
 * @ORM\Table(name="portfolio_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PortfolioProfileRepository")
 */
class PortfolioProfile
{
    const PORTFOLIO_PROFILE_ID_NEW_BUSINESS_ACCELERATION = 6;
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
     * @return PortfolioProfile
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
     * @return PortfolioProfile
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
     * @return PortfolioProfile
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
     * Get excel status.
     * 
     * @return string
     */
    public function getExcelStatus(){
        $portfolio_profile_id = $this->getId();
        if ($portfolio_profile_id === 2) {
            return 'BB';
        } elseif ($portfolio_profile_id === 3) {
            return 'TC';
        } elseif ($portfolio_profile_id === 4) {
            return 'NC';
        } elseif ($portfolio_profile_id === 5) {
            return 'HI';
        }
        return '';
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New portfolio profile';
    }
}
