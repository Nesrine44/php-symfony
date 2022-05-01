<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MDRecommandation
 *
 * @ORM\Table(name="md_recommandation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MDRecommandationRepository")
 */
class MDRecommandation
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
     * @ORM\Column(name="recommandation", type="text", length=65535, nullable=false)
     */
    private $recommandation;

    /**
     * @var string
     *
     * @ORM\Column(name="feedback", type="text", length=65535, nullable=false)
     */
    private $feedback;

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
     * Set recommandation.
     *
     * @param string $recommandation
     *
     * @return MDRecommandation
     */
    public function setRecommandation($recommandation)
    {
        $this->recommandation = $recommandation;

        return $this;
    }

    /**
     * Get recommandation.
     *
     * @return string
     */
    public function getRecommandation()
    {
        return $this->recommandation;
    }

    /**
     * Set feedback.
     *
     * @param string $feedback
     *
     * @return MDRecommandation
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback.
     *
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set periode.
     *
     * @param string $periode
     *
     * @return MDRecommandation
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
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return MDRecommandation
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
            'recommandation' => $this->getRecommandation(),
            'feedback' => $this->getFeedback(),
            'periode' => $this->getPeriode(),
            'user' => $this->getUser()->toArray(),
        );
    }
}
