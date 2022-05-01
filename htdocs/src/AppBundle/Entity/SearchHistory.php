<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchHistory
 *
 * @ORM\Table(name="search_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SearchHistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SearchHistory
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="css_class", type="string", length=255, nullable=true)
     */
    private $css_class;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="search_histories")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;


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
     * Set created_at
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        if (!$this->created_at) {
            $this->created_at = new \DateTime();
        }
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }


    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return SearchHistory
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
     * Set url.
     *
     * @param string|null $url
     *
     * @return SearchHistory
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set cssClass.
     *
     * @param string|null $cssClass
     *
     * @return SearchHistory
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
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return SearchHistory
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


    /**
     * To array.
     * 
     * @return array
     */
    public function toArray(){
        return [
            'id' => $this->getId(),
            'user_id' => (($this->getUser()) ? $this->getUser()->getId() : null),
            'title' => $this->getTitle(),
            'url' => $this->getUrl(),
            'css_class' => $this->getCssClass(),
        ];
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getTitle()) ? 'Search '.$this->getTitle() : 'New search';
    }
}
