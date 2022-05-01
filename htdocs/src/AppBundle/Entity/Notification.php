<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Notification
{
    const STATUS_TO_SEND = 'to_send';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SENT = 'sent';

    const TYPE_MAIL = 'mail';
    const TYPE_OTHER = 'other';

    const ACTION_ON_PROMOTE_INNOVATION = 'promote_innovation';


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\ManyToOne(targetEntity="Innovation")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_status", type="string", nullable=true)
     */
    private $status = self::STATUS_TO_SEND;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_type", type="string", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_action", type="string", nullable=true)
     */
    private $action;

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
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set data.
     *
     * @param string|null $data
     *
     * @return Activity
     */
    public function setData($data = null)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     *
     * @return string|null
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * set Data Array.
     * @param array $data
     * @return $this
     */
    public function setDataArray($data = array())
    {
        if($data) {
            $this->data = json_encode($data);
        }
        return $this;
    }

    /**
     * get Data Array.
     *
     * @return array
     */
    public function getDataArray()
    {
        return json_decode($this->data, true);
    }


    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation|null $innovation
     *
     * @return Activity
     */
    public function setInnovation(\AppBundle\Entity\Innovation $innovation = null)
    {
        $this->innovation = $innovation;

        return $this;
    }

    /**
     * Get innovation.
     *
     * @return \AppBundle\Entity\Innovation|null
     */
    public function getInnovation()
    {
        return $this->innovation;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return Notification
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type.
     *
     * @param string|null $type
     *
     * @return Notification
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set action.
     *
     * @param string|null $action
     *
     * @return Notification
     */
    public function setAction($action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? 'Notification n°'.$this->getId() : 'New notification';
    }
}
