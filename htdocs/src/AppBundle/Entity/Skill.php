<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Skill
 *
 * @ORM\Table(name="skill", indexes={@ORM\Index(name="tag_title_idx", columns={"title"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkillRepository")
 */
class Skill
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
     * @var boolean
     *
     * @ORM\Column(name="is_main_skill", type="boolean")
     */
    private $is_main_skill = false;


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
     * Set title.
     *
     * @param string|null $title
     *
     * @return Skill
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
     * Set isMainSkill.
     *
     * @param bool $isMainSkill
     *
     * @return Skill
     */
    public function setIsMainSkill($isMainSkill)
    {
        $this->is_main_skill = $isMainSkill;

        return $this;
    }

    /**
     * Get isMainSkill.
     *
     * @return bool
     */
    public function getIsMainSkill()
    {
        return $this->is_main_skill;
    }


    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New skill';
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
            'is_main_skill' => $this->getIsMainSkill()
        );
    }

    /**
     * To select2 array.
     *
     * @return array
     */
    public function toSelect2Array(){
        return [
            "id" => $this->getId(),
            "text" => $this->getTitle(),
        ];
    }
}
