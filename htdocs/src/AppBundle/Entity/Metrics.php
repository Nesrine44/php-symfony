<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Metrics
 *
 * @ORM\Table(name="metrics")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MetricsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Metrics
{
    // Mail promote email
    const ACTION_MAIL_PROMOTE_SENT = 'mail_promote_sent';
    const ACTION_MAIL_PROMOTE_OPENED = 'mail_promote_opened';
    const ACTION_MAIL_PROMOTE_CLICKED = 'mail_promote_clicked';
    const ACTION_ID_MAIL_PROMOTE_SENT = 1;
    const ACTION_ID_MAIL_PROMOTE_OPENED = 2;
    const ACTION_ID_MAIL_PROMOTE_CLICKED = 3;
    // End Mail promote email

    // Mail others
    const ACTION_SHOW_ACTIVITY = 'show_activity';
    const ACTION_SHOW_ACTIVITY_VIEWS = 'show_activity_view_all_views';
    const ACTION_SHOW_ACTIVITY_DOWNLOADS = 'show_activity_view_all_downloads';
    const ACTION_SHOW_ACTIVITY_TAB_VIEWS = 'show_activity_tab_views';
    const ACTION_SHOW_ACTIVITY_TAB_DOWNLOADS = 'show_activity_tab_downloads';
    const ACTION_TIDIO_EVENT_ELEVATORPITCH_COMPLETED = 'tidio_event_elevatorpitch_completed';
    const ACTION_TIDIO_EVENT_FINANCIAL_COMPLETED = 'tidio_event_financial_completed';
    const ACTION_ID_SHOW_ACTIVITY = 4;
    const ACTION_ID_SHOW_ACTIVITY_VIEWS = 5;
    const ACTION_ID_SHOW_ACTIVITY_DOWNLOADS = 6;
    const ACTION_ID_SHOW_ACTIVITY_TAB_VIEWS = 7;
    const ACTION_ID_SHOW_ACTIVITY_TAB_DOWNLOADS = 8;
    const ACTION_ID_TIDIO_EVENT_ELEVATORPITCH_COMPLETED = 9;
    const ACTION_ID_TIDIO_EVENT_FINANCIAL_COMPLETED = 10;
    // End Mail others


    // Mails change stage
    const ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_SENT = 'mail_change_stage_experiment_sent';
    const ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_OPENED = 'mail_change_stage_experiment_opened';
    const ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED = 'mail_change_stage_experiment_clicked';

    const ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_SENT = 11;
    const ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_OPENED = 12;
    const ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED = 13;

    const ACTION_MAIL_CHANGE_STAGE_INCUBATE_SENT = 'mail_change_stage_incubate_sent';
    const ACTION_MAIL_CHANGE_STAGE_INCUBATE_OPENED = 'mail_change_stage_incubate_opened';
    const ACTION_MAIL_CHANGE_STAGE_INCUBATE_CLICKED = 'mail_change_stage_incubate_clicked';

    const ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_SENT = 14;
    const ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_OPENED = 15;
    const ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_CLICKED = 16;
    // End Mails change stage


    // Mails innovation feedbacks
    const ACTION_MAIL_FEEDBACK_NEW_FEATURE_SENT = 'mail_feedback_new_feature_sent';
    const ACTION_MAIL_FEEDBACK_NEW_FEATURE_OPENED = 'mail_feedback_new_feature_opened';
    const ACTION_MAIL_FEEDBACK_NEW_FEATURE_CLICKED = 'mail_feedback_new_feature_clicked';
    const ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_SENT = 17;
    const ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_OPENED = 18;
    const ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_CLICKED = 19;

    const ACTION_MAIL_FEEDBACK_INVITE_SENT = 'mail_feedback_invite_sent';
    const ACTION_MAIL_FEEDBACK_INVITE_OPENED = 'mail_feedback_invite_opened';
    const ACTION_MAIL_FEEDBACK_INVITE_CLICKED = 'mail_feedback_invite_clicked';
    const ACTION_ID_MAIL_FEEDBACK_INVITE_SENT = 20;
    const ACTION_ID_MAIL_FEEDBACK_INVITE_OPENED = 21;
    const ACTION_ID_MAIL_FEEDBACK_INVITE_CLICKED = 22;

    const ACTION_MAIL_FEEDBACK_ANSWER_SENT = 'mail_feedback_answer_sent';
    const ACTION_MAIL_FEEDBACK_ANSWER_OPENED = 'mail_feedback_answer_opened';
    const ACTION_MAIL_FEEDBACK_ANSWER_CLICKED = 'mail_feedback_answer_clicked';
    const ACTION_ID_MAIL_FEEDBACK_ANSWER_SENT = 23;
    const ACTION_ID_MAIL_FEEDBACK_ANSWER_OPENED = 24;
    const ACTION_ID_MAIL_FEEDBACK_ANSWER_CLICKED = 25;
    // End Mails innovation feedbacks


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
     * @ORM\Column(name="metrics_action", type="string", nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="metrics_key", type="string", nullable=true)
     */
    private $key;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Innovation")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true)
     */
    private $data;


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
        if (!$this->created_at) {
            $this->created_at = new \DateTime();
        }
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
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return Activity
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
     * Set action.
     *
     * @param string|null $action
     *
     * @return Metrics
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
     * Set key.
     *
     * @param string|null $key
     *
     * @return Metrics
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
     * getActionsChoice
     * @return array
     */
    public static function getActionsChoice(){
        $ret = [];
        $actions = self::getActionsArray();
        foreach ($actions as $key => $value){
            $ret[$value] = $value;
        }
        return $ret;
    }

    /**
     * getActionsArray
     * @return array
     */
    public static function getActionsArray(){
        return array(
            self::ACTION_ID_MAIL_PROMOTE_SENT => self::ACTION_MAIL_PROMOTE_SENT,
            self::ACTION_ID_MAIL_PROMOTE_OPENED => self::ACTION_MAIL_PROMOTE_OPENED,
            self::ACTION_ID_MAIL_PROMOTE_CLICKED => self::ACTION_MAIL_PROMOTE_CLICKED,

            self::ACTION_ID_SHOW_ACTIVITY => self::ACTION_SHOW_ACTIVITY,
            self::ACTION_ID_SHOW_ACTIVITY_VIEWS => self::ACTION_SHOW_ACTIVITY_VIEWS,
            self::ACTION_ID_SHOW_ACTIVITY_DOWNLOADS => self::ACTION_SHOW_ACTIVITY_DOWNLOADS,
            self::ACTION_ID_SHOW_ACTIVITY_TAB_VIEWS => self::ACTION_SHOW_ACTIVITY_TAB_VIEWS,
            self::ACTION_ID_SHOW_ACTIVITY_TAB_DOWNLOADS => self::ACTION_SHOW_ACTIVITY_TAB_DOWNLOADS,
            self::ACTION_ID_TIDIO_EVENT_ELEVATORPITCH_COMPLETED => self::ACTION_TIDIO_EVENT_ELEVATORPITCH_COMPLETED,
            self::ACTION_ID_TIDIO_EVENT_FINANCIAL_COMPLETED => self::ACTION_TIDIO_EVENT_FINANCIAL_COMPLETED,

            self::ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_SENT => self::ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_SENT,
            self::ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_OPENED => self::ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_OPENED,
            self::ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED => self::ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED,

            self::ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_SENT => self::ACTION_MAIL_CHANGE_STAGE_INCUBATE_SENT,
            self::ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_OPENED => self::ACTION_MAIL_CHANGE_STAGE_INCUBATE_OPENED,
            self::ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_CLICKED => self::ACTION_MAIL_CHANGE_STAGE_INCUBATE_CLICKED,


            self::ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_SENT => self::ACTION_MAIL_FEEDBACK_NEW_FEATURE_SENT,
            self::ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_OPENED => self::ACTION_MAIL_FEEDBACK_NEW_FEATURE_OPENED,
            self::ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_CLICKED => self::ACTION_MAIL_FEEDBACK_NEW_FEATURE_CLICKED,

            self::ACTION_ID_MAIL_FEEDBACK_INVITE_SENT => self::ACTION_MAIL_FEEDBACK_INVITE_SENT,
            self::ACTION_ID_MAIL_FEEDBACK_INVITE_OPENED => self::ACTION_MAIL_FEEDBACK_INVITE_OPENED,
            self::ACTION_ID_MAIL_FEEDBACK_INVITE_CLICKED => self::ACTION_MAIL_FEEDBACK_INVITE_CLICKED,

            self::ACTION_ID_MAIL_FEEDBACK_ANSWER_SENT => self::ACTION_MAIL_FEEDBACK_ANSWER_SENT,
            self::ACTION_ID_MAIL_FEEDBACK_ANSWER_OPENED => self::ACTION_MAIL_FEEDBACK_ANSWER_OPENED,
            self::ACTION_ID_MAIL_FEEDBACK_ANSWER_CLICKED => self::ACTION_MAIL_FEEDBACK_ANSWER_CLICKED,
        );
    }

    /**
     * Get action by action_id.
     * 
     * @param int $action_id
     * @return string
     */
    public static function getActionByActiondId($action_id){
        $actions = self::getActionsArray();
        if(array_key_exists($action_id, $actions)){
            return $actions[$action_id];
        }
        return $action_id;
    }

    /**
     * Generate key for user and innovation.
     *
     * @param User $user
     * @param Innovation $innovation
     * @return string
     */
    public static function generateKeyForUserAndInnovation($user, $innovation)
    {
        return md5(time().'-'.$user->getId().'-'.$innovation->getId());
    }

    
    /**
     * Generate key for user and innovation.
     *
     * @param string $key
     * @param User $user
     * @param Innovation $innovation
     * @param int $action_id
     * @return string
     */
    public static function generateGlobalKeyForUserInnovationAndAction($key, $user, $innovation, $action_id)
    {
        return $key.'-'.$user->getId().'-'.$innovation->getId().'-'.$action_id;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? 'Metrics n°'.$this->getId() : 'New metrics';
    }
}
