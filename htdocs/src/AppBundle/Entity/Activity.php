<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActivityRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Activity
{
    // ACTION_ID
    const ACTION_INNOVATION_CREATED = 1; // Création d'une innovation
    const ACTION_INNOVATION_UPDATED = 2; // Mise à jour d'un champs d'une innovation
    const ACTION_INNOVATION_DELETED = 3; // Suppression d'une innovation
    const ACTION_INNOVATION_CHANGE_STAGE = 4; // Modification du stage d'une innovation
    const ACTION_INNOVATION_CHANGE_STATUS = 5; // Modification du status d'une innovation (Hors status New) // NON UTILISÉ
    const ACTION_INNOVATION_ONLINE_OFFLINE = 6; // Modification du status is_online d'une innovation // NON UTILISÉ
    const ACTION_INNOVATION_TOP_STORY = 7; // Modification du status top_story d'une innovation // NON UTILISÉ
    const ACTION_INNOVATION_BIG_BET = 8; // Modification du status big_bet d'une innovation // NON UTILISÉ
    const ACTION_EXPORT_EXCEL = 9; // Export fait par l'utilisateur
    const ACTION_EXPORT_PPT = 10; // Export fait par l'utilisateur
    const ACTION_INNOVATION_FROZEN = 11; // Export fait par l'utilisateur
    const ACTION_MAIL_CONTACT = 12; // L'utilisateur clique sur le bouton "Contact" dans l'exploration d'une innovation
    const ACTION_DOWNLOAD_INNOVATION_BOOK = 13; // Téléchargement d'un innovation BOOK
    const ACTION_DOWNLOAD_INNOVATION_CRITERIA = 14; // Téléchargement d'un Innovation IN and OUT criteria

    const ACTION_PROMOTE_INNOVATION_VIEW = 15; // Visualisation d'une innovation dans explore
    const ACTION_PROMOTE_INNOVATION_EXPORT = 16; // Export d'une innovation dans explore
    const ACTION_INNOVATION_SHARE = 22; // Partage d'une innovation dans explore

    const ACTION_TEAM_INNOVATION_CREATE_RIGHT = 17; // Création d'un droit pour une innovation
    const ACTION_TEAM_INNOVATION_UPDATE_RIGHT = 18; // Modification d'un droit pour une innovation
    const ACTION_TEAM_INNOVATION_DELETE_RIGHT = 19; // Suppression d'un droit pour une innovation

    const ACTION_DOWNLOAD_ASSETS_PACK = 20; // Téléchargement de l'asset pack
    const ACTION_DOWNLOAD_INNOVATION_LEAFLET = 21; // Téléchargement de l'innovation leaflet


    const ACTION_FEEDBACK_REQUEST = 23; // Demande de feedback
    const ACTION_FEEDBACK_ANSWER = 24; // Donner du feedback

    const ACTION_CANVAS_CREATED = 25; // Création d'un canvas
    const ACTION_CANVAS_UPDATED = 26; // Mise à jour d'un champs d'un canvas

    // SOURCE_ID
    const SOURCE_WEB = 1;
    const SOURCE_WS = 2;
    const SOURCE_IOS = 3;
    const SOURCE_ANDROID = 4;


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
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $old_id;

    /**
     * @var int
     *
     * @ORM\Column(name="action_id", type="integer", nullable=true)
     */
    private $action_id;

    /**
     * @var int
     *
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $source_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_child", type="boolean")
     */
    private $is_child = false;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="activities")
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
     * @ORM\ManyToOne(targetEntity="FinancialData", inversedBy="activities")
     * @ORM\JoinColumn(name="financial_data_id", referencedColumnName="id")
     */
    protected $financial_data;

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
     * @return Activity
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
     * import created_at
     */
    public function importCreatedAt($created_at = null)
    {
        if ($created_at) {
            $this->created_at = $created_at;
        }
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
     * Get timestamp created_at.
     *
     * @return int
     */
    public function getTimestampCreatedAt()
    {
        return $this->created_at->getTimestamp();
    }

    /**
     * Set actionId.
     *
     * @param int|null $actionId
     *
     * @return Activity
     */
    public function setActionId($actionId = null)
    {
        $this->action_id = $actionId;

        return $this;
    }

    /**
     * Get actionId.
     *
     * @return int|null
     */
    public function getActionId()
    {
        return $this->action_id;
    }

    /**
     * Get action libelle.
     *
     * @return int|string
     */
    public function getActionLibelle()
    {
        $actionChoices = self::getFlattenActions();
        return (array_key_exists($this->action_id, $actionChoices)) ? $actionChoices[$this->action_id] : $this->action_id;
    }

    /**
     * Set sourceId.
     *
     * @param int|null $sourceId
     *
     * @return Activity
     */
    public function setSourceId($sourceId = null)
    {
        $this->source_id = $sourceId;

        return $this;
    }

    /**
     * Get sourceId.
     *
     * @return int|null
     */
    public function getSourceId()
    {
        return $this->source_id;
    }

    /**
     * Set isChild.
     *
     * @param bool $isChild
     *
     * @return Activity
     */
    public function setIsChild($isChild)
    {
        $this->is_child = $isChild;

        return $this;
    }

    /**
     * Get isChild.
     *
     * @return bool
     */
    public function getIsChild()
    {
        return $this->is_child;
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
        $this->data = json_encode($data);

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
     * Set financialData.
     *
     * @param \AppBundle\Entity\FinancialData|null $financialData
     *
     * @return Activity
     */
    public function setFinancialData(\AppBundle\Entity\FinancialData $financialData = null)
    {
        $this->financial_data = $financialData;

        return $this;
    }

    /**
     * Get financialData.
     *
     * @return \AppBundle\Entity\FinancialData|null
     */
    public function getFinancialData()
    {
        return $this->financial_data;
    }

    /**
     * Set oldId.
     *
     * @param int $oldId
     *
     * @return Activity
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

    /**
     * To html.
     *
     * @param bool $need_innovation_name
     * @return string
     */
    public function toHtml($need_innovation_name = false)
    {
        $ret = '<div class="content-activity activity-' . $this->getActionId() . '   ' . $this->getHtmlClass() . '">';
        $content = $this->getHtmlContent($need_innovation_name);
        $context = $this->getHtmlContext();
        $proper_created = $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format("m/d/Y H:i:s");
        $ret .= '<div class="line">';
        $ret .= '<div class="content-icon"><div class="icon"></div></div>';
        $ret .= '</div>';
        $ret .= '<div class="infos">';
        $ret .= '<div class="content">' . $content . '</div>';
        $ret .= '<div class="context">' . $context . '</div>';
        $ret .= '</div>';
        $ret .= '<div class="date with-tooltip" data-timestamp="' . $this->getCreatedAt()->getTimestamp() . '" title="' . $proper_created . '">' . $this->getRelativeCreatedAtDate() . '</div>';
        $ret .= '</div>';
        return $ret;
    }

    /**
     * To promote html.
     *
     * @param string $style
     * @return string
     */
    public function toPromoteHtml($style = '')
    {
        $user = $this->getUser();
        $default_bg = ($style == 'big') ? '/images/default/user.png' : '/images/default/user-white.png';
        $proper_created = $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format("m/d/Y H:i:s");
        $ret = '<div class="list-item-special-activity promote '.$style.'">';
        $ret .= '<div class="picture loading-bg" data-bg="'.$user->getPictureUrl().'" data-default-bg="'.$default_bg.'"></div>';
        if($style == 'big') {
            $ret .= '<div class="infos">';
            $ret .= '<div class="name text-ellipsis line-height-1-2 font-montserrat-700"><a href="/user/'.$user->getId().'" class="link-profile color-000 hub-link-on-hover line-height-1-2">'.$user->getProperUsername().'</a></div>';
            $ret .= '<div class="subname text-ellipsis line-height-1-2 color-9b9b9b font-size-14 font-work-sans-400">'.$user->getSituationAndEntity().'</div>';
            $ret .= '</div>';
        }else{
            $ret .= '<div class="name text-ellipsis line-height-1-2"><a href="/user/' . $user->getId() . '" class="link-profile color-000 hub-link-on-hover line-height-1-2">' . $user->getProperUsername() . '</a></div>';
        }
        $ret .= '<div class="date with-tooltip" data-timestamp="' . $this->getCreatedAt()->getTimestamp() . '" title="' . $proper_created . '">' . str_replace(" ", "",$this->getRelativeCreatedAtDate()) . '</div>';
        $ret .= '</div>';
        return $ret;
    }

    /**
     * To promote html.
     *
     * @param string $style
     * @return string
     */
    public function toShareHtml($style = "")
    {
        $user = $this->getUser();
        $proper_created = $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format("m/d/Y H:i:s");
        $default_bg = ($style == 'big') ? '/images/default/user.png' : '/images/default/user-white.png';
        $data_array = $this->getDataArray();
        $target_user = $data_array['target_user'];
        $ret = '<div class="list-item-special-activity share '.$style.'">';
        $title_seen = ($data_array['clicked']) ? 'title="'.$target_user['username'].' saw the innovation"' : '';
        $classe_seen = ($data_array['clicked']) ? 'with-tooltip' : 'opacity-0';
        $ret .= '<div class="seen '.$classe_seen.'" '.$title_seen.'></div>';
        $ret .= '<div class="picture loading-bg" data-bg="'.$target_user['picture'].'" data-default-bg="'.$default_bg.'"></div>';
        if($style == 'big') {
            $ret .= '<div class="infos">';
            $ret .= '<div class="text-ellipsis line-height-1-2 font-montserrat-700"><a href="/user/'.$target_user['id'].'" class="link-profile color-000 hub-link-on-hover line-height-1-2">'.$target_user['username'].'</a></div>';
            $situation_and_entity = (array_key_exists('situation_and_entity', $target_user)) ? $target_user['situation_and_entity'] : '';
            $ret .= '<div class="subname text-ellipsis line-height-1-2 color-9b9b9b font-size-14 font-work-sans-400">'.$situation_and_entity.'</div>';
            $ret .= '</div>';
        }else{
            $ret .= '<div class="name text-ellipsis line-height-1-2"><a href="/user/'.$target_user['id'].'" class="link-profile color-000 hub-link-on-hover line-height-1-2">'.$target_user['username'].'</a></div>';
        }
        $ret .= '<div class="other-info text-ellipsis line-height-1-2">Shared by <a href="/user/'.$user->getId().'" class="color-787878 cursor-pointer link-profile link-underline with-tooltip-user line-height-1-2" data-picture="'.$user->getPictureUrl().'" data-situation-and-entity="'.$user->getSituationAndEntity().'" data-username="'.$user->getProperUsername().'">'.$user->getProperUsername().'</a></div>';
        $ret .= '<div class="date with-tooltip" data-timestamp="' . $this->getCreatedAt()->getTimestamp() . '" title="' . $proper_created . '">' .  str_replace(" ", "",$this->getRelativeCreatedAtDate()) . '</div>';
        $ret .= '</div>';
        return $ret;
    }

    /**
     * Get html class.
     *
     * @return string
     */
    public function getHtmlClass()
    {
        $action_id = $this->getActionId();
        $data = $this->getDataArray();
        $key = (array_key_exists('key', $data)) ? $data['key'] : '';
        switch ($action_id) {
            case self::ACTION_INNOVATION_CREATED:
            case self::ACTION_EXPORT_EXCEL:
            case self::ACTION_EXPORT_PPT:
            case self::ACTION_INNOVATION_CHANGE_STAGE:
            case self::ACTION_INNOVATION_DELETED:
            case self::ACTION_INNOVATION_FROZEN:
            case self::ACTION_MAIL_CONTACT:
                return 'big';
            case self::ACTION_INNOVATION_UPDATED:
                if ($key == 'title' || $key == 'market_date') {
                    return 'big';
                }
                return '';
            default:
                return '';
        }
    }

    /**
     * Get html context.
     *
     * @return string
     */
    public function getHtmlContext()
    {
        $action_id = $this->getActionId();
        if ($action_id == self::ACTION_EXPORT_EXCEL || $action_id == self::ACTION_EXPORT_PPT) {
            return 'Export generated on ' . $this->getHtmlCreatedAtDate();
        }
        return '';
    }

    /**
     * Get html created_at date.
     *
     * @return string
     */
    public function getHtmlCreatedAtDate()
    {
        return $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format('m/d/Y') . ' at ' . $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format('g:i a');
    }

    /**
     * Get relative created_at date.
     *
     * @param bool $only_time
     * @param bool $minimal
     * @return string
     */
    public function getRelativeCreatedAtDate()
    {
        $date = $this->getCreatedAt();
        return self::getRelativeDate($date, false, true);
    }

    /**
     * Get relative date.
     *
     * @param $date
     * @param bool $only_time
     * @param bool $minimal
     * @return string
     */
    public static function getRelativeDate($date){
        $actualDate = new \DateTime();
        $actualDate->setTimezone(new \DateTimeZone('GMT'));
        $diff = $date->diff($actualDate);
        if ($diff->format("%y") >= 1) {
            return $diff->format("%y")."yr";
        } elseif ($diff->format("%m") >= 1) {
            return $diff->format("%m")."mth";
        } elseif ($diff->format("%a") >= 1) {
            if($diff->format("%a") > 7){
                return (round($diff->format("%a")/7, 0, PHP_ROUND_HALF_DOWN))."w";
            }
            return $diff->format("%a")."d";
        } elseif ($diff->format("%h") >= 1) {
            return $diff->format("%h")."h";
        } elseif ($diff->format("%i") >= 1) {
            return $diff->format("%i")."mn";
        } else {
            return 'now';
        }
    }

    /**
     * Get HTML content.
     *
     * @param bool $need_innovation_name
     * @return string
     */
    public function getHtmlContent($need_innovation_name = false)
    {
        $action_id = $this->getActionId();
        $user_activity = 'Someone';
        $user_link = "<a>'.$user_activity.'</a>";
        if ($this->getUser()) {
            $user_activity = $this->getUser()->getProperUsername();
            $user_link = '<a href="/user/'.$this->getUser()->getId().'" class="link-profile link-underline">'.$user_activity.'</a>';
        }
        $innovation_name = '';
        $clear_innovation_name = 'the project';
        $export_adder = ($action_id == self::ACTION_EXPORT_EXCEL) ? ' to EXCEL' : ' to PPT';
        $data = $this->getDataArray();
        $target_user_link = '';
        if(array_key_exists('target_user', $data) && $data['target_user']){
            $target_user_link = '<a href="/user/'.$data['target_user']['id'].'" class="link-profile link-underline">'.$data['target_user']['username'].'</a>';
        }
        if (!$this->getInnovation()) {
            if ($data && array_key_exists('url', $data)) {
                if (strpos($data['url'], 'submit_button=export_ppt_top_contributors_performance') !== false) {
                    $clear_innovation_name = 'Entity performance review';
                } elseif (strpos($data['url'], 'export=ppt') !== false) {
                    $clear_innovation_name = 'Selection in PPT';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'export=excel') !== false) {
                    $clear_innovation_name = 'Selection in Excel';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Power%20Point%20Quali%20(PPT)","type":"Power Point Quali (PPT)') !== false) {
                    $clear_innovation_name = 'Power Point Quali (PPT)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Excel%20(XLS)') !== false) {
                    $clear_innovation_name = 'Excel (XLS)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Power%20Point%20(PPT)') !== false) {
                    $clear_innovation_name = 'Power Point (PPT)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Team%20Matrix%20update%20(XLS)') !== false) {
                    $clear_innovation_name = 'Team Matrix update (XLS)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Team%20Matrix%20update%20-%20without%20duplicate%20(XLS)') !== false) {
                    $clear_innovation_name = 'Team Matrix update - without duplicate (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'active-users') {
                    $clear_innovation_name = 'Active Users (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-excel') {
                    $clear_innovation_name = 'All Innovations in Excel (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-excel-selection') {
                    $clear_innovation_name = 'Selection in Excel';
                    $export_adder = '';
                } elseif ($data['type'] === 'entity-performance-review') {
                    $clear_innovation_name = 'Entity performance review';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-ppt') {
                    $clear_innovation_name = 'Power Point Quali (PPT)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-ppt-selection') {
                    $clear_innovation_name = 'Selection in PPT';
                    $export_adder = '';
                }
            }
        }
        if ($need_innovation_name && $this->getInnovation()) {
            $clear_innovation_name = '<a href="' . $this->getInnovation()->getInnovationUrl() . '" class="link_explore_detail">' . $this->getInnovation()->getTitle() . '</a>';
            $innovation_name = ' on ' . $clear_innovation_name;
        }
        $key = (array_key_exists('key', $data)) ? $data['key'] : 'unknown';
        $proper_key = ucfirst(str_replace('_', ' ', $key));
        if ($proper_key == 'title') {
            $proper_key = 'Innovation Name';
        } elseif ($key == 'pot_picture_1') {
            $proper_key = 'Drinking ritual picture';
        } elseif ($key == 'pot_picture_2') {
            $proper_key = 'Key competitor picture';
        } elseif ($key == 'market_date') {
            $proper_key = 'Market Introduction date';
        } elseif ($key == 'proofs_of_traction_picture_1_legend') {
            $proper_key = 'Drinking ritual picture legend';
        } elseif ($key == 'proofs_of_traction_picture_2_legend') {
            $proper_key = 'Key competitor picture legend';
        }
        $adder = '';
        if ($key == 'title') {
            $adder = ' of "' . $data['old_value'] . '"';
        } else if (strtolower($key) == 'contact') {
            $adder = ' to "' . $data['new_value']['title'] . '"';
        }
        switch ($action_id) {
            case self::ACTION_INNOVATION_CREATED:
                if ($need_innovation_name) {
                    return '<span class="name">' . $user_link . '</span> created ' . $clear_innovation_name;
                }
                return '<span class="name">' . $user_link . '</span> created this project';
            case self::ACTION_INNOVATION_UPDATED:
                // TODO GOOD WORDING
                return '<span class="name">' . $user_link . '</span> modified ' . $proper_key . $adder . $innovation_name;
            case self::ACTION_INNOVATION_DELETED:
                if ($need_innovation_name) {
                    return '<span class="name">' . $user_link . '</span> deleted ' . $clear_innovation_name;
                }
                return '<span class="name">' . $user_link . '</span> deleted this project';
            case self::ACTION_INNOVATION_CHANGE_STAGE:
                return '<span class="name">' . $user_link . '</span> changed stage to "' . Stage::returnCurrentStageTitle($data['new_value']) . '"' . $innovation_name;
            case self::ACTION_INNOVATION_FROZEN:
                if ($data['new_value'] == '1') {
                    return '<span class="name">' . $user_link . '</span> froze ' . $clear_innovation_name;
                } else {
                    return '<span class="name">' . $user_link . '</span> unfroze ' . $clear_innovation_name;
                }
            case self::ACTION_EXPORT_EXCEL:
                if ($need_innovation_name) {
                    return '<span class="name">' . $user_link . '</span> exported ' . $clear_innovation_name . $export_adder;
                }
                return '<span class="name">' . $user_link . '</span> exported this project to EXCEL';
            case self::ACTION_EXPORT_PPT:
                if ($need_innovation_name) {
                    return '<span class="name">' . $user_link . '</span> exported ' . $clear_innovation_name . $export_adder;
                }
                return '<span class="name">' . $user_link . '</span> exported this project to PPT';
            case self::ACTION_MAIL_CONTACT:
                return '<span class="name">' . $user_link . '</span> contacted ' . $data['username'] . ' about ' . $clear_innovation_name;
            case self::ACTION_DOWNLOAD_INNOVATION_BOOK:
                $book_name = self::getDownloadInnovationBookTitleByType($data['type']);
                return '<span class="name">' . $user_link . '</span> downloaded ' . $book_name;
            case self::ACTION_DOWNLOAD_INNOVATION_CRITERIA:
                return '<span class="name">' . $user_link . '</span> downloaded Innovation IN and OUT criteria';
            case self::ACTION_TEAM_INNOVATION_CREATE_RIGHT:
                $clear_innovation_name = ($need_innovation_name) ? $clear_innovation_name : 'the';
                return '<span class="name">' . $user_link . '</span> added <span class="name">' . $data['user_name'] . '</span> to ' . $clear_innovation_name . ' Team';
            case self::ACTION_TEAM_INNOVATION_UPDATE_RIGHT:
                $clear_innovation_name = ($need_innovation_name) ? $clear_innovation_name : 'the';
                return '<span class="name">' . $user_link . '</span> updated <span class="name">' . $data['user_name'] . '</span> rights to ' . $clear_innovation_name . ' Team';
            case self::ACTION_TEAM_INNOVATION_DELETE_RIGHT:
                $clear_innovation_name = ($need_innovation_name) ? $clear_innovation_name : 'the';
                return '<span class="name">' . $user_link . '</span> removed <span class="name">' . $data['user_name'] . '</span> from ' . $clear_innovation_name . ' Team';
            case self::ACTION_DOWNLOAD_ASSETS_PACK:
                return '<span class="name">' . $user_link . '</span> downloaded ASSET PACK';
            case self::ACTION_DOWNLOAD_INNOVATION_LEAFLET:
                return '<span class="name">' . $user_link . '</span> downloaded INNOVATION LEAFLET';
            case self::ACTION_INNOVATION_SHARE:
                return '<span class="name">' . $user_link . '</span> shared '.$clear_innovation_name.' to <span class="name">'.$target_user_link.'</span>';
            case self::ACTION_FEEDBACK_REQUEST:
                return '<span class="name">' . $user_link . '</span> asked feedback to <span class="name">'.$target_user_link.'</span> on '.$clear_innovation_name;
            case self::ACTION_CANVAS_CREATED:
                $canvas_title = ($data['canvas_title']) ? ' "'.$data['canvas_title'].'"' : '';
                if(!$canvas_title && $data['canvas_id']){
                    $canvas_title = ' #'.$data['canvas_id'];
                }
                return '<span class="name">' . $user_link . '</span> created new canvas'.$canvas_title. $adder . $innovation_name;;
            case self::ACTION_CANVAS_UPDATED:
                $canvas_title = ($data['canvas_title']) ? ' "'.$data['canvas_title'].'"' : '';
                if(!$canvas_title && $data['canvas_id']){
                    $canvas_title = ' #'.$data['canvas_id'];
                }
                //$field = ucfirst(str_replace('_', ' ', $data['field_name']));
                return '<span class="name">' . $user_link . '</span> updated canvas'.$canvas_title. $adder . $innovation_name;;
            case self::ACTION_FEEDBACK_ANSWER:
                return '<span class="name">' . $user_link . '</span> gave feedback on '.$clear_innovation_name;
            default:
                return '<span class="name">' . $user_link . '</span> ==> ACTION : ' . $action_id . ' key => ' . $data['key'] . ' // old => ' . $data['old_value'] . ' to new => ' . $data['new_value'] . $innovation_name;

        }
    }

    /**
     * Get HTML content.
     *
     * @param bool $need_innovation_name
     * @return string
     */
    public function getClearMessage()
    {
        $action_id = $this->getActionId();
        $user_activity = 'Someone';
        if ($this->getUser()) {
            $user_activity = $this->getUser()->getProperUsername();
        }
        $innovation_name = '';
        $clear_innovation_name = 'the project';
        $export_adder = ($action_id == self::ACTION_EXPORT_EXCEL) ? ' to EXCEL' : ' to PPT';
        $data = $this->getDataArray();
        if (!$this->getInnovation()) {
            if ($data && array_key_exists('url', $data)) {
                if (strpos($data['url'], 'submit_button=export_ppt_top_contributors_performance') !== false) {
                    $clear_innovation_name = 'Entity performance review';
                } elseif (strpos($data['url'], 'export=ppt') !== false) {
                    $clear_innovation_name = 'Selection in PPT';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'export=excel') !== false) {
                    $clear_innovation_name = 'Selection in Excel';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Power%20Point%20Quali%20(PPT)","type":"Power Point Quali (PPT)') !== false) {
                    $clear_innovation_name = 'Power Point Quali (PPT)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Excel%20(XLS)') !== false) {
                    $clear_innovation_name = 'Excel (XLS)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Power%20Point%20(PPT)') !== false) {
                    $clear_innovation_name = 'Power Point (PPT)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Team%20Matrix%20update%20(XLS)') !== false) {
                    $clear_innovation_name = 'Team Matrix update (XLS)';
                    $export_adder = '';
                } elseif (strpos($data['url'], 'submit_button=Team%20Matrix%20update%20-%20without%20duplicate%20(XLS)') !== false) {
                    $clear_innovation_name = 'Team Matrix update - without duplicate (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'active-users') {
                    $clear_innovation_name = 'Active Users (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-excel') {
                    $clear_innovation_name = 'All Innovations in Excel (XLS)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-excel-selection') {
                    $clear_innovation_name = 'Selection in Excel';
                    $export_adder = '';
                } elseif ($data['type'] === 'entity-performance-review') {
                    $clear_innovation_name = 'Entity performance review';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-ppt') {
                    $clear_innovation_name = 'Power Point Quali (PPT)';
                    $export_adder = '';
                } elseif ($data['type'] === 'innovations-ppt-selection') {
                    $clear_innovation_name = 'Selection in PPT';
                    $export_adder = '';
                }
            }
        }
        if ($this->getInnovation()) {
            $clear_innovation_name = $this->getInnovation()->getTitle();
            $innovation_name = ' on ' . $clear_innovation_name;
        }
        $key = (array_key_exists('key', $data)) ? $data['key'] : 'unknown';
        $proper_key = ucfirst(str_replace('_', ' ', $key));
        if ($proper_key == 'title') {
            $proper_key = 'Innovation Name';
        } elseif ($key == 'pot_picture_1') {
            $proper_key = 'Drinking ritual picture';
        } elseif ($key == 'pot_picture_2') {
            $proper_key = 'Key competitor picture';
        } elseif ($key == 'market_date') {
            $proper_key = 'Market Introduction date';
        } elseif ($key == 'proofs_of_traction_picture_1_legend') {
            $proper_key = 'Drinking ritual picture legend';
        } elseif ($key == 'proofs_of_traction_picture_2_legend') {
            $proper_key = 'Key competitor picture legend';
        }
        $adder = '';
        if ($key == 'title') {
            $adder = ' of "' . $data['old_value'] . '"';
        } else if (strtolower($key) == 'contact') {
            $adder = ' to "' . $data['new_value']['title'] . '"';
        }
        switch ($action_id) {
            case self::ACTION_INNOVATION_CREATED:
                return $user_activity . ' created ' . $clear_innovation_name;
            case self::ACTION_INNOVATION_UPDATED:
                // TODO GOOD WORDING
                return $user_activity . ' modified ' . $proper_key . $adder . $innovation_name;
            case self::ACTION_INNOVATION_DELETED:
                return '<span class="name">' . $user_activity . ' deleted ' . $clear_innovation_name;
            case self::ACTION_INNOVATION_CHANGE_STAGE:
                return $user_activity . ' changed stage to "' . Stage::returnCurrentStageTitle($data['new_value']) . '"' . $innovation_name;
            case self::ACTION_INNOVATION_FROZEN:
                if ($data['new_value'] == '1') {
                    return $user_activity . ' froze ' . $clear_innovation_name;
                } else {
                    return $user_activity . ' unfroze ' . $clear_innovation_name;
                }
            case self::ACTION_EXPORT_EXCEL:
                return $user_activity . ' exported ' . $clear_innovation_name . $export_adder;
            case self::ACTION_EXPORT_PPT:
                return $user_activity . ' exported ' . $clear_innovation_name . $export_adder;
            case self::ACTION_MAIL_CONTACT:
                return $user_activity . ' contacted ' . $data['username'] . ' about ' . $clear_innovation_name;
            case self::ACTION_DOWNLOAD_INNOVATION_BOOK:
                $book_name = self::getDownloadInnovationBookTitleByType($data['type']);
                return $user_activity . ' downloaded ' . $book_name;
            case self::ACTION_DOWNLOAD_INNOVATION_CRITERIA:
                return $user_activity . ' downloaded Innovation IN and OUT criteria';
            case self::ACTION_TEAM_INNOVATION_CREATE_RIGHT:
                return $user_activity . ' added ' . $data['user_name'] . ' to ' . $clear_innovation_name . ' Team';
            case self::ACTION_TEAM_INNOVATION_UPDATE_RIGHT:
                return $user_activity . ' updated ' . $data['user_name'] . ' rights to ' . $clear_innovation_name . ' Team';
            case self::ACTION_TEAM_INNOVATION_DELETE_RIGHT:
                return $user_activity . ' removed ' . $data['user_name'] . ' from ' . $clear_innovation_name . ' Team';
            case self::ACTION_DOWNLOAD_ASSETS_PACK:
                return $user_activity . ' downloaded ASSET PACK';
            case self::ACTION_DOWNLOAD_INNOVATION_LEAFLET:
                return $user_activity . ' downloaded INNOVATION LEAFLET';
            case self::ACTION_PROMOTE_INNOVATION_VIEW:
                return $user_activity . ' viewed '.$clear_innovation_name;
            case self::ACTION_PROMOTE_INNOVATION_EXPORT:
                return $user_activity . ' exported '.$clear_innovation_name;
            case self::ACTION_INNOVATION_SHARE:
                return $user_activity . ' share '.$clear_innovation_name." to ".$data['target_user']['username'];
            case self::ACTION_FEEDBACK_REQUEST:
                return $user_activity . ' asked feedback to '.$data['target_user']['username'].' on '.$clear_innovation_name;
            case self::ACTION_FEEDBACK_ANSWER:
                return $user_activity . ' gave feedback on '.$clear_innovation_name;
            case self::ACTION_CANVAS_CREATED:
                $canvas_title = ($data['canvas_title']) ? ' "'.$data['canvas_title'].'"' : '';
                if(!$canvas_title && $data['canvas_id']){
                    $canvas_title = ' #'.$data['canvas_id'];
                }
                return $user_activity.' created new canvas'.$canvas_title.' for '.$clear_innovation_name;
            case self::ACTION_CANVAS_UPDATED:
                $canvas_title = ($data['canvas_title']) ? ' "'.$data['canvas_title'].'"' : '';
                if(!$canvas_title && $data['canvas_id']){
                    $canvas_title = ' #'.$data['canvas_id'];
                }
                //$field = ucfirst(str_replace('_', ' ', $data['field_name']));
                return $user_activity.' updated canvas'.$canvas_title.' for '.$clear_innovation_name;
            default:
                return $user_activity . ' ==> ACTION : ' . $action_id . ' key => ' . $data['key'] . ' // old => ' . $data['old_value'] . ' to new => ' . $data['new_value'] . $innovation_name;

        }
    }

    /**
     * Get download innovation book title by type.
     *
     * @param $type
     * @return string
     */
    public static function getDownloadInnovationBookTitleByType($type)
    {
        switch ($type) {
            case 'rtd':
                return 'READY-TO-DRINK Innovation Book';
            case 'craft':
                return 'CRAFT Innovation Book';
            case 'nolow':
                return 'NO & LOW ALCOHOL Innovation Book';
            default:
                return 'Innovation Book';

        }
    }

    /**
     * Generate title.
     *
     * @param string|null $key
     * @return $this
     */
    public function generateTitle($key = null)
    {
        $add_user = false;
        switch ($this->getActionId()) {
            case self::ACTION_DOWNLOAD_INNOVATION_BOOK:
                $title = 'Download Innovation Book';
                $add_user = true;
                break;
            case self::ACTION_DOWNLOAD_ASSETS_PACK:
                $title = 'Download Asset Pack';
                $add_user = true;
                break;
            case self::ACTION_DOWNLOAD_INNOVATION_LEAFLET:
                $title = 'Download Innovation Leaflet';
                $add_user = true;
                break;
            case self::ACTION_DOWNLOAD_INNOVATION_CRITERIA:
                $title = 'Download Innovation IN and OUT criteria';
                $add_user = true;
                break;
            case self::ACTION_EXPORT_PPT:
            case self::ACTION_EXPORT_EXCEL:
                $title = "Export ";
                if ($this->getActionId() == self::ACTION_EXPORT_EXCEL) {
                    $title .= " excel ";
                } elseif ($this->getActionId() == self::ACTION_EXPORT_PPT) {
                    $title .= " PPT ";
                }
                if ($key) {
                    $title .= $key;
                }
                if ($this->getInnovation()) {
                    $title .= " for innovation " . $this->getInnovation()->getTitle();
                }
                break;
            default:
                if ($this->getInnovation()) {
                    $title = $this->getInnovation()->getTitle();
                    if ($key) {
                        $title .= ' [' . $key . ']';
                    }
                } else {
                    $title = 'Some activity';
                    $add_user = true;
                }
                break;
        }
        if ($add_user && $this->getUser()) {
            $title .= " - " . $this->getUser()->getProperUsername();
        }
        $this->setTitle($title);
        return $this;
    }


    /**
     * To dashboard array.
     *
     * @return array
     */
    public function toDashboardArray()
    {
        $infos = array(
            'stage' => null,
            'stage_name' => '',
            'stage_icon_class' => 'light ',
            'user' => (($this->getUser()) ? $this->getUser()->getProperUsername() : ''),
            'innovation_name' => (($this->getInnovation()) ? $this->getInnovation()->getTitle() : ''),
            'created_at' => $this->getCreatedAt()->getTimestamp(),
            'relative_created' => $this->getRelativeCreatedAtDate(),
            'placeholder_date' => $this->getCreatedAt()->setTimezone(new \DateTimeZone('GMT'))->format("m/d/Y H:i:s"),
            'entity_brand' => '',
            'innovation_url' => null,
        );
        if ($this->getInnovation()) {
            $innovation = $this->getInnovation();
            $infos['stage'] = ($innovation->getStage()) ? $innovation->getStage()->getCssClass() : '';
            $infos['stage_icon_class'] .= $infos['stage'];
            $infos['stage_name'] = ($innovation->getStage()) ? $innovation->getStage()->getTitle() : '';
            if($innovation->getIsFrozen()){
                $infos['stage_name'] .= ' (frozen)';
                $infos['stage_icon_class'] .= ' frozen';
            }
            $infos['entity_brand'] = ($innovation->getEntity()) ? $innovation->getEntity()->getTitle() : '';
            if ($innovation->getBrand()) {
                $infos['entity_brand'] .= ($infos['entity_brand'] != '') ? ', ' . $innovation->getBrand()->getTitle() : $innovation->getBrand()->getTitle();
            }
            $infos['innovation_url'] = $innovation->getInnovationUrl();
        }
        return $infos;
    }

    /**
     * Get filters actions.
     *
     * @return array
     */
    public static function getFiltersActions()
    {
        $ret = array();
        $actions = self::getFlattenActions();
        foreach ($actions as $key => $value) {
            $ret[$value] = $key;
        }
        return $ret;
    }

    /**
    * Get flatten actions.
    *
    * @return array
    */
    public static function getFlattenActions()
    {
        $ret = array();
        $ret[self::ACTION_INNOVATION_CREATED] = 'ACTION_INNOVATION_CREATED ('.self::ACTION_INNOVATION_CREATED.')';
        $ret[self::ACTION_INNOVATION_UPDATED] = 'ACTION_INNOVATION_UPDATED ('.self::ACTION_INNOVATION_UPDATED.')';
        $ret[self::ACTION_INNOVATION_DELETED] = 'ACTION_INNOVATION_DELETED ('.self::ACTION_INNOVATION_DELETED.')';
        $ret[self::ACTION_INNOVATION_CHANGE_STAGE] = 'ACTION_INNOVATION_CHANGE_STAGE ('.self::ACTION_INNOVATION_CHANGE_STAGE.')';
        $ret[self::ACTION_INNOVATION_FROZEN] = 'ACTION_INNOVATION_FROZEN ('.self::ACTION_INNOVATION_FROZEN.')';
        $ret[self::ACTION_EXPORT_EXCEL] = 'ACTION_EXPORT_EXCEL ('.self::ACTION_EXPORT_EXCEL.')';
        $ret[self::ACTION_EXPORT_PPT] = 'ACTION_EXPORT_PPT ('.self::ACTION_EXPORT_PPT.')';
        $ret[self::ACTION_MAIL_CONTACT] = 'ACTION_MAIL_CONTACT ('.self::ACTION_MAIL_CONTACT.')';
        $ret[self::ACTION_DOWNLOAD_INNOVATION_BOOK] = 'ACTION_DOWNLOAD_INNOVATION_BOOK ('.self::ACTION_DOWNLOAD_INNOVATION_BOOK.')';
        $ret[self::ACTION_DOWNLOAD_INNOVATION_CRITERIA] = 'ACTION_DOWNLOAD_INNOVATION_CRITERIA ('.self::ACTION_DOWNLOAD_INNOVATION_CRITERIA.')';
        $ret[self::ACTION_PROMOTE_INNOVATION_VIEW] = 'ACTION_PROMOTE_INNOVATION_VIEW ('.self::ACTION_PROMOTE_INNOVATION_VIEW.')';
        $ret[self::ACTION_PROMOTE_INNOVATION_EXPORT] = 'ACTION_PROMOTE_INNOVATION_EXPORT ('.self::ACTION_PROMOTE_INNOVATION_EXPORT.')';
        $ret[self::ACTION_INNOVATION_SHARE] = 'ACTION_INNOVATION_SHARE ('.self::ACTION_INNOVATION_SHARE.')';
        $ret[self::ACTION_TEAM_INNOVATION_CREATE_RIGHT] = 'ACTION_TEAM_INNOVATION_CREATE_RIGHT ('.self::ACTION_TEAM_INNOVATION_CREATE_RIGHT.')';
        $ret[self::ACTION_TEAM_INNOVATION_UPDATE_RIGHT] = 'ACTION_TEAM_INNOVATION_UPDATE_RIGHT ('.self::ACTION_TEAM_INNOVATION_UPDATE_RIGHT.')';
        $ret[self::ACTION_TEAM_INNOVATION_DELETE_RIGHT] = 'ACTION_TEAM_INNOVATION_DELETE_RIGHT ('.self::ACTION_TEAM_INNOVATION_DELETE_RIGHT.')';
        $ret[self::ACTION_DOWNLOAD_ASSETS_PACK] = 'ACTION_DOWNLOAD_ASSETS_PACK ('.self::ACTION_DOWNLOAD_ASSETS_PACK.')';
        $ret[self::ACTION_DOWNLOAD_INNOVATION_LEAFLET] = 'ACTION_DOWNLOAD_INNOVATION_LEAFLET ('.self::ACTION_DOWNLOAD_INNOVATION_LEAFLET.')';
        $ret[self::ACTION_FEEDBACK_REQUEST] = 'ACTION_FEEDBACK_REQUEST ('.self::ACTION_FEEDBACK_REQUEST.')';
        $ret[self::ACTION_FEEDBACK_ANSWER] = 'ACTION_FEEDBACK_ANSWER ('.self::ACTION_FEEDBACK_ANSWER.')';
        $ret[self::ACTION_CANVAS_CREATED] = 'ACTION_CANVAS_CREATED ('.self::ACTION_CANVAS_CREATED.')';
        $ret[self::ACTION_CANVAS_UPDATED] = 'ACTION_CANVAS_UPDATED ('.self::ACTION_CANVAS_UPDATED.')';
        return $ret;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? 'Activity n°'.$this->getId() : 'New activity';
    }


    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
