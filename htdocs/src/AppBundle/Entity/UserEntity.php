<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEntity
 *
 * @ORM\Table(name="user_entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserEntityRepository")
 */
class UserEntity
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="pr_title", type="string", length=255, nullable=true)
     */
    private $pr_title;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * Set prTitle.
     *
     * @param string|null $prTitle
     *
     * @return UserEntity
     */
    public function setPrTitle($prTitle = null)
    {
        $this->pr_title = $prTitle;

        return $this;
    }

    /**
     * Get prTitle.
     *
     * @return string|null
     */
    public function getPrTitle()
    {
        return $this->pr_title;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return UserEntity
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
     * Get proper title.
     *
     * @return string|null
     */
    public function getProperTitle()
    {
        return ($this->getTitle()) ? $this->getTitle() : $this->getPrTitle();
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getProperTitle() : 'New user entity';
    }
}
