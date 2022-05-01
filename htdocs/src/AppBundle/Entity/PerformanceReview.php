<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PerformanceReview
 *
 * @ORM\Table(name="performance_review")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PerformanceReviewRepository")
 */
class PerformanceReview
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
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="performance_reviews")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="current_key", type="string", length=255, nullable=true)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="current_value", type="text", length=65535, nullable=true)
     */
    private $value;

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
     * Set key.
     *
     * @param string|null $key
     *
     * @return PerformanceReview
     */
    public function setKey($key = null)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value.
     *
     * @param string|null $value
     *
     * @return PerformanceReview
     */
    public function setValue($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation|null $innovation
     *
     * @return PerformanceReview
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
     * Set oldId.
     *
     * @param int $oldId
     *
     * @return PerformanceReview
     */
    public function setOldId($oldId)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get oldId.
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->old_id;
    }
}
