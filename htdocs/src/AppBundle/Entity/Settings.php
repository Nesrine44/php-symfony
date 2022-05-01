<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
{
    
    const EXPORT_PROGRESS_ERROR = -1;
    const EXPORT_PROGRESS_MIN = 0;
    const EXPORT_PROGRESS_CREATED = 3;
    const EXPORT_PROGRESS_BEFORE_DATA_LOADED = 6;
    const EXPORT_PROGRESS_DATA_LAUNCH = 10;
    const EXPORT_PROGRESS_IN_LOOP = 88;
    const EXPORT_PROGRESS_MAX = 100;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_myportal_authentication_enabled", type="boolean")
     */
    private $is_myportal_authentication_enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     */
    private $contact_email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_https_enabled", type="boolean")
     */
    private $is_https_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_video_enabled", type="boolean")
     */
    private $is_video_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_tidio_chat_enabled", type="boolean")
     */
    private $is_tidio_chat_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_walkthrough_enabled", type="boolean")
     */
    private $is_walkthrough_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_maintenance_enabled", type="boolean")
     */
    private $is_maintenance_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_beta_enabled", type="boolean")
     */
    private $is_beta_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_data_capture_toast_enabled", type="boolean")
     */
    private $is_data_capture_toast_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_edition_quanti_enabled", type="boolean", nullable=true)
     */
    private $is_edition_quanti_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_edition_quali_enabled", type="boolean")
     */
    private $is_edition_quali_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_project_creation_enabled", type="boolean")
     */
    private $is_project_creation_enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_closed_message_enabled", type="boolean")
     */
    private $is_closed_message_enabled = false;

    /**
     * @var datetime
     *
     * @ORM\Column(name="ping", type="datetime", nullable=true)
     */
    private $ping;

    /**
     * @var string
     *
     * @ORM\Column(name="current_financial_date", type="string", length=255, nullable=true)
     */
    private $current_financial_date;

    /**
     * @var int
     *
     * @ORM\Column(name="current_trimester", type="integer")
     */
    private $current_trimester;

    /**
     * @var string
     *
     * @ORM\Column(name="open_date", type="string", length=255, nullable=true)
     */
    private $open_date;

    /**
     * @var string
     *
     * @ORM\Column(name="close_date", type="string", length=255, nullable=true)
     */
    private $close_date;

    /**
     * @var string
     *
     * @ORM\Column(name="open_date_libelle", type="string", length=255, nullable=true)
     */
    private $open_date_libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="close_date_libelle", type="string", length=255, nullable=true)
     */
    private $close_date_libelle;

    /**
     * @var int
     *
     * @ORM\Column(name="cache_version", type="integer", nullable=true)
     */
    private $cache_version = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="last_ns_group", type="integer", nullable=true)
     */
    private $last_ns_group = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_promote_innovation_emails_enabled", type="boolean")
     */
    private $is_promote_innovation_emails_enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="notifier_email", type="string", length=255, nullable=true)
     */
    private $notifier_email;


    /**
     * @var string
     *
     * @ORM\Column(name="developer_email", type="string", length=255, nullable=true)
     */
    private $developer_email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_emails_sent_to_developer_enabled", type="boolean")
     */
    private $is_emails_sent_to_developer_enabled = false;

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
     * Set contactEmail.
     *
     * @param string|null $contactEmail
     *
     * @return Settings
     */
    public function setContactEmail($contactEmail = null)
    {
        $this->contact_email = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail.
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * Set isHttpsEnabled.
     *
     * @param bool $isHttpsEnabled
     *
     * @return Settings
     */
    public function setIsHttpsEnabled($isHttpsEnabled)
    {
        $this->is_https_enabled = $isHttpsEnabled;

        return $this;
    }

    /**
     * Get isHttpsEnabled.
     *
     * @return bool
     */
    public function getIsHttpsEnabled()
    {
        return $this->is_https_enabled;
    }

    /**
     * Set isVideoEnabled.
     *
     * @param bool $isVideoEnabled
     *
     * @return Settings
     */
    public function setIsVideoEnabled($isVideoEnabled)
    {
        $this->is_video_enabled = $isVideoEnabled;

        return $this;
    }

    /**
     * Get isVideoEnabled.
     *
     * @return bool
     */
    public function getIsVideoEnabled()
    {
        return $this->is_video_enabled;
    }

    /**
     * Set isTidioChatEnabled.
     *
     * @param bool $isTidioChatEnabled
     *
     * @return Settings
     */
    public function setIsTidioChatEnabled($isTidioChatEnabled)
    {
        $this->is_tidio_chat_enabled = $isTidioChatEnabled;

        return $this;
    }

    /**
     * Get isTidioChatEnabled.
     *
     * @return bool
     */
    public function getIsTidioChatEnabled()
    {
        return $this->is_tidio_chat_enabled;
    }

    /**
     * Set isMaintenanceEnabled.
     *
     * @param bool $isMaintenanceEnabled
     *
     * @return Settings
     */
    public function setIsMaintenanceEnabled($isMaintenanceEnabled)
    {
        $this->is_maintenance_enabled = $isMaintenanceEnabled;

        return $this;
    }

    /**
     * Get isMaintenanceEnabled.
     *
     * @return bool
     */
    public function getIsMaintenanceEnabled()
    {
        return $this->is_maintenance_enabled;
    }

    /**
     * Set isBetaEnabled.
     *
     * @param bool $isBetaEnabled
     *
     * @return Settings
     */
    public function setIsBetaEnabled($isBetaEnabled)
    {
        $this->is_beta_enabled = $isBetaEnabled;

        return $this;
    }

    /**
     * Get isBetaEnabled.
     *
     * @return bool
     */
    public function getIsBetaEnabled()
    {
        return $this->is_beta_enabled;
    }

    /**
     * Set isEditionQuantiEnabled.
     *
     * @param bool $isEditionQuantiEnabled
     *
     * @return Settings
     */
    public function setIsEditionQuantiEnabled($isEditionQuantiEnabled)
    {
        $this->is_edition_quanti_enabled = $isEditionQuantiEnabled;

        return $this;
    }

    /**
     * Get isEditionQuantiEnabled.
     *
     * @return bool
     */
    public function getIsEditionQuantiEnabled()
    {
        return $this->is_edition_quanti_enabled;
    }

    /**
     * Set isDataCaptureToastEnabled.
     *
     * @param bool $isDataCaptureToastEnabled
     *
     * @return Settings
     */
    public function setIsDataCaptureToastEnabled($isDataCaptureToastEnabled)
    {
        $this->is_data_capture_toast_enabled = $isDataCaptureToastEnabled;

        return $this;
    }

    /**
     * Get isDataCaptureToastEnabled.
     *
     * @return bool
     */
    public function getIsDataCaptureToastEnabled()
    {
        return $this->is_data_capture_toast_enabled;
    }

    /**
     * Set isEditionQualiEnabled.
     *
     * @param bool $isEditionQualiEnabled
     *
     * @return Settings
     */
    public function setIsEditionQualiEnabled($isEditionQualiEnabled)
    {
        $this->is_edition_quali_enabled = $isEditionQualiEnabled;

        return $this;
    }

    /**
     * Get isEditionQualiEnabled.
     *
     * @return bool
     */
    public function getIsEditionQualiEnabled()
    {
        return $this->is_edition_quali_enabled;
    }

    /**
     * Set isProjectCreationEnabled.
     *
     * @param bool $isProjectCreationEnabled
     *
     * @return Settings
     */
    public function setIsProjectCreationEnabled($isProjectCreationEnabled)
    {
        $this->is_project_creation_enabled = $isProjectCreationEnabled;

        return $this;
    }

    /**
     * Get isProjectCreationEnabled.
     *
     * @return bool
     */
    public function getIsProjectCreationEnabled()
    {
        return $this->is_project_creation_enabled;
    }

    /**
     * Set isClosedMessageEnabled.
     *
     * @param bool $isClosedMessageEnabled
     *
     * @return Settings
     */
    public function setIsClosedMessageEnabled($isClosedMessageEnabled)
    {
        $this->is_closed_message_enabled = $isClosedMessageEnabled;

        return $this;
    }

    /**
     * Get isClosedMessageEnabled.
     *
     * @return bool
     */
    public function getIsClosedMessageEnabled()
    {
        return $this->is_closed_message_enabled;
    }

    /**
     * Set ping.
     *
     * @param \DateTime|null $ping
     *
     * @return Settings
     */
    public function setPing($ping = null)
    {
        $this->ping = $ping;

        return $this;
    }

    /**
     * Get ping.
     *
     * @return \DateTime|null
     */
    public function getPing()
    {
        return $this->ping;
    }

    /**
     * Get ping timestamp.
     *
     * @return int|null
     */
    public function getPingTimestamp()
    {
        return ($this->ping) ? $this->ping->getTimestamp() : null;
    }

    /**
     * Update ping.
     *
     * @return Settings
     */
    public function updatePing()
    {
        $this->ping = new \DateTime();

        return $this;
    }

    /**
     * Set currentFinancialDate.
     *
     * @param string|null $currentFinancialDate
     *
     * @return Settings
     */
    public function setCurrentFinancialDate($currentFinancialDate = null)
    {
        $this->current_financial_date = $currentFinancialDate;

        return $this;
    }

    /**
     * Get currentFinancialDate.
     *
     * @return string|null
     */
    public function getCurrentFinancialDate()
    {
        return $this->current_financial_date;
    }

    /**
     * Set currentTrimester.
     *
     * @param int $currentTrimester
     *
     * @return Settings
     */
    public function setCurrentTrimester($currentTrimester)
    {
        $this->current_trimester = $currentTrimester;

        return $this;
    }

    /**
     * Get currentTrimester.
     *
     * @return int
     */
    public function getCurrentTrimester()
    {
        return $this->current_trimester;
    }

    /**
     * Set openDate.
     *
     * @param string|null $openDate
     *
     * @return Settings
     */
    public function setOpenDate($openDate = null)
    {
        $this->open_date = $openDate;

        return $this;
    }

    /**
     * Get openDate.
     *
     * @return string|null
     */
    public function getOpenDate()
    {
        return $this->open_date;
    }

    /**
     * Set closeDate.
     *
     * @param string|null $closeDate
     *
     * @return Settings
     */
    public function setCloseDate($closeDate = null)
    {
        $this->close_date = $closeDate;

        return $this;
    }

    /**
     * Get closeDate.
     *
     * @return string|null
     */
    public function getCloseDate()
    {
        return $this->close_date;
    }

    /**
     * Set openDateLibelle.
     *
     * @param string|null $openDateLibelle
     *
     * @return Settings
     */
    public function setOpenDateLibelle($openDateLibelle = null)
    {
        $this->open_date_libelle = $openDateLibelle;

        return $this;
    }

    /**
     * Get openDateLibelle.
     *
     * @return string|null
     */
    public function getOpenDateLibelle()
    {
        return $this->open_date_libelle;
    }

    /**
     * Set closeDateLibelle.
     *
     * @param string|null $closeDateLibelle
     *
     * @return Settings
     */
    public function setCloseDateLibelle($closeDateLibelle = null)
    {
        $this->close_date_libelle = $closeDateLibelle;

        return $this;
    }

    /**
     * Get closeDateLibelle.
     *
     * @return string|null
     */
    public function getCloseDateLibelle()
    {
        return $this->close_date_libelle;
    }

    /**
     * Set isMyportalAuthenticationEnabled.
     *
     * @param bool $isMyportalAuthenticationEnabled
     *
     * @return Settings
     */
    public function setIsMyportalAuthenticationEnabled($isMyportalAuthenticationEnabled)
    {
        $this->is_myportal_authentication_enabled = $isMyportalAuthenticationEnabled;

        return $this;
    }

    /**
     * Get isMyportalAuthenticationEnabled.
     *
     * @return bool
     */
    public function getIsMyportalAuthenticationEnabled()
    {
        return $this->is_myportal_authentication_enabled;
    }

    /**
     * Set cacheVersion.
     *
     * @param int|null $cacheVersion
     *
     * @return Settings
     */
    public function setCacheVersion($cacheVersion = null)
    {
        $this->cache_version = $cacheVersion;

        return $this;
    }

    /**
     * Get cacheVersion.
     *
     * @return int|null
     */
    public function getCacheVersion()
    {
        return $this->cache_version;
    }

    /**
     * Update cacheVersion.
     *
     * @return Settings
     */
    public function updateCacheVersion()
    {
        $this->cache_version = time();

        return $this;
    }

    /**
     * To string.
     * 
     * @return string
     */
    public function __toString()
    {
        return "Settings";
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'is_myportal_authentication_enabled' => $this->getIsMyportalAuthenticationEnabled(),
            'contact_email' => $this->getContactEmail(),
            'is_https_enabled' => $this->getIsHttpsEnabled(),
            'is_video_enabled' => $this->getIsVideoEnabled(),
            'is_tidio_chat_enabled' => $this->getIsTidioChatEnabled(),
            'is_walkthrough_enabled' => $this->getIsWalkthroughEnabled(),
            'is_maintenance_enabled' => $this->getIsMaintenanceEnabled(),
            'is_beta_enabled' => $this->getIsBetaEnabled(),
            'is_data_capture_toast_enabled' => $this->getIsDataCaptureToastEnabled(),
            'is_edition_quanti_enabled' => $this->getIsEditionQuantiEnabled(),
            'is_edition_quali_enabled' => $this->getIsEditionQualiEnabled(),
            'is_project_creation_enabled' => $this->getIsProjectCreationEnabled(),
            'is_closed_message_enabled' => $this->getIsClosedMessageEnabled(),
            'ping' => $this->getPing(),
            'current_financial_date' => $this->getCurrentFinancialDate(),
            'current_trimester' => $this->getCurrentTrimester(),
            'open_date' => $this->getOpenDate(),
            'close_date' => $this->getCloseDate(),
            'open_date_libelle' => $this->getOpenDateLibelle(),
            'close_date_libelle' => $this->getCloseDateLibelle(),
            'libelle_budget_next_year' => $this->getLibelleBudgetNextYear(),
            'libelle_current_le' => $this->getLibelleBudgetNextYear(null, true),
            'libelle_last_a' => $this->getLibelleLastA(null, true),
            'cache_version' => $this->getCacheVersion(),
            'main_classes' => '',
        );
    }

    /**
     * Get libelle budget next year
     * TODO : REFACTOR
     *
     * @param string|null $date
     * @param bool $proper
     * @return string
     */
    public function getLibelleBudgetNextYear($date = null, $proper = false)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        if ($month < 4) {
            $libelle = 'LE2_' . $year;
        } elseif ($month < 7) {
            $libelle = 'B' . ($year + 1) . '_initial';
        } elseif ($month < 10) {
            $libelle = 'B' . ($year + 1) . '_final';
        } else {
            $libelle = 'LE1_' . ($year + 1);
        }
        if ($proper) {
            return str_replace('final', '', str_replace('_', ' ', $libelle));
        }
        return $libelle;
    }

    /**
     * Get budget number next year.
     *
     * @param string|null $date
     * @return int
     */
    public function getBudgetNumberNextYear($date = null)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('Y'));
        $month = intval($the_date->format('n'));
        return ($month < 7) ? $year : $year + 1;
    }

    /**
     *  Get libelle budget current year
     *
     * @param string|null $date
     * @return string
     */
    public function getLibelleBudgetCurrentYear($date = null)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        return ($month < 7) ? 'A' . ($year - 1) : 'A'.$year;
    }

    /**
     * Get latest estimate libelle.
     * TODO : REFACTOR
     *
     * @param null $date
     * @param bool $with_b
     * @return null|string
     */
    public function getLatestEstimateLibelle($date = null, $with_b = true)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $futur_year = $year + 1;

        $year_libelle = substr($year, -2);
        $futur_year_libelle = substr($futur_year, -2);

        if ($month < 4) {
            $nb_trimestre = 3;
        } elseif ($month < 7) {
            $nb_trimestre = 4;
        } elseif ($month < 10) {
            $nb_trimestre = 1;
        } else {
            $nb_trimestre = 2;
        }
        switch ($nb_trimestre) {
            case 1:
                if (!$with_b) {
                    return null;
                }
                return 'B' . $futur_year_libelle . '_final';
            case 2:
                return 'LE1_' . $futur_year_libelle;
            case 3:
                return 'LE2_' . $year_libelle;
            case 4:
                return 'LE3_' . $year_libelle;
            default:
                return null;
        }
    }

    /**
     * Get latest estimate budget libelle.
     *
     * @param string|null $date
     * @param bool $with_post_string
     * @return null|string
     */
    public function getLatestEstimateBudgetLibelle($date = null, $with_post_string = true)
    {
        if (!$date) {
            $date = $this->getCurrentFinancialDate();
        }
        $the_date = new \DateTime($date);
        $year = intval($the_date->format('y'));
        $futur_year = $year + 1;

        $year_libelle = substr($year, -2);
        $futur_year_libelle = substr($futur_year, -2);

        $nb_trimestre = $this->getTrimesterByFinancialDate($date);
        $post_string = '';
        if($with_post_string){
            $post_string = ($nb_trimestre == 4) ? '_initial' : '_final';
        }
        switch ($nb_trimestre) {
            case 1:
                return 'B' . $futur_year_libelle . $post_string;
            case 2:
                return 'B' . $futur_year_libelle . $post_string;
            case 3:
                return 'B' . $year_libelle . $post_string;
            case 4:
                return 'B' . $year_libelle . $post_string;
            default:
                return null;
        }
    }

    /**
     * Get libelle last a.
     *
     * @param string|null $date
     * @param bool $last_writed
     * @return int|string
     */
    public function getLibelleLastA($date = null, $last_writed = false)
    {
        if (!$date) {
            $date = $this->getCurrentFinancialDate();
        }
        $date_explode = explode('-', $date);
        $date_Y = intval($date_explode[0]);
        $date_y = $date_Y - 2000;
        $trimestre = $this->current_trimester;
        if ($trimestre < 3) {
            $year = $date_y;
        } else {
            $year = $date_y - 1;
        }
        $last_a = 'A' . $year;
        if ($last_writed) {
            $fields = $this->getFinancialDataPostFields($date, true, true);
            foreach ($fields as $key => $value) {
                $first_letter = substr($key, 0, 1);
                if ($first_letter == 'A') {
                    $last_a = $key;
                }
            }
        }
        return $last_a;
    }

    /**
     * Get last a date.
     *
     * @param string|null $date
     * @return int|string
     */
    public function getLastADate($date = null)
    {
        if (!$date) {
            $date = $this->getCurrentFinancialDate();
        }
        $date_explode = explode('-', $date);
        $date_Y = intval($date_explode[0]);
        $year = $date_Y;
        $trimestre = $this->current_trimester;
        if ($trimestre >= 3) {
            $year = $date_Y - 1;
        }
        return $year.'-06-30';
    }

    /**
     * Get libelle last estimate next year.
     * 
     * @param string|null $date
     * @return string
     */
    public function getLibelleLastEstimateNextYear($date = null)
    {
        if (!$date) {
            $the_date = new \DateTime($this->getCurrentFinancialDate());
        } else {
            $the_date = new \DateTime($date);
        }
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        if ($month < 4) {
            return 'LE2_' . $year;
        } elseif ($month < 7) {
            return 'LE3_' . $year;
        } elseif ($month < 10) {
            return 'B' . ($year + 1) . '_final';
        } else {
            return 'LE1_' . ($year + 1);
        }
    }

    /**
     * Get financial data post fields
     * TODO : REFACTOR
     *
     * @param string|null $date
     * @param bool $edition
     * @param bool $add_NA_fields
     * @param bool $add_previous_helper_field
     * @return array|null
     */
    public function getFinancialDataPostFields($date = null, $edition = true, $add_NA_fields = false, $add_previous_helper_field = false)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $ret = null;
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $previous_year = $year - 1;
        $previous_of_previous_year = $year - 2;
        $futur_year = $year + 1;

        $year_libelle = substr($year, -2);
        $previous_year_libelle = substr($previous_year, -2);
        $previous_of_previous_year_libelle = substr($previous_of_previous_year, -2);
        $futur_year_libelle = substr($futur_year, -2);

        $disabled_mode = ($edition == false);
        if ($month < 4) {
            $nb_trimestre = 3;
        } elseif ($month < 7) {
            $nb_trimestre = 4;
        } elseif ($month < 10) {
            $nb_trimestre = 1;
        } else {
            $nb_trimestre = 2;
        }
        switch ($nb_trimestre) {
            case 1:
                $ret = array();
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '0');
                $ret['A' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '1');
                if ($add_NA_fields) {
                    $ret['N/A'] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                    $ret['B' . $futur_year_libelle . ' (final)'] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                } else {
                    $ret['B' . $futur_year_libelle . ' (final)'] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '2');
                }
                break;
            case 2:
                $ret = array(
                    'A' . $previous_year_libelle => array('disabled' => true, 'mandatory' => false, 'position' => '0'),
                    'A' . $year_libelle => array('disabled' => false, 'mandatory' => false, 'position' => '1'),
                    'B' . $futur_year_libelle . ' (final)' => array('disabled' => false, 'mandatory' => false, 'position' => '2', 'libelle' => 'B' . $futur_year_libelle),
                    'LE1 ' . $futur_year_libelle => array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3'),
                );
                break;
            case 3:
                $ret = array();
                $ret['A' . $previous_of_previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '0');
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '1');
                $ret['B' . $year_libelle . ' (final)'] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                if ($add_previous_helper_field) {
                    $ret['LE1 ' . $year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '3');
                    $ret['LE2 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '4');
                } else {
                    $ret['LE2 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                }
                break;
            case 4:
                $ret = array();
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'mandatory' => false, 'position' => '0');
                $ret['B' . $year_libelle . ' (final)'] = array('disabled' => true, 'mandatory' => false, 'position' => '1');
                if ($add_previous_helper_field) {
                    $ret['LE2 ' . $year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                    $ret['LE3 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                    $ret['B' . $futur_year_libelle . ' (initial)'] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '4');
                } else {
                    $ret['LE3 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '2');
                    $ret['B' . $futur_year_libelle . ' (initial)'] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '3');
                }
                break;
        }

        return $ret;
    }

    /**
     * getFinancialDataTableDates
     *
     * @param $date
     * @param $add_previous_helper_field
     * @return array
     */
    public function getFinancialDataTableDates($date = null, $add_previous_helper_field = true){
        $post_fields = $this->getFinancialDataPostFields($date, true, false, $add_previous_helper_field);
        return array_keys($post_fields);
    }

    /**
     * Get financial data table fields for stage.
     *
     * @param Innovation $innovation
     * @param null $date
     * @param bool $add_previous_helper_field
     * @param bool $is_a_service
     * @return array
     */
    public function getFinancialDataTableFieldsForInnovation($innovation, $date = null, $add_previous_helper_field = true)
    {
        $current_stage = ($innovation->getStage()) ? $innovation->getStage()->getCssClass() : 'empty';
        $is_a_service = $innovation->isAService();
        $is_a_nba = $innovation->isANewBusinessAcceleration();
        $ret = array(
            'year' => $this->getFinancialDataTableDates($date)
        );
        $post_fields = $this->getFinancialDataPostFields($date, true, false, $add_previous_helper_field);
        if($is_a_nba){
            $ret['investment'] = array();
            $ret['revenue'] = array();
        }else {
            if (!$is_a_service && !in_array($current_stage, ['discover', 'ideate'])) {
                $ret['volume'] = array();
                if ($current_stage != 'experiment') {
                    $ret['net_sales'] = array();
                    $ret['contributing_margin'] = array();
                }
            }
            $ret['central_investment'] = array();
            if ($is_a_service || !in_array($current_stage, ['discover', 'ideate'])) {
                $ret['advertising_promotion'] = array();
            }
            if (!$is_a_service && !in_array($current_stage, ['discover', 'ideate', 'experiment'])) {
                $ret['cogs'] = array();
            }
        }

        foreach ($ret as $key => $value){
            if($key != 'year') {
                foreach ($post_fields as $post_libelle => $field) {
                    $libelle = $key . '_' . $post_libelle;
                    $id = FinancialData::cleanFieldLibelle($libelle);
                    $ret[$key][] = array('libelle' => $libelle, 'id' => $id, 'type' => 'td', 'infos' => $field, 'placeholder' => $this->getFinancialDataPlaceholder($key, $post_libelle));
                }
            }
        }
        return $ret;
    }

    /**
     * Get financial data fields for stage.
     * TODO : REFACTOR
     *
     * @param $current_stage
     * @param null $date
     * @param bool $edition
     * @param bool $add_NA_fields
     * @param bool $add_previous_helper_field
     * @param bool $is_a_service
     * @param bool $is_a_nba
     * @return array
     */
    public function getFinancialDataFieldsForStage($current_stage, $date = null, $edition = true, $add_NA_fields = false, $add_previous_helper_field = false, $is_a_service = false, $is_a_nba = false)
    {
        $ret = array(
            'to_display_list' => array(),
            'list' => array(),
            'details' => array(),
        );
        $post_fields = $this->getFinancialDataPostFields($date, $edition, $add_NA_fields, $add_previous_helper_field);
        if($is_a_nba){
            $pre_fields = array('investment','revenue');
        }else if($is_a_service){
            $pre_fields = array('central_investment','advertising_promotion');
        }elseif ($current_stage == 'ideate' || $current_stage == 'discover') {
            $pre_fields = array('central_investment');
        } elseif ($current_stage == 'experiment') {
            $pre_fields = array('volume', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'incubate') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'scale_up') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'permanent_range') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'discontinued') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } else {
            return $ret;
        }

        foreach ($post_fields as $post_libelle => $field) {
            $tr_libelle = (array_key_exists('libelle', $field)) ? $field['libelle'] : $post_libelle;
            $libelle = $post_libelle;
            $id = str_replace(' ', '_', $libelle);
            $id = str_replace('(', '', $id);
            $id = str_replace(')', '', $id);
            $ret['to_display_list'][] = array('libelle' => $tr_libelle, 'type' => 'tr', 'id' => $id);
            foreach ($pre_fields as $pre_field) {
                $libelle = $pre_field . '_' . $post_libelle;
                $id = str_replace(' ', '_', $libelle);
                $id = str_replace('(', '', $id);
                $id = str_replace(')', '', $id);
                $ret['list'][] = $id;
                $ret['to_display_list'][] = array('libelle' => $libelle, 'id' => $id, 'type' => 'td', 'infos' => $field, 'placeholder' => $this->getFinancialDataPlaceholder($pre_field, $post_libelle));
                $ret['details'][$libelle] = $field;
            }
        }
        return $ret;
    }

    /**
     * Get next financial date
     * 
     * @return string
     */
    public function getNextFinancialDate()
    {
        $current_date = $this->getCurrentFinancialDate();
        $current_trimester = $this->getCurrentTrimester();
        $trimester = $current_trimester + 1;
        if($trimester >= 5){
            $trimester = 1;
        }
        return $this->getFinancialDateByTrimester($current_trimester, $trimester, $current_date);
    }

    /**
     * Get previous financial date
     *
     * @return string
     */
    public function getPreviousFinancialDate()
    {
        $current_date = $this->getCurrentFinancialDate();
        $current_trimester = $this->getCurrentTrimester();
        $trimester = $current_trimester - 1;
        if($trimester <= 0){
            $trimester = 4;
        }
        return $this->getFinancialDateByTrimester($current_trimester, $trimester, $current_date);
    }

    /**
     * Get financial date by trimester.
     *
     * @param int $old_trimester
     * @param int $trimester
     * @param string|null $financial_date
     * @return string
     */
    public function getFinancialDateByTrimester($old_trimester, $trimester, $financial_date = null){
        if(!$financial_date){
            $financial_date = $this->getCurrentFinancialDate();
        }
        $date_explode = explode('-', $financial_date);
        $year = intval($date_explode[0]);
        if ($trimester == 3) {
            $dateM = '01';
            if($old_trimester == 2){
                $year += 1;
            }
        } else if ($trimester == 4) {
            $dateM = '04';
        } else if ($trimester == 1) {
            $dateM = '07';
        } else {
            $dateM = 10;
            if($old_trimester == 3){
                $year -= 1;
            }
        }
        return $year . '-' . $dateM . '-15';
    }

    /**
     * Get financial data placeholder.
     * 
     * @param string $pre_libelle
     * @param string $post_libelle
     * @return string
     */
    public function getFinancialDataPlaceholder($pre_libelle, $post_libelle)
    {
        $post_libelle = str_replace(' (final)', '', $post_libelle);
        $post_libelle = str_replace(' (initial)', '', $post_libelle);
        if ($pre_libelle == 'volume') {
            return 'V ' . $post_libelle;
        } elseif ($pre_libelle == 'net_sales') {
            return 'NS ' . $post_libelle;
        } elseif ($pre_libelle == 'contributing_margin') {
            return 'CM ' . $post_libelle;
        } elseif ($pre_libelle == 'central_investment') {
            return 'CI ' . $post_libelle;
        } elseif ($pre_libelle == 'advertising_promotion') {
            return 'AP ' . $post_libelle;
        } elseif ($pre_libelle == 'caap') {
            return 'CAAP ' . $post_libelle;
        } elseif ($pre_libelle == 'investment') {
            return 'I ' . $post_libelle;
        } elseif ($pre_libelle == 'revenue') {
            return 'R ' . $post_libelle;
        } elseif ($pre_libelle == 'cogs') {
            return 'COGS ' . $post_libelle;
        } else {
            return $pre_libelle . ' ' . $post_libelle;
        }
    }

    /**
     * Get current trimester by financial date.
     *
     * @return int
     */
    public function getTrimesterByFinancialDate($date = null){
        if($date){
            $date_explode = explode('-', $date);
            $dateM = $date_explode[1];
            if($dateM == '01'){
                return 3;
            }elseif($dateM == '04'){
                return 4;
            }elseif($dateM == '07'){
                return 1;
            }else{
                return 2;
            }
        }else{
            return $this->getCurrentTrimester();
        }
    }

    /**
     * Get budget libelle for date.
     *
     * @param string|null $el_date
     * @param string $id
     * @return string
     */
    public function getBudgetLibelleForDate($el_date = null, $id = "")
    {
        $the_date = ($el_date) ? $el_date : $this->getCurrentFinancialDate();
        $nb_trimestre = $this->getTrimesterByFinancialDate($the_date);
        $the_id = str_replace('B', '', $id);
        $special_case = ($the_id && intval($the_id) <= intval(date('y')));
        switch ($nb_trimestre) {
            case 1:
                return '_final';
            case 2:
                return '_final';
            case 3:
                return '_final';
            case 4:
                return ($special_case) ? '_final' : '_initial';
            default:
                return '';
        }
    }


    /**
     * Get financials libelles for explore volumes.
     *
     * @return array
     */
    function getFinancialsLibellesForExploreVolumes()
    {
        $ret = array();
        $start_date_year = 15;
        $the_date = $this->getCurrentFinancialDate();
        $last_a = $this->getLibelleLastA(null, true);
        $max_year = intval(str_replace('A', '', $last_a));
        for ($year = $start_date_year; $year < $max_year; $year++) {
            $ret[] = 'A' . $year;
        }
        if (!in_array($last_a, $ret)) {
            $ret[] = $last_a;
        }
        $last_b = $this->getLatestEstimateBudgetLibelle($the_date, true);
        if ($last_b) {
            $ret[] = $last_b;
        }
        $last_le = $this->getLatestEstimateLibelle($the_date, false);
        if ($last_le) {
            $ret[] = $last_le;
        }
        return $ret;
    }

    /**
     * Get percent diff between two values.
     *
     * @param int|float $oldValue
     * @param int|float $newValue
     * @return int|string
     */
    public static function getPercentDiffBetweenTwoValues($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return 0;
        }
        if($newValue == 0){
           return -100;
        }
        $denominator = ($oldValue < 0 && $newValue >= 0) ? abs($oldValue) : $oldValue;
        $percentChange = (($newValue - $oldValue) / $denominator) * 100;
        return number_format($percentChange, 0, '.', '');
    }

    /**
     * Get percent evolution between two values.
     *
     * @param int|float $oldValue
     * @param int|float $newValue
     * @return int|string
     */
    public static function getPercentEvolutionBetweenTwoValues($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return 0;
        }
        $percentChange = (($newValue - $oldValue) / abs($oldValue)) * 100;
        return number_format($percentChange, 0);
    }

    /**
     * Get percent total between two values.
     *
     * @param int|float $value
     * @param int|float $total
     * @return int|string
     */
    public static function getPercentTotalBetweenTwoValues($value, $total)
    {
        if ($value == 0 || $total == 0) {
            return 0;
        }
        return number_format(($value / $total) * 100, 0);
    }

    /**
     * Special round by 3.
     *
     * @param $nb
     * @return float
     */
    public static function specialRoundBy3($nb)
    {
        if ($nb < 10) {
            return round($nb, 2);
        } elseif ($nb < 100) {
            return round($nb, 1);
        } else {
            return round($nb);
        }
    }

    /**
     * Get proper to display libelle.
     *
     * @param string $key
     * @return string
     */
    public static function getProperToDisplayLibelle($key)
    {
        $key = str_replace(' (final)', '', $key);
        $key = str_replace(' (initial)', '', $key);
        return str_replace('_', ' ', $key);
    }

    /**
     * Return proper js string
     * 
     * @param string $string
     * @param bool $remove_spaces_and_slash
     * @return string
     */
    public static function returnProperJsString($string, $remove_spaces_and_slash = false)
    {
        $proper = strtolower($string);
        $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'a', 'Ä' => 'A', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'o', 'Ð' => 'D', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'o', 'Ø' => 'O', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'O', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'ß', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'þ', 'Þ' => 'Þ', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'Д', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'Щ', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'Ю', 'я' => 'я', 'Я' => 'Я');
        $ret = strtolower(str_replace(array_keys($transliterationTable), array_values($transliterationTable), $proper));
        if($remove_spaces_and_slash){
            $ret = trim(str_replace('/', '', $ret));
            $ret = preg_replace('/\s+/', ' ',$ret);
            $ret = str_replace(' ', '_', $ret);
        }
        return $ret;
    }


    /**
     * Is open date?
     *
     * @param string|null $date
     * @return bool
     */
    public function isOpenDate($date = null)
    {
        if ($date) {
            $date_now = $date;
        } else {
            $date_now = date('Y-m-d');
        }
        $open_date =  $this->getOpenDate();
        $closure_date =  $this->getCloseDate();
        if(!$open_date || $closure_date){
            return false;
        }
        if ($date_now < $open_date || $date_now > $closure_date) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Is date current exercise.
     *
     * @param string $date_str
     * @return bool
     */
    public function isDateCurrentExercise($date_str)
    {

        return ($date_str == $this->getCurrentFinancialDate());
    }


    /**
     * Get libelle last B for excel export.
     *
     * @param null $date
     * @return string
     */
    public function getLibelleLastBForExcelExport($date = null)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        if ($month < 4) {
            return 'LE2 ' . $year;
        } elseif ($month < 7) {
            return 'iB' . ($year + 1);
        } elseif ($month < 10) {
            return 'fB' . ($year + 1);
        } else {
            return 'LE1 ' . ($year + 1);
        }
    }

    /**
     * Get financial date libelle.
     * 
     * @param null $date
     * @return string
     */
    function getFinancialDateLibelle($date = null)
    {
        if (!$date) {
            $the_date = new \DateTime($this->current_financial_date);
        } else {
            $the_date = new \DateTime($date);
        }
        $the_date->setTimezone(new \DateTimeZone('GMT'));
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $futur_year = $year + 1;

        if ($month < 4) {
            return 'LE2 ' . $year;
        } elseif ($month < 7) {
            return 'LE3 ' . $year;
        } elseif ($month < 10) {
            return 'B' . $futur_year;
        } else {
            return 'LE1 ' . $futur_year;
        }
    }

    /**
     * Get all financial date libelles for data.
     *
     * @param $el_date
     * @param bool $all_loop
     * @return array
     */
    public function getAllFinancialDateLibellesForData($el_date, $all_loop = false)
    {
        $ret_dates = array();
        $last_a = $this->getLibelleLastA($el_date, true);
        $max_year = intval(str_replace('A', '', $last_a));
        for ($year = 15; $year < $max_year; $year++) {
            $ret_dates[] = 'A' . $year;
        }
        if (!in_array($last_a, $ret_dates)) {
            $ret_dates[] = $last_a;
        }
        $fields = $this->getFinancialDataPostFields($el_date, true, true);
        foreach ($fields as $key => $value) {
            $key = str_replace(' (final)', '', $key);
            $key = str_replace(' (initial)', '', $key);
            if (!in_array($key, $ret_dates)) {
                $ret_dates[] = $key;
            }
        }
        if (!$all_loop) {
            return $ret_dates;
        }
        $vsly_libelle = "VsLY";
        $vsly_pre_indexes = array('Vol', 'NS', 'CM', '', 'CAAP');
        $ret_dates[] = $vsly_libelle;
        $all_loop_ret = array();
        $pre_indexes = array('Vol', 'NS', 'CM', 'A&P', 'CI', '', 'CAAP');
        foreach ($pre_indexes as $pre_index) {
            foreach ($ret_dates as $ret_date) {
                if ($ret_date == $vsly_libelle) {
                    if (in_array($pre_index, $vsly_pre_indexes)) {
                        $all_loop_ret[] = $ret_date;
                    }
                } elseif ($ret_date != 'N/A') {
                    $all_loop_ret[] = $pre_index . ' ' . $ret_date;
                }
            }
        }
        return $all_loop_ret;
    }

    /**
     * Set lastNsGroup.
     *
     * @param int|null $lastNsGroup
     *
     * @return Settings
     */
    public function setLastNsGroup($lastNsGroup = null)
    {
        $this->last_ns_group = $lastNsGroup;

        return $this;
    }

    /**
     * Get lastNsGroup.
     *
     * @return int|null
     */
    public function getLastNsGroup()
    {
        return $this->last_ns_group;
    }

    /**
     * Get lastNsGroup.
     *
     * @param User $user
     * @return int|null
     */
    public function getLastNsGroupForUser($user)
    {
        if($user->hasMonitorAccess()) {
            return $this->last_ns_group;
        }
        return 'N/A';
    }



    /**
     * Set isPromoteInnovationEmailsEnabled.
     *
     * @param bool $isPromoteInnovationEmailsEnabled
     *
     * @return Settings
     */
    public function setIsPromoteInnovationEmailsEnabled($isPromoteInnovationEmailsEnabled)
    {
        $this->is_promote_innovation_emails_enabled = $isPromoteInnovationEmailsEnabled;

        return $this;
    }

    /**
     * Get isPromoteInnovationEmailsEnabled.
     *
     * @return bool
     */
    public function getIsPromoteInnovationEmailsEnabled()
    {
        return $this->is_promote_innovation_emails_enabled;
    }

    /**
     * Set developerEmail.
     *
     * @param string|null $developerEmail
     *
     * @return Settings
     */
    public function setDeveloperEmail($developerEmail = null)
    {
        $this->developer_email = $developerEmail;

        return $this;
    }

    /**
     * Get developerEmail.
     *
     * @return string|null
     */
    public function getDeveloperEmail()
    {
        return $this->developer_email;
    }

    /**
     * Set isEmailsSentToDeveloperEnabled.
     *
     * @param bool $isEmailsSentToDeveloperEnabled
     *
     * @return Settings
     */
    public function setIsEmailsSentToDeveloperEnabled($isEmailsSentToDeveloperEnabled)
    {
        $this->is_emails_sent_to_developer_enabled = $isEmailsSentToDeveloperEnabled;

        return $this;
    }

    /**
     * Get isEmailsSentToDeveloperEnabled.
     *
     * @return bool
     */
    public function getIsEmailsSentToDeveloperEnabled()
    {
        return $this->is_emails_sent_to_developer_enabled;
    }

    /**
     * Set notifierEmail.
     *
     * @param string|null $notifierEmail
     *
     * @return Settings
     */
    public function setNotifierEmail($notifierEmail = null)
    {
        $this->notifier_email = $notifierEmail;

        return $this;
    }

    /**
     * Get notifierEmail.
     *
     * @return string|null
     */
    public function getNotifierEmail()
    {
        return $this->notifier_email;
    }

    /**
     * Get website base url.
     *
     * @return string
     */
    public static function getWebsiteBaseUrl(){
        if(array_key_exists('CURRENT_MODE', $_ENV) && $_ENV['CURRENT_MODE'] == 'dev'){
            return "http://127.0.0.1";
        }
        if($_ENV['CURRENT_PLATFORM'] == 'staging'){
             return "https://innovation-staging.pernod-ricard.io";
        }
        return 'https://innovation.pernod-ricard.com';
    }

    /**
     * Set isWalkthroughEnabled.
     *
     * @param bool $isWalkthroughEnabled
     *
     * @return Settings
     */
    public function setIsWalkthroughEnabled($isWalkthroughEnabled)
    {
        $this->is_walkthrough_enabled = $isWalkthroughEnabled;

        return $this;
    }

    /**
     * Get isWalkthroughEnabled.
     *
     * @return bool
     */
    public function getIsWalkthroughEnabled()
    {
        return $this->is_walkthrough_enabled;
    }


    /**
     * Get Xss Clean String
     * @param $string
     * @return string
     */
    public static function getXssCleanString($string){
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
