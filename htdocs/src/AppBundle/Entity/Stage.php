<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 * @ORM\Table(name="stage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StageRepository")
 */
class Stage
{
    const STAGE_ID_DISCOVER = 1;
    const STAGE_ID_IDEATE = 2;
    const STAGE_ID_EXPERIMENT = 3;
    const STAGE_ID_INCUBATE = 4;
    const STAGE_ID_SCALE_UP = 5;
    const STAGE_ID_SUCCESS_STORY = 6;
    const STAGE_ID_DISCONTINUED = 7;
    const STAGE_ID_PERMANENT_RANGE = 8;
    
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
     * @var string
     *
     * @ORM\Column(name="css_class", type="string", length=255, nullable=true)
     */
    private $css_class;

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
     * Set id.
     * 
     * @param int $id
     *
     * @return Stage
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Stage
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
     * Set cssClass.
     *
     * @param string|null $cssClass
     *
     * @return Stage
     */
    public function setCssClass($cssClass = null)
    {
        $this->css_class = $cssClass;

        return $this;
    }

    /**
     * Get cssClass.
     *
     * @return string|null
     */
    public function getCssClass()
    {
        return $this->css_class;
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
            'css_class' => $this->getCssClass()
        );
    }

    /**
     * Get excel libelle.
     * 
     * @param string $post_current_stage
     * @return string
     */
    public function getExcelLibelle($post_current_stage = ''){
        switch ($this->getId()) {
            case self::STAGE_ID_DISCOVER :
                return '1 - discover' . $post_current_stage;
            case self::STAGE_ID_IDEATE:
                return '2 - ideate' . $post_current_stage;
            case self::STAGE_ID_EXPERIMENT:
                return '3 - experiment' . $post_current_stage;
            case self::STAGE_ID_INCUBATE:
                return '4 - incubate' . $post_current_stage;
            case self::STAGE_ID_SCALE_UP:
                return '5 - scale-up' . $post_current_stage;
            case self::STAGE_ID_DISCONTINUED:
                return '6 - discontinued' . $post_current_stage;
            case self::STAGE_ID_SUCCESS_STORY:
                return '8 - success story' . $post_current_stage;
            case self::STAGE_ID_PERMANENT_RANGE:
                return '7 - permanent range' . $post_current_stage;
            default:
                return '0 - empty' . $post_current_stage;
        }
    }

    /**
     * Get url libelle.
     *
     * @return string
     */
    public function getUrlLibelle(){
        switch ($this->getId()) {
            case self::STAGE_ID_DISCOVER :
                return 'discover';
            case self::STAGE_ID_IDEATE:
                return 'ideate';
            case self::STAGE_ID_EXPERIMENT:
                return 'experiment';
            case self::STAGE_ID_INCUBATE:
                return 'incubate';
            case self::STAGE_ID_SCALE_UP:
                return 'scale-up';
            case self::STAGE_ID_DISCONTINUED:
                return 'discontinued';
            case self::STAGE_ID_SUCCESS_STORY:
                return 'success-story';
            case self::STAGE_ID_PERMANENT_RANGE:
                return 'permanent-range';
            default:
                return '';
        }
    }


    /**
     * Return current stage title.
     *
     * @param $key
     * @return mixed|null
     */
    public static function returnCurrentStageTitle($key)
    {
        $key = intval($key);
        $ret = array(
            self::STAGE_ID_DISCOVER => 'Discover',
            self::STAGE_ID_IDEATE => 'Ideate',
            self::STAGE_ID_EXPERIMENT => 'Experiment',
            self::STAGE_ID_INCUBATE => 'Incubate',
            self::STAGE_ID_SCALE_UP => 'Scale up',
            self::STAGE_ID_SUCCESS_STORY => 'Success story',
            self::STAGE_ID_DISCONTINUED => 'Discontinued',
            self::STAGE_ID_PERMANENT_RANGE => 'Permanent range'
        );
        return (array_key_exists($key, $ret)) ? $ret[$key] : null;
    }

    /**
     * Get explore stage ids.
     * 
     * @return array
     */
    public static function getExploreStageIds()
    {
        return array(
            self::STAGE_ID_INCUBATE,
            self::STAGE_ID_SCALE_UP,
        );
    }

    /**
     * Get early stage ids.
     *
     * @return array
     */
    public static function getEarlyStageIds()
    {
        return array(
            self::STAGE_ID_DISCOVER,
            self::STAGE_ID_IDEATE,
            self::STAGE_ID_EXPERIMENT,
        );
    }

    /**
     * Get inner stage ids.
     *
     * @return array
     */
    public static function getInnerStages()
    {
        return array(
            self::STAGE_ID_DISCOVER,
            self::STAGE_ID_IDEATE,
            self::STAGE_ID_EXPERIMENT,
            self::STAGE_ID_INCUBATE,
            self::STAGE_ID_SCALE_UP
        );
    }

    /**
     * Get explore stage ids.
     *
     * @return array
     */
    public static function getExploreStages()
    {
        return array(
            self::STAGE_ID_INCUBATE,
            self::STAGE_ID_SCALE_UP
        );
    }

    /**
     * Get out of funnel stage ids.
     *
     * @return array
     */
    public static function getOutOfFunnelStageIds()
    {
        return array(
            self::STAGE_ID_PERMANENT_RANGE,
            self::STAGE_ID_DISCONTINUED
        );
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New stage';
    }
}
