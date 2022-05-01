<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedbackRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Feedback
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
     * @ORM\ManyToOne(targetEntity="FeedbackInvitation", inversedBy="feedbacks")
     * @ORM\JoinColumn(name="invitation_id", referencedColumnName="id")
     */
    private $invitation;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=false)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="`role`", type="string", length=255, nullable=false)
     */
    private $role;


    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;


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
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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
     * Set message.
     *
     * @param string $message
     *
     * @return Feedback
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set role.
     *
     * @param string $role
     *
     * @return Feedback
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set invitation.
     *
     * @param FeedbackInvitation|null $invitation
     *
     * @return Feedback
     */
    public function setInvitation(FeedbackInvitation $invitation = null)
    {
        $this->invitation = $invitation;

        return $this;
    }

    /**
     * Get invitation.
     *
     * @return FeedbackInvitation|null
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Set rating.
     *
     * @param float $rating
     *
     * @return Feedback
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'invitation' => $this->getInvitation()->toArray(),
            'message' => $this->getMessage(),
            'rating' => $this->getRating(),
            'role' => $this->getRole(),
            'created_at' => $this->getRelativeCreatedAt()
        );
    }

    /**
     * Get relative created_at.
     * 
     * @return string
     */
    public function getRelativeCreatedAt(){
        if(!$this->getCreatedAt()){
            return '';
        }
        return $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format('M j, Y');
    }
}
