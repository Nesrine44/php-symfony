<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdditionalPicture
 *
 * @ORM\Table(name="additional_picture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdditionalPictureRepository")
 */
class AdditionalPicture
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="additional_pictures")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Picture", cascade={"remove"})
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     */
    protected $picture;

    /**
     * @var int
     *
     * @ORM\Column(name="picture_order", type="integer", nullable=true)
     */
    private $order;

    /**
     * Set order.
     *
     * @param int $order
     *
     * @return AdditionalPicture
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation $innovation
     *
     * @return AdditionalPicture
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
     * Set picture.
     *
     * @param \AppBundle\Entity\Picture $picture
     *
     * @return AdditionalPicture
     */
    public function setPicture(\AppBundle\Entity\Picture $picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return \AppBundle\Entity\Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
