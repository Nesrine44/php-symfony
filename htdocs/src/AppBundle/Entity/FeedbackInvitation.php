<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


/**
 * FeedbackInvitation
 *
 * @ORM\Table(name="feedback_invitation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedbackInvitationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class FeedbackInvitation
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_ANSWERED = 'ANSWERED';
    const STATUS_REMOVED = 'REMOVED';
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
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="feedback_invitations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Innovation")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id", nullable=false)
     */
    private $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('', 'PENDING', 'REMOVED', 'ANSWERED')")
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="invitation", cascade={"persist"})
     */
    protected $feedbacks;

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
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
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
     * Creates token from user id, sender id and innovation id.
     *
     * @param string $userid
     * @param string $senderid
     * @param string $innoid
     *
     * @return FeedbackInvitation
     */
    public function setToken($userid, $senderid, $innoid)
    {
        $this->token = md5($userid.'-'.$senderid.'-'.$innoid.'-'.time());

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return FeedbackInvitation
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
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return FeedbackInvitation
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
     * Set sender.
     *
     * @param \AppBundle\Entity\User|null $sender
     *
     * @return FeedbackInvitation
     */
    public function setSender(\AppBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return FeedbackInvitation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function toArray() {
        /**
         * TODO : Un jour il faudra utiliser les classes de serialization, c'est fait pour...
         * Le seul souci c'est que la plupart des methodes des entités foirent et il faut les corriger ;
         * Le serializer passe par toutes les methodes des entités et montre du coup les erreurs dans certaines methodes
         * Details : https://symfony.com/doc/current/components/serializer.html
         */

        // $encoder = new JsonEncoder();
        // $normalizer = new ObjectNormalizer();
        // $normalizer->setCircularReferenceHandler(function ($object) {
        //     if (method_exists($object, 'getId')) {
        //         return $object->getId();
        //     }
        //     return null;
        // });
        // $serializer = new Serializer([$normalizer], [$encoder]);

        // return $serializer->serialize($this, $format);
        return array(
            'id' => $this->getId(),
            'token' => $this->getToken(),
            'user' => $this->getUser()->toArray(),
            'sender' => $this->getSender()->toArray(),
            'message' => $this->getMessage(),
            'token' => $this->getToken(),
            'status' => $this->getStatus(),
            'role' => $this->guessRole(),
        );
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation|null $innovation
     *
     * @return FeedbackInvitation
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
     * Add feedback.
     *
     * @param \AppBundle\Entity\Feedback $feedback
     *
     * @return FeedbackInvitation
     */
    public function addFeedback(\AppBundle\Entity\Feedback $feedback)
    {
        $this->feedbacks[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback.
     *
     * @param \AppBundle\Entity\Feedback $feedback
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFeedback(\AppBundle\Entity\Feedback $feedback)
    {
        return $this->feedbacks->removeElement($feedback);
    }

    /**
     * Get feedbacks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * Guess role.
     *
     * @return null
     */
    public function guessRole()
    {
        $feedbacks = $this->getFeedbacks();
        if (!$feedbacks) {
            return null;
        }
        foreach ($feedbacks as $feedback){
            if($feedback->getRole()){
                return $feedback->getRole();
            }
        }
        return null;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedbacks = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
