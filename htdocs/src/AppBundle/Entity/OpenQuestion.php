<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpenQuestion
 *
 * @ORM\Table(name="open_question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OpenQuestionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OpenQuestion
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    /**
     * @ORM\OneToOne(targetEntity="Innovation", inversedBy="open_question")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id", nullable=false)
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;


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
     * Set message.
     *
     * @param string|null $message
     *
     * @return OpenQuestion
     */
    public function setMessage($message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set contact.
     *
     * @param \AppBundle\Entity\User|null $contact
     *
     * @return OpenQuestion
     */
    public function setContact(\AppBundle\Entity\User $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation $innovation
     *
     * @return OpenQuestion
     */
    public function setInnovation(\AppBundle\Entity\Innovation $innovation)
    {
        $this->innovation = $innovation;

        return $this;
    }

    /**
     * Get innovation.
     *
     * @return \AppBundle\Entity\Innovation
     */
    public function getInnovation()
    {
        return $this->innovation;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(){
        $ret = array();
        $ret['id'] = $this->getId();
        $ret['message'] = ($this->getMessage()) ? $this->getMessage() : null;
        $ret['updated_at'] = ($this->getUpdatedAt()) ? $this->getUpdatedAt()->getTimestamp() : null;
        $ret['contact'] = array(
            'uid' => (($this->getContact()) ? $this->getContact()->getId() : null),
            'username' => (($this->getContact()) ? $this->getContact()->getProperUsername() : ''),
            'email' => (($this->getContact()) ? $this->getContact()->getEmail() : ''),
            'picture' => (($this->getContact()) ? $this->getContact()->getPictureUrl() : '')
        );
        return $ret;
    }
}
