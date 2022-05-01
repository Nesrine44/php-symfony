<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Entity
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
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $old_id;
    

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="EntityGroup")
     * @ORM\JoinColumn(name="entity_group_id", referencedColumnName="id")
     */
    private $group;

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
    public function setCreatedAt() {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt() {
        $this->updated_at = new \DateTime();
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Entity
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
     * Set oldId.
     *
     * @param int|null $oldId
     *
     * @return Entity
     */
    public function setOldId($oldId = null)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get oldId.
     *
     * @return int|null
     */
    public function getOldId()
    {
        return $this->old_id;
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
            'group_id' => $this->getGroupId()
        );
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New entity';
    }

    /**
     * Set group.
     *
     * @param \AppBundle\Entity\EntityGroup|null $group
     *
     * @return Entity
     */
    public function setGroup(\AppBundle\Entity\EntityGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group.
     *
     * @return \AppBundle\Entity\EntityGroup|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get group id.
     *
     * @return int
     */
    public function getGroupId(){
        return ($this->getGroup()) ? $this->getGroup()->getId() : -1;
    }
}
