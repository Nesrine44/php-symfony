<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrAuthBundle\Model\PrAuthUser;


/**
 * @ORM\Entity
 * @ORM\Table(name="pr_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends PrAuthUser
{
    const DEFAULT_USER_PICTURE = '/images/default/user.png';


    const ROLE_USER = 'ROLE_USER';
    const ROLE_MANAGEMENT = 'ROLE_MANAGEMENT';
    const ROLE_MD = 'ROLE_MD';
    const ROLE_HQ = 'ROLE_HQ';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    const PROPER_ROLE_USER = 'User';
    const PROPER_ROLE_MANAGEMENT = 'Management';
    const PROPER_ROLE_MD = 'Managing Director';
    const PROPER_ROLE_HQ = 'HQ';
    const PROPER_ROLE_SUPER_ADMIN = 'Developer';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $old_id;

    /**
     * @ORM\OneToMany(targetEntity="UserInnovationRight", cascade={"ALL"}, mappedBy="user")
     */
    protected $user_innovation_rights;

    /**
     * @ORM\ManyToOne(targetEntity="Entity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private $entity;

    /**
     * @ORM\ManyToOne(targetEntity="EntityGroup")
     * @ORM\JoinColumn(name="entity_group_id", referencedColumnName="id")
     */
    private $perimeter;


    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user")
     */
    protected $activities;

    /**
     * @ORM\OneToMany(targetEntity="SearchHistory", mappedBy="user")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $search_histories;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accept_newsletter", type="boolean")
     */
    private $accept_newsletter = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_popup_enabled", type="boolean")
     */
    private $is_popup_enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_video_enabled", type="boolean")
     */
    private $is_video_enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_seen_walkthrough", type="boolean")
     */
    private $has_seen_walkthrough = false;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @var string
     *
     * @ORM\Column(name="situation", type="string", length=255, nullable=true)
     */
    private $situation;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;


    /**
     * @ORM\ManyToOne(targetEntity="UserEntity")
     * @ORM\JoinColumn(name="user_entity_id", referencedColumnName="id")
     */
    private $user_entity;


    /**
     * @ORM\OneToMany(targetEntity="FeedbackInvitation", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $feedback_invitations;

    /**
     * @ORM\OneToMany(targetEntity="Innovation", mappedBy="contact", cascade={"persist"})
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $own_innovations;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accept_scheduled_emails", type="boolean")
     */
    private $accept_scheduled_emails = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accept_contact", type="boolean")
     */
    private $accept_contact = true;


    /**
     * @ORM\OneToMany(targetEntity="UserSkill", mappedBy="user")
     * @ORM\OrderBy({"created_at" = "ASC"})
     */
    protected $user_skills;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add userInnovationRight.
     *
     * @param \AppBundle\Entity\UserInnovationRight $userInnovationRight
     *
     * @return User
     */
    public function addUserInnovationRight(\AppBundle\Entity\UserInnovationRight $userInnovationRight)
    {
        $this->user_innovation_rights[] = $userInnovationRight;

        return $this;
    }

    /**
     * Remove userInnovationRight.
     *
     * @param \AppBundle\Entity\UserInnovationRight $userInnovationRight
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUserInnovationRight(\AppBundle\Entity\UserInnovationRight $userInnovationRight)
    {
        return $this->user_innovation_rights->removeElement($userInnovationRight);
    }

    /**
     * Get userInnovationRights.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserInnovationRights()
    {
        return $this->user_innovation_rights;
    }

    /**
     * Add activity.
     *
     * @param \AppBundle\Entity\Activity $activity
     *
     * @return User
     */
    public function addActivity(\AppBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity.
     *
     * @param \AppBundle\Entity\Activity $activity
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeActivity(\AppBundle\Entity\Activity $activity)
    {
        return $this->activities->removeElement($activity);
    }

    /**
     * Get activities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }


    /**
     * Set entity.
     *
     * @param \AppBundle\Entity\Entity|null $entity
     *
     * @return User
     */
    public function setEntity(\AppBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity.
     *
     * @return \AppBundle\Entity\Entity|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set oldId.
     *
     * @param int $oldId
     *
     * @return User
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
     * Set accept_newsletter.
     *
     * @param bool $isNewsletterAccepted
     *
     * @return User
     */
    public function setAcceptNewsletter($acceptNewsletter)
    {
        $this->accept_newsletter = $acceptNewsletter;

        return $this;
    }

    /**
     * Get accept_newsletter.
     *
     * @return bool
     */
    public function getAcceptNewsletter()
    {
        return $this->accept_newsletter;
    }

    /**
     * Set isVideoEnabled.
     *
     * @param bool $isVideoEnabled
     *
     * @return User
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
     * Set isPopupEnabled.
     *
     * @param bool $isPopupEnabled
     *
     * @return User
     */
    public function setIsPopupEnabled($isPopupEnabled)
    {
        $this->is_popup_enabled = $isPopupEnabled;

        return $this;
    }

    /**
     * Get isPopupEnabled.
     *
     * @return bool
     */
    public function getIsPopupEnabled()
    {
        return $this->is_popup_enabled;
    }


    public function setLastLogin(\DateTime $time = null)
    {
        return parent::setLastLogin($time); // TODO: Change the autogenerated stub
    }

    /**
     * Get generated local password.
     *
     * @param string|null $secret_key
     *
     * @return bool
     */
    public function getGeneratedLocalPassword($secret_key = null)
    {
        if ($secret_key && $this->getIsPrEmploye()) {
            return md5($secret_key . strtolower($this->getEmail()));
        }
        return "1pipo2";
    }

    /**
     * Get proper user role to display
     *
     * @return string
     */
    public function getProperUserRole()
    {
        if ($this->hasRole(self::ROLE_SUPER_ADMIN)) {
            return self::PROPER_ROLE_SUPER_ADMIN;
        } elseif ($this->hasRole(self::ROLE_HQ)) {
            return self::PROPER_ROLE_HQ;
        } elseif ($this->hasRole(self::ROLE_MANAGEMENT)) {
            return self::PROPER_ROLE_MANAGEMENT;
        }
        $role = $this->guessRoleByFeedbacks();
        return ($role) ? $role : '';
    }

    /**
     * Guess role by feedbacks.
     *
     * @return null
     */
    public function guessRoleByFeedbacks()
    {
        foreach ($this->getFeedbackInvitations() as $feedbackInvitation) {
            if ($feedbackInvitation->guessRole()) {
                return $feedbackInvitation->guessRole();
            }
        }
        return null;
    }

    /**
     * getProperRole
     *
     * @param bool $with_no_role
     * @return bool|string
     */
    public function getProperRole($with_no_role = false)
    {
        if ($this->hasRole(self::ROLE_SUPER_ADMIN)) {
            return self::PROPER_ROLE_SUPER_ADMIN;
        } elseif ($this->hasRole(self::ROLE_HQ)) {
            return self::PROPER_ROLE_HQ;
        } elseif ($this->hasRole(self::ROLE_MANAGEMENT)) {
            return self::PROPER_ROLE_MANAGEMENT;
        }
        return $this->getProperRoleByUserRoles($with_no_role);
    }

    /**
     * Get proper role by user_roles.
     *
     * @param bool $with_no_role
     * @return bool|string
     */
    public function getProperRoleByUserRoles($with_no_role = false)
    {
        $user_roles = $this->getUserInnovationRightRolesArray();
        if (in_array('entity_leader', $user_roles)) {
            return 'Entity leader';
        } elseif (in_array('financial_leader', $user_roles)) {
            return 'Financial leader';
        } elseif (in_array('team_leader', $user_roles)) {
            return 'Team Leader';
        } elseif (in_array(UserInnovationRight::ROLE_RESEARCH_AND_DEVELOPMENT, $user_roles)) {
            return UserInnovationRight::ROLE_RESEARCH_AND_DEVELOPMENT;
        } elseif (in_array(UserInnovationRight::ROLE_OPERATIONS, $user_roles)) {
            return UserInnovationRight::ROLE_OPERATIONS;
        } elseif (in_array(UserInnovationRight::ROLE_LEGAL, $user_roles)) {
            return UserInnovationRight::ROLE_LEGAL;
        } elseif (in_array(UserInnovationRight::ROLE_MANAGEMENT, $user_roles)) {
            return UserInnovationRight::ROLE_MANAGEMENT;
        } elseif (in_array(UserInnovationRight::ROLE_CONSUMER_INSIGHTS, $user_roles)) {
            return UserInnovationRight::ROLE_CONSUMER_INSIGHTS;
        } elseif (in_array(UserInnovationRight::ROLE_FINANCE_CONTACT, $user_roles)) {
            return UserInnovationRight::ROLE_FINANCE_CONTACT;
        } elseif (in_array(UserInnovationRight::ROLE_CONTACT_OWNER, $user_roles)) {
            return 'Maker (' . UserInnovationRight::ROLE_CONTACT_OWNER . ')';
        } elseif (in_array(UserInnovationRight::ROLE_OTHER, $user_roles)) {
            return 'Maker';
        } elseif (in_array('maker', $user_roles)) {
            return 'Maker';
        } elseif (in_array(UserInnovationRight::ROLE_CONTACT_OWNER, $user_roles)) {
            return 'Maker';
        } elseif ($with_no_role) {
            return 'NO ROLE';
        } else {
            return false;
        }
    }

    /**
     * getUserInnovationRightRolesArray
     *
     * @return array
     */
    public function getUserInnovationRightRolesArray()
    {
        $roles = array();
        foreach ($this->getUserInnovationRights() as $userInnovationRight) {
            $roles[] = $userInnovationRight->getUserRole();
        }
        return array_unique($roles);
    }

    /**
     * getUserInnovationRightRolesWriteArray
     *
     * @return array
     */
    public function getUserInnovationRightRolesWriteArray()
    {
        $roles = array();
        foreach ($this->getUserInnovationRights() as $userInnovationRight) {
            if ($userInnovationRight->getRight() == UserInnovationRight::RIGHT_WRITE) {
                $roles[] = $userInnovationRight->getUserRole();
            }
        }
        return array_unique($roles);
    }


    /**
     * Get picture url.
     *
     * @param int $size
     * @param string $default_img
     * @return string
     */
    public function getPictureUrl($size = 120, $default_img = self::DEFAULT_USER_PICTURE)
    {
        if (!$this->getIsPrEmploye()) {
            return $this->getGravatarPictureUrl($size, $default_img);
        } else {
            return "https://shark-api-v1.pernod-ricard.io/users/" . $this->getEmail() . "/photo";
        }
    }

    /**
     * Get gravatar picture url
     *
     * @param int $size
     * @param string $default_img
     * @return string
     */
    public function getGravatarPictureUrl($size = 120, $default_img = self::DEFAULT_USER_PICTURE)
    {
        $r = 'g';
        /*
         * TODO :
         * À changer avant la mise en prod
         * Je suis obligé de mettre une url valide car elle est envoyée à gravatar
         */
        $base_url = 'https://innovation.pernod-ricard.com';
        $email = $this->email;
        $default = urlencode($base_url . $default_img);
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$size&d=$default&r=$r";
        return $url;
    }

    /**
     * Has admin rights.
     *
     * @return bool
     */
    public function hasAdminRights()
    {
        return ($this->hasRole(self::ROLE_HQ) || $this->hasRole(self::ROLE_SUPER_ADMIN));
    }

    /**
     * Has developer rights.
     *
     * @return bool
     */
    public function hasDeveloperRights()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * Has management rights.
     *
     * @return bool
     */
    public function hasManagementRights()
    {
        return ($this->hasRole(self::ROLE_MANAGEMENT));
    }

    /**
     * Has no role.
     *
     * @return bool
     */
    public function hasNoRole()
    {
        if ($this->hasAdminRights() || $this->hasManagementRights() || $this->isManagingDirector()) {
            return false;
        }
        $user_roles = $this->getUserInnovationRightRolesArray();
        return (count($user_roles) == 0);
    }

    /**
     * has No Innovations.
     *
     * @return bool
     */
    public function hasNoInnovations()
    {
        if ($this->hasAdminRights()) {
            return false;
        }
        $user_roles = $this->getUserInnovationRightRolesWriteArray();
        return (count($user_roles) == 0);
    }

    /**
     * Has only role maker.
     *
     * @return bool
     */
    public function hasOnlyRoleMaker()
    {
        if ($this->hasAdminRights()) {
            return false;
        }
        $user_roles = $this->getUserInnovationRightRolesArray();
        return (count($user_roles) == 1 && $user_roles[0] == 'maker');
    }

    /**
     * Has manage access.
     *
     * @return bool
     */
    public function hasManageAccess()
    {
        if ($this->hasAdminRights()) { // || $this->hasManagementRights()
            return true;
        }
        return (count($this->getUserInnovationRights()) > 0);
    }

    /**
     * Has new business model access.
     *
     * @return bool
     */
    public function hasNewBusinessModelAccess()
    {
        if ($this->hasAdminRights() || $this->hasManagementRights()) {
            return true;
        }
        foreach ($this->getUserInnovationRights() as $userInnovationRight){
            /* @var Innovation $innovation */
            $innovation = $userInnovationRight->getInnovation();
            if(
                $innovation &&
                $innovation->isAService() &&
                $innovation->isANewBusinessAcceleration()
            ){
                return true;
            }
        }
        return false;
    }

    /**
     * Has monitor access.
     *
     * @return bool
     */
    public function hasMonitorAccess()
    {
        return ($this->hasAdminRights() || $this->hasManagementRights());
    }

    /**
     * Has access to innovation.
     *
     * @param Innovation $innovation
     * @return bool
     */
    public function hasAccessToInnovation($innovation)
    {
        if (!$this->hasManageAccess()) {
            return false;
        }
        if ($this->hasAdminRights() || $this->hasManagementRights()) {
            return true;
        }
        return $this->getUserInnovationRightForAnInnovation($innovation);
    }

    /**
     * Can edit this innovation.
     *
     * @param Innovation $innovation
     * @return bool
     */
    public function canEditThisInnovation($innovation)
    {
        if (!$this->hasManageAccess() || $this->hasManagementRights()) {
            return false;
        }
        if ($this->hasAdminRights()) {
            return true;
        }
        $userInnovationRight = $this->getUserInnovationRightForAnInnovation($innovation);
        if (!$userInnovationRight) {
            return false;
        }
        return $userInnovationRight->getRight() == UserInnovationRight::RIGHT_WRITE;
    }

    /**
     * is limited on innovation.
     *
     * @param array $innovation
     * @return bool
     */
    public function isLimitedOnInnovation($innovation)
    {
        if ($this->hasAdminRights() || $this->hasManagementRights()) {
            return false;
        }
        if (!$this->hasManageAccess()) {
            return true;
        }
        $userInnovationRight = $this->getUserInnovationRightForAnInnovationArray($innovation);
        if (!$userInnovationRight) {
            return true;
        }
        return ($userInnovationRight->getRight() != UserInnovationRight::RIGHT_WRITE);
    }

    /**
     * Get user_innovation_right for an innovation.
     *
     * @param Innovation $an_innovation
     * @return UserInnovationRight|null
     */
    public function getUserInnovationRightForAnInnovation($an_innovation)
    {
        foreach ($this->getUserInnovationRights() as $userInnovationRight) {
            if ($userInnovationRight->getInnovation()->getId() == $an_innovation->getId()) {
                return $userInnovationRight;
            }
        }
        return null;
    }

    /**
     * Get user_innovation_right for an innovation array.
     *
     * @param array $an_innovation_array
     * @return UserInnovationRight|null
     */
    public function getUserInnovationRightForAnInnovationArray($an_innovation_array)
    {
        foreach ($this->getUserInnovationRights() as $userInnovationRight) {
            if ($userInnovationRight->getInnovation()->getId() == $an_innovation_array['id']) {
                return $userInnovationRight;
            }
        }
        return null;
    }

    /**
     * hasRoleEntityTeamLeader
     *
     * @param Innovation|null $an_innovation
     * @return bool
     */
    public function hasRoleEntityTeamLeader($an_innovation = null)
    {
        if ($this->hasAdminRights() || $this->hasManagementRights()) {
            return true;
        }
        if ($an_innovation) {
            $user_innovation_right = $this->getUserInnovationRightForAnInnovation($an_innovation);
            if ($user_innovation_right) {
                return in_array($user_innovation_right->getUserRole(), array('entity_leader', 'team_leader', 'financial_leader'));
            }
        }
        $user_roles = $this->getUserInnovationRightRolesArray();
        return (in_array('entity_leader', $user_roles) || in_array('team_leader', $user_roles) || in_array('financial_leader', $user_roles));
    }

    /**
     * Get user rights array.
     *
     * @return array
     */
    public function getUserRightsArray()
    {
        $ret = array();
        foreach ($this->getUserInnovationRights() as $innovationRight) {
            if ($innovationRight->getInnovation()) {
                $ret[] = array(
                    'right' => $innovationRight->getRight(),
                    'innovation_id' => $innovationRight->getInnovation()->getId(),
                    'user_uid' => $this->getId(),
                    'role' => $innovationRight->getRole(),
                );
            }
        }
        return $ret;
    }

    /**
     * Get user roles array.
     *
     * @return array
     */
    public function getUserRolesArray()
    {
        $ret = array();
        if ($this->hasAdminRights()) {
            $ret[] = 'hq';
        }
        if ($this->hasDeveloperRights()) {
            $ret[] = 'dev';
        }
        if ($this->isManagingDirector()) {
            $ret[] = 'managing_director';
        }
        if ($this->hasManagementRights()) {
            $ret[] = 'management';
            $ret[] = 'readonly';
        }
        $result = array_merge($ret, $this->getUserInnovationRightRolesArray());
        return array_unique($result);
    }

    /**
     * Get filters
     *
     * @param $other_datas
     * @param $user_innovations_array
     * @param bool $for_monitor
     * @return array
     */
    public function getFilters($other_datas, $user_innovations_array, $for_monitor = false)
    {
        $is_admin = $this->hasAdminRights();
        $is_management = $this->hasManageAccess();
        $filters = array();
        $ret = array();

        if (!$other_datas) {
            return $ret;
        }

        if ($for_monitor && !$this->hasManageAccess()) {
            return $ret;
        }

        // current_stage
        if (!$for_monitor) {
            $ret['filter']['current_stage'] = array();
            $ret['order']['current_stage'] = null;
            $checked = (array_key_exists('current_stage', $filters));
            $ret['filter']['current_stage'] = array(
                array('id' => 1, 'libelle' => 'Discover', 'checked' => (!$checked || ($checked && in_array(1, $filters['current_stage']))) ? 'checked=""' : ''),
                array('id' => 2, 'libelle' => 'Ideate', 'checked' => (!$checked || ($checked && in_array(2, $filters['current_stage']))) ? 'checked=""' : ''),
                array('id' => 3, 'libelle' => 'Experiment', 'checked' => (!$checked || ($checked && in_array(3, $filters['current_stage']))) ? 'checked=""' : ''),
                array('id' => 4, 'libelle' => 'Incubate', 'checked' => (!$checked || ($checked && in_array(4, $filters['current_stage']))) ? 'checked=""' : ''),
                array('id' => 5, 'libelle' => 'Scale up', 'checked' => (!$checked || ($checked && in_array(5, $filters['current_stage']))) ? 'checked=""' : ''),
                array('id' => 7, 'libelle' => 'Discontinued', 'checked' => (!$checked || ($checked && in_array(7, $filters['current_stage']))) ? '' : ''),
                array('id' => 8, 'libelle' => 'Permanent range', 'separator' => true, 'checked' => (!$checked || ($checked && in_array(8, $filters['current_stage']))) ? '' : ''),
                array('id' => 99, 'libelle' => 'View Frozen projects', 'checked' => ''),
                array('id' => 100, 'libelle' => 'View only Frozen projects', 'checked' => ''),
            );
        }

        // title
        $ret['filter']['title'] = array();
        $ret['order']['title'] = null;

        $checked = (array_key_exists('title', $filters));
        foreach ($user_innovations_array as $inno_array) {
            $theChecked = (!$checked || ($checked && in_array($inno_array['id'], $filters['title']))) ? 'checked=""' : '';
            $ret['filter']['title'][] = array('id' => $inno_array['id'], 'libelle' => $inno_array['title'], 'checked' => $theChecked);
        }


        // classification
        $ret['filter']['classification'] = array();
        $ret['order']['classification'] = null;
        $checked = (array_key_exists('classification', $filters));
        $ret['filter']['classification'] = array(
            0 => array('id' => "Product", 'libelle' => 'Product', 'checked' => (!$checked || ($checked && in_array('Product', $filters['classification']))) ? 'checked=""' : ''),
            2 => array('id' => "Service", 'libelle' => 'Service', 'checked' => (!$checked || ($checked && in_array('Service', $filters['classification']))) ? 'checked=""' : ''),
            3 => array('id' => "Empty", 'libelle' => 'Empty', 'checked' => (!$checked || ($checked && in_array('Empty', $filters['classification']))) ? 'checked=""' : ''),
        );

        // innovation_type
        $ret['filter']['innovation_type'] = array();
        $ret['order']['innovation_type'] = null;
        $checked = (array_key_exists('innovation_type', $filters));
        $ret['filter']['innovation_type'] = array(
            0 => array('id' => "Stretch", 'libelle' => 'Stretch', 'checked' => (!$checked || ($checked && in_array('Stretch', $filters['innovation_type']))) ? 'checked=""' : ''),
            1 => array('id' => "Incremental", 'libelle' => 'Incremental', 'checked' => (!$checked || ($checked && in_array("Incremental", $filters['innovation_type']))) ? 'checked=""' : ''),
            2 => array('id' => "Breakthrough", 'libelle' => 'Breakthrough', 'checked' => (!$checked || ($checked && in_array('Breakthrough', $filters['innovation_type']))) ? 'checked=""' : ''),
            3 => array('id' => "0", 'libelle' => 'Empty', 'checked' => (!$checked || ($checked && in_array('0', $filters['innovation_type']))) ? 'checked=""' : ''),
        );
        // brand
        $ret['filter']['brand'] = array();
        $ret['order']['brand'] = null;
        $checked = (array_key_exists('brand', $filters));
        foreach ($other_datas['brands_json'] as $brand) {
            $theChecked = (!$checked || ($checked && in_array($brand['id'], $filters['brand']))) ? 'checked=""' : '';
            $ret['filter']['brand'][] = array('id' => $brand['id'], 'libelle' => $brand['text'], 'checked' => $theChecked);
        }

        // entity
        $ret['filter']['entity'] = array();
        $ret['order']['entity'] = null;
        $checked = (array_key_exists('entity', $filters));
        foreach ($other_datas['entities_json'] as $an_entity) {
            $theChecked = (!$checked || ($checked && in_array($an_entity['id'], $filters['entity']))) ? 'checked=""' : '';
            $ret['filter']['entity'][] = array('id' => $an_entity['id'], 'libelle' => $an_entity['text'], 'checked' => $theChecked);
        }
        if ($is_admin || $is_management) {
            if (!$for_monitor) {
                // CUMULATIVE A_P
                $ret['filter']['cumul_a_p'] = array();
                $ret['order']['cumul_a_p'] = null;
                $checked = (array_key_exists('cumul_a_p', $filters));
                $ret['filter']['cumul_a_p'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 500k€', 'checked' => (!$checked || ($checked && in_array('1', $filters['cumul_a_p']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 500k€ & 1M€', 'checked' => (!$checked || ($checked && in_array('2', $filters['cumul_ap']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 1M€ & 2M€', 'checked' => (!$checked || ($checked && in_array('3', $filters['cumul_a_p']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Between 2M€ & 5M€', 'checked' => (!$checked || ($checked && in_array('4', $filters['cumul_a_p']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Between 5M€ & 10M€', 'checked' => (!$checked || ($checked && in_array('5', $filters['cumul_a_p']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'More than 10M€', 'checked' => (!$checked || ($checked && in_array('6', $filters['cumul_a_p']))) ? 'checked=""' : ''),
                );

                // CUMULATIVE CAAP
                $ret['filter']['cumul_caap'] = array();
                $ret['order']['cumul_caap'] = null;
                $checked = (array_key_exists('cumul_caap', $filters));
                $ret['filter']['cumul_caap'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 500k€', 'checked' => (!$checked || ($checked && in_array('1', $filters['cumul_caap']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 500k€ & 1M€', 'checked' => (!$checked || ($checked && in_array('2', $filters['cumul_caap']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 1M€ & 2M€', 'checked' => (!$checked || ($checked && in_array('3', $filters['cumul_caap']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Between 2M€ & 5M€', 'checked' => (!$checked || ($checked && in_array('4', $filters['cumul_caap']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Between 5M€ & 10M€', 'checked' => (!$checked || ($checked && in_array('5', $filters['cumul_caap']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'More than 10M€', 'checked' => (!$checked || ($checked && in_array('6', $filters['cumul_caap']))) ? 'checked=""' : ''),
                );
            } else {
                // LATEST A_P
                $ret['filter']['latest_a_p'] = array();
                $ret['order']['latest_a_p'] = null;
                $checked = (array_key_exists('latest_a_p', $filters));
                $ret['filter']['latest_a_p'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 500k€', 'checked' => (!$checked || ($checked && in_array('1', $filters['latest_a_p']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 500k€ & 1M€', 'checked' => (!$checked || ($checked && in_array('2', $filters['latest_a_p']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 1M€ & 2M€', 'checked' => (!$checked || ($checked && in_array('3', $filters['latest_a_p']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Between 2M€ & 5M€', 'checked' => (!$checked || ($checked && in_array('4', $filters['latest_a_p']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Between 5M€ & 10M€', 'checked' => (!$checked || ($checked && in_array('5', $filters['latest_a_p']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'More than 10M€', 'checked' => (!$checked || ($checked && in_array('6', $filters['latest_a_p']))) ? 'checked=""' : ''),
                );

                // LATEST NS
                $ret['filter']['latest_net_sales'] = array();
                $ret['order']['latest_net_sales'] = null;
                $checked = (array_key_exists('latest_net_sales', $filters));
                $ret['filter']['latest_net_sales'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 500k€', 'checked' => (!$checked || ($checked && in_array('1', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 500k€ & 1M€', 'checked' => (!$checked || ($checked && in_array('2', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 1M€ & 2M€', 'checked' => (!$checked || ($checked && in_array('3', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Between 2M€ & 5M€', 'checked' => (!$checked || ($checked && in_array('4', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Between 5M€ & 10M€', 'checked' => (!$checked || ($checked && in_array('5', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'More than 10M€', 'checked' => (!$checked || ($checked && in_array('6', $filters['latest_net_sales']))) ? 'checked=""' : ''),
                );

                // LATEST CAAP
                $ret['filter']['latest_caap'] = array();
                $ret['order']['latest_caap'] = null;
                $checked = (array_key_exists('latest_caap', $filters));
                $ret['filter']['latest_caap'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 500k€', 'checked' => (!$checked || ($checked && in_array('1', $filters['latest_caap']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 500k€ & 1M€', 'checked' => (!$checked || ($checked && in_array('2', $filters['latest_caap']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 1M€ & 2M€', 'checked' => (!$checked || ($checked && in_array('3', $filters['latest_caap']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Between 2M€ & 5M€', 'checked' => (!$checked || ($checked && in_array('4', $filters['latest_caap']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Between 5M€ & 10M€', 'checked' => (!$checked || ($checked && in_array('5', $filters['latest_caap']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'More than 10M€', 'checked' => (!$checked || ($checked && in_array('6', $filters['latest_caap']))) ? 'checked=""' : ''),
                );

                // LATEST VOLUMES
                $ret['filter']['latest_volume'] = array();
                $ret['order']['latest_volume'] = null;
                $checked = (array_key_exists('latest_volume', $filters));
                $ret['filter']['latest_volume'] = array(
                    0 => array('id' => '1', 'libelle' => 'Under 10 k9Lcs', 'checked' => (!$checked || ($checked && in_array('1', $filters['latest_volume']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Between 10 & 20 k9Lcs', 'checked' => (!$checked || ($checked && in_array('2', $filters['latest_volume']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Between 100 & 1000 k9Lcs', 'checked' => (!$checked || ($checked && in_array('3', $filters['latest_volume']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'More than 1000 k9Lcs', 'checked' => (!$checked || ($checked && in_array('6', $filters['latest_volume']))) ? 'checked=""' : ''),
                );
            }
        }

        // growth_model
        $ret['filter']['growth_model'] = array();
        $ret['order']['growth_model'] = null;
        $checked = (array_key_exists('growth_model', $filters));
        $ret['filter']['growth_model'] = array(
            0 => array('id' => 'fast_growth', 'libelle' => 'Fast growth', 'checked' => (!$checked || ($checked && in_array('fast_growth', $filters['growth_model']))) ? 'checked=""' : ''),
            1 => array('id' => 'slow_build', 'libelle' => 'Slow build', 'checked' => (!$checked || ($checked && in_array('slow_build', $filters['growth_model']))) ? 'checked=""' : ''),
        );

        // years_since_launch
        $ret['filter']['years_since_launch'] = array();
        $ret['order']['years_since_launch'] = null;
        $ret['filter']['years_since_launch'] = array(
            0 => array('id' => '1', 'libelle' => 'Y1', 'checked' => (!$checked || ($checked && in_array('1', $filters['years_since_launch']))) ? 'checked=""' : ''),
            1 => array('id' => '2', 'libelle' => 'Y2', 'checked' => (!$checked || ($checked && in_array('2', $filters['years_since_launch']))) ? 'checked=""' : ''),
            2 => array('id' => '3', 'libelle' => 'Y3', 'checked' => (!$checked || ($checked && in_array('3', $filters['years_since_launch']))) ? 'checked=""' : ''),
            3 => array('id' => '4', 'libelle' => 'Y4', 'checked' => (!$checked || ($checked && in_array('4', $filters['years_since_launch']))) ? 'checked=""' : ''),
            4 => array('id' => '5', 'libelle' => '5 years and more', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('6', $filters['years_since_launch']))) ? 'checked=""' : ''),
            5 => array('id' => '6', 'libelle' => 'Not in market', 'checked' => (!$checked || ($checked && in_array('6', $filters['years_since_launch']))) ? 'checked=""' : ''),

        );

        if ($for_monitor) {
            // PORTFOLIO
            $ret['filter']['portfolio'] = array();
            $ret['order']['portfolio'] = null;
            $checked = (array_key_exists('portfolio', $filters));
            $ret['filter']['portfolio'] = array(
                0 => array('id' => 'big_bet', 'libelle' => 'Big Bet', 'checked' => (!$checked || ($checked && in_array('3', $filters['portfolio']))) ? 'checked=""' : ''),
                1 => array('id' => 'rtd_rts', 'libelle' => 'RTD / RTS', 'checked' => (!$checked || ($checked && in_array('2', $filters['portfolio']))) ? 'checked=""' : ''),
                2 => array('id' => 'no_low_alcohol', 'libelle' => 'No & Low Alcohol', 'checked' => (!$checked || ($checked && in_array('3', $filters['portfolio']))) ? 'checked=""' : ''),
                3 => array('id' => 'specialty', 'libelle' => 'Specialty', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('6', $filters['portfolio']))) ? 'checked=""' : ''),
                4 => array('id' => 'none', 'libelle' => 'Empty', 'checked' => (!$checked || ($checked && in_array('4', $filters['portfolio']))) ? 'checked=""' : ''),
            );
        }

        if (!$for_monitor) {
            // other
            $ret['filter']['other'] = array();
            $ret['order']['other'] = null;
            $checked = (array_key_exists('other', $filters));
            $ret['filter']['other'] = array(
                0 => array('id' => '1', 'libelle' => 'Missing business data', 'checked' => (!$checked || ($checked && in_array('business_datas', $filters['other']))) ? 'checked=""' : ''),
                1 => array('id' => '2', 'libelle' => 'Completed business data', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('completed_business_datas', $filters['other']))) ? 'checked=""' : ''),
                2 => array('id' => '3', 'libelle' => 'Big Bet', 'checked' => (!$checked || ($checked && in_array('is_big_bet', $filters['other']))) ? 'checked=""' : ''),
                3 => array('id' => '4', 'libelle' => 'Top contributor', 'checked' => (!$checked || ($checked && in_array('is_top_contrib', $filters['other']))) ? 'checked=""' : ''),
                4 => array('id' => '0', 'libelle' => 'Empty', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('0', $filters['other']))) ? 'checked=""' : ''),
                5 => array('id' => '201', 'libelle' => 'Product', 'checked' => ''),
                6 => array('id' => '202', 'libelle' => 'Service', 'checked' => ''),
            );
            if ($is_admin) {
                $ret['filter']['other'] = array(
                    //0 => array('id' => '101', 'libelle' => 'Simple investment', 'checked' => (!$checked || ($checked && in_array('business_datas', $filters['other']))) ? 'checked=""' : ''),
                    //1 => array('id' => '102', 'libelle' => 'High investment', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('business_datas', $filters['other']))) ? 'checked=""' : ''),
                    0 => array('id' => '1', 'libelle' => 'Missing business data', 'checked' => (!$checked || ($checked && in_array('business_datas', $filters['other']))) ? 'checked=""' : ''),
                    1 => array('id' => '2', 'libelle' => 'Completed business data', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('completed_business_datas', $filters['other']))) ? 'checked=""' : ''),
                    2 => array('id' => '3', 'libelle' => 'Big Bet', 'checked' => (!$checked || ($checked && in_array('is_big_bet', $filters['other']))) ? 'checked=""' : ''),
                    3 => array('id' => '4', 'libelle' => 'Top contributor', 'checked' => (!$checked || ($checked && in_array('is_top_contrib', $filters['other']))) ? 'checked=""' : ''),
                    4 => array('id' => '5', 'libelle' => 'Negative CAAP', 'checked' => (!$checked || ($checked && in_array('is_worst_contrib', $filters['other']))) ? 'checked=""' : ''),
                    5 => array('id' => '6', 'libelle' => 'High investment', 'checked' => (!$checked || ($checked && in_array('is_high_invest', $filters['other']))) ? 'checked=""' : ''),
                    6 => array('id' => '0', 'libelle' => 'Empty', 'separator' => true, 'checked' => (!$checked || ($checked && in_array('0', $filters['other']))) ? 'checked=""' : ''),
                    7 => array('id' => '201', 'libelle' => 'Product', 'checked' => ''),
                    8 => array('id' => '202', 'libelle' => 'Service', 'checked' => ''),
                );
            }
        }
        if (!$for_monitor) {
            // Consumer opportunity
            $ret['filter']['consumer_opportunity'] = array();
            $ret['order']['consumer_opportunity'] = null;
            $checked = (array_key_exists('consumer_opportunity', $filters));
            $ret['filter']['consumer_opportunity'] = array(
                0 => array('id' => 1, 'libelle' => 'Human Authenticity', 'checked' => (!$checked || ($checked && in_array(1, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                1 => array('id' => 2, 'libelle' => 'Easy at Home & Everywhere', 'checked' => (!$checked || ($checked && in_array(2, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                2 => array('id' => 3, 'libelle' => 'Shaking The Codes', 'checked' => (!$checked || ($checked && in_array(3, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                3 => array('id' => 4, 'libelle' => 'Power to Consumers', 'checked' => (!$checked || ($checked && in_array(4, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                4 => array('id' => 5, 'libelle' => 'Feminine Identity', 'checked' => (!$checked || ($checked && in_array(5, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                5 => array('id' => 6, 'libelle' => 'Doing Good', 'checked' => (!$checked || ($checked && in_array(6, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                6 => array('id' => 7, 'libelle' => 'Better for Me', 'checked' => (!$checked || ($checked && in_array(7, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                7 => array('id' => 8, 'libelle' => 'Tactical Innovation', 'separator' => true, 'checked' => (!$checked || ($checked && in_array(8, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
                8 => array('id' => 0, 'libelle' => 'Empty', 'checked' => (!$checked || ($checked && in_array(0, $filters['consumer_opportunity']))) ? 'checked=""' : ''),
            );
        }

        return $ret;
    }

    /**
     * Get array for table tr.
     *
     * @return array
     */
    function getArrayForTableTr()
    {
        $contact_username = $this->getProperUsername();
        $right = $this->getProperRole();
        return array(
            'uir_id' => null,
            'user_url' => $this->getUserUrl(),
            'user_uid' => $this->getId(),
            'username' => $contact_username,
            'user_email' => $this->getEmail(),
            'right' => $right,
            'extends_rights' => true
        );
    }


    /**
     * Get user url.
     *
     * @return string
     */
    function getUserUrl()
    {
        return '/user/' . $this->getId();
    }

    /**
     * Get contact innovations.
     *
     * @return array
     */
    public function getContactInnovations()
    {
        $ret = array();
        foreach ($this->getUserInnovationRights() as $innovationRight) {
            if ($innovationRight->getInnovation() && $innovationRight->isContactOwner()) {
                $ret[] = $innovationRight->getInnovation();
            }
        }
        return $ret;
    }

    /**
     * Get contact innovations ids.
     *
     * @param bool $with_out_of_funnel
     * @return array
     */
    public function getContactInnovationsIds($with_out_of_funnel = true)
    {
        $ret = array();
        foreach ($this->getUserInnovationRights() as $innovationRight) {
            if (!$with_out_of_funnel) {
                if (
                    $innovationRight->getInnovation() &&
                    !$innovationRight->getInnovation()->isOutOfFunnel() &&
                    $innovationRight->isContactOwner()
                ) {
                    $ret[] = $innovationRight->getInnovation()->getId();
                }
            } else {
                if ($innovationRight->getInnovation() && $innovationRight->isContactOwner()) {
                    $ret[] = $innovationRight->getInnovation()->getId();
                }
            }
        }
        return $ret;
    }

    /**
     * Get team member innovations ids.
     *
     * @param bool $with_out_of_funnel
     * @param bool $only_write
     * @return array
     */
    public function getTeamMemberInnovationsIds($only_write = true, $with_out_of_funnel = true)
    {
        $ret = array();
        foreach ($this->getUserInnovationRights() as $innovationRight) {
            if (!$with_out_of_funnel) {
                if (
                    $innovationRight->getInnovation() &&
                    !$innovationRight->getInnovation()->isOutOfFunnel() &&
                    !$innovationRight->isContactOwner()
                ) {
                    if ($only_write && $innovationRight->getRight() === UserInnovationRight::RIGHT_WRITE) {
                        $ret[] = $innovationRight->getInnovation()->getId();
                    } elseif (!$only_write) {
                        $ret[] = $innovationRight->getInnovation()->getId();
                    }
                }
            } else {
                if ($innovationRight->getInnovation() && !$innovationRight->isContactOwner()) {
                    if ($only_write && $innovationRight->getRight() === UserInnovationRight::RIGHT_WRITE) {
                        $ret[] = $innovationRight->getInnovation()->getId();
                    } elseif (!$only_write) {
                        $ret[] = $innovationRight->getInnovation()->getId();
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getProperUsername() : 'New user';
    }


    /**
     * Get admin user_rights message
     *
     * @return null|string
     */
    public function getAdminUserRightsMessage()
    {
        if ($this->hasManagementRights()) {
            return "User has management rights";
        } elseif ($this->hasAdminRights()) {
            return "User has admin rights (HQ or Developer)";
        }
        return null;
    }


    /**
     * Set perimeter.
     *
     * @param \AppBundle\Entity\EntityGroup|null $perimeter
     *
     * @return User
     */
    public function setPerimeter(\AppBundle\Entity\EntityGroup $perimeter = null)
    {
        $this->perimeter = $perimeter;

        return $this;
    }

    /**
     * Get perimeter.
     *
     * @return \AppBundle\Entity\EntityGroup|null
     */
    public function getPerimeter()
    {
        return $this->perimeter;
    }

    /**
     * Get proper perimeter id.
     *
     * @return integer
     */
    public function getProperPerimeterId()
    {
        return ($this->getPerimeter()) ? $this->getPerimeter()->getId() : -1;
    }

    /**
     * to array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'text' => $this->getProperUsername(),
            'username' => $this->getProperUsername(),
            'mail' => $this->getEmail(),
            'proper_role' => $this->getProperUserRole(),
            'url' => $this->getUserUrl(),
            'picture' => $this->getPictureUrl(200),
            'accept_contact' => $this->getAcceptContact(),
            'situation_and_entity' => (($this->getSituationAndEntity()) ? $this->getSituationAndEntity() : ''),
            'nb_projects' => $this->getNbProjects(),
            'nb_skills' => $this->getNbSkills(),
            'is_pr_employee' => $this->getIsPrEmploye(),
            'minimal_last_login' => $this->getRelativeMinimalLastLoginDate(),
        );
    }

    /**
     * to minimal array.
     *
     * @return array
     */
    public function toMinimalArray()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getProperUsername(),
            'mail' => $this->getEmail(),
            'url' => $this->getUserUrl(),
            'picture' => $this->getPictureUrl(200),
        );
    }

    /**
     * get nb projects
     * @return int
     */
    public function getNbProjects()
    {
        $owner_projects_ids = $this->getContactInnovationsIds(false);
        $teammember_projects_ids = $this->getTeamMemberInnovationsIds(true, false);
        return count($owner_projects_ids) + count($teammember_projects_ids);
    }

    /**
     * Get relative last login date.
     *
     * @return string
     */
    public function getRelativeLastLoginDate()
    {
        $date = $this->lastLogin;
        if (!$date) {
            return "Didn’t connect lately";
        }
        $actualDate = new \DateTime();
        $actualDate->setTimezone(new \DateTimeZone('GMT'));
        $diff = $date->diff($actualDate);
        if ($diff->format("%y") >= 1 || $diff->format("%m") > 6) {
            return "Didn’t connect lately";
        } elseif ($diff->format("%m") >= 1) {
            return "Connected " . $diff->format("%m") . "mth ago";
        } elseif ($diff->format("%a") >= 1) {
            return "Connected " . $diff->format("%a") . "d ago";
        } elseif ($diff->format("%h") >= 1) {
            return "Connected " . $diff->format("%h") . "h ago";
        } elseif ($diff->format("%i") > 5) {
            return "Connected " . $diff->format("%i") . "mn ago";
        } else {
            return "Currently connected";
        }
    }

    /**
     * Get relative minimal last login date.
     *
     * @return string
     */
    public function getRelativeMinimalLastLoginDate()
    {
        $date = $this->lastLogin;
        if (!$date) {
            return null;
        }
        $actualDate = new \DateTime();
        $actualDate->setTimezone(new \DateTimeZone('GMT'));
        $diff = $date->diff($actualDate);
        if ($diff->format("%y") >= 1) {
            return $diff->format("%y") . 'y';
        } elseif ($diff->format("%m") >= 1) {
            return $diff->format("%m") . "mth";
        } elseif ($diff->format("%a") >= 1) {
            return $diff->format("%a") . "d";
        } elseif ($diff->format("%h") >= 1) {
            return $diff->format("%h") . "h";
        } else {
            $minutes = ($diff->format("%i") > 0) ? $diff->format("%i") : 1;
            return $minutes . "m";
        }
    }

    /**
     * Get situation and entity.
     *
     * @return string|null
     */
    public function getSituationAndEntity()
    {
        $situation_and_entity = $this->getSituation();
        if ($this->getUserEntity()) {
            $situation_and_entity .= " (" . $this->getUserEntity()->getProperTitle() . ")";
        }
        return $situation_and_entity;
    }

    /**
     * Get possible connected user ids.
     * @return array
     */
    public function getPossibleConnectedUserIds()
    {
        $user_ids = array();
        $activities = $this->getActivities()->filter(function ($activity) {
            return (
                $activity->getActionId() == Activity::ACTION_PROMOTE_INNOVATION_VIEW
            );
        });
        foreach ($activities as $activity) {
            $innovation = $activity->getInnovation();
            if ($innovation) {
                $user_innovation_rights = $innovation->getUserInnovationRights();
                foreach ($user_innovation_rights as $user_innovation_right) {
                    if ($user_innovation_right->getRight() == UserInnovationRight::RIGHT_WRITE && $user_innovation_right->getUser()) {
                        if (!in_array($user_innovation_right->getUser()->getId(), $user_ids)) {
                            $user_ids[] = $user_innovation_right->getUser()->getId();
                        }
                    }
                }
            }
        }
        return $user_ids;
    }

    /**
     * Add feedbackInvitation.
     *
     * @param \AppBundle\Entity\FeedbackInvitation $feedbackInvitation
     *
     * @return User
     */
    public function addFeedbackInvitation(\AppBundle\Entity\FeedbackInvitation $feedbackInvitation)
    {
        $this->feedback_invitations[] = $feedbackInvitation;

        return $this;
    }

    /**
     * Remove feedbackInvitation.
     *
     * @param \AppBundle\Entity\FeedbackInvitation $feedbackInvitation
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFeedbackInvitation(\AppBundle\Entity\FeedbackInvitation $feedbackInvitation)
    {
        return $this->feedback_invitations->removeElement($feedbackInvitation);
    }

    /**
     * Get feedbackInvitations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedbackInvitations()
    {
        return $this->feedback_invitations;
    }

    /**
     * Returns if $this has a Managing Director
     *
     * @return boolean Do this user has the MD role
     */
    public function isManagingDirector()
    {
        return $this->hasRole(self::ROLE_MD);
    }

    /**
     * Add searchHistory.
     *
     * @param \AppBundle\Entity\SearchHistory $searchHistory
     *
     * @return User
     */
    public function addSearchHistory(\AppBundle\Entity\SearchHistory $searchHistory)
    {
        $this->search_histories[] = $searchHistory;

        return $this;
    }

    /**
     * Remove searchHistory.
     *
     * @param \AppBundle\Entity\SearchHistory $searchHistory
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSearchHistory(\AppBundle\Entity\SearchHistory $searchHistory)
    {
        return $this->search_histories->removeElement($searchHistory);
    }

    /**
     * Get searchHistories.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSearchHistories()
    {
        return $this->search_histories;
    }


    /**
     * Get last 6 search histories
     *
     * @return array
     */
    public function getLastSearchHistories()
    {
        $ret = [];
        if (!$this->getSearchHistories()) {
            return $ret;
        }
        foreach ($this->getSearchHistories()->slice(0, 6) as $searchHistory) {
            $ret[] = $searchHistory->toArray();
        }
        return $ret;
    }

    /**
     * Set hasSeenWalkthrough.
     *
     * @param bool $hasSeenWalkthrough
     *
     * @return User
     */
    public function setHasSeenWalkthrough($hasSeenWalkthrough)
    {
        $this->has_seen_walkthrough = $hasSeenWalkthrough;

        return $this;
    }

    /**
     * Get hasSeenWalkthrough.
     *
     * @return bool
     */
    public function getHasSeenWalkthrough()
    {
        return $this->has_seen_walkthrough;
    }

    /**
     * Get Mouseflow type for $current_path.
     *
     * @return string
     */
    public function getMouseflowType()
    {
        if ($this->hasDeveloperRights()) {
            return 'dev';
        } elseif ($this->hasAdminRights()) {
            return 'hq';
        } elseif ($this->hasManagementRights()) {
            return 'management';
        } elseif ($this->isManagingDirector()) {
            return 'managing_director';
        } elseif ($this->hasNoRole()) {
            return 'other';
        }
        return 'maker';
    }

    /**
     * Add ownInnovation.
     *
     * @param Innovation $ownInnovation
     *
     * @return User
     */
    public function addOwnInnovation(Innovation $ownInnovation)
    {
        $this->own_innovations[] = $ownInnovation;

        return $this;
    }

    /**
     * Remove ownInnovation.
     *
     * @param Innovation $ownInnovation
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOwnInnovation(Innovation $ownInnovation)
    {
        return $this->own_innovations->removeElement($ownInnovation);
    }

    /**
     * Get ownInnovations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnInnovations()
    {
        return $this->own_innovations;
    }

    /**
     * Set acceptScheduledEmails.
     *
     * @param bool $acceptScheduledEmails
     *
     * @return User
     */
    public function setAcceptScheduledEmails($acceptScheduledEmails)
    {
        $this->accept_scheduled_emails = $acceptScheduledEmails;

        return $this;
    }

    /**
     * Get acceptScheduledEmails.
     *
     * @return bool
     */
    public function getAcceptScheduledEmails()
    {
        return $this->accept_scheduled_emails;
    }

    /**
     * Set situation.
     *
     * @param string|null $situation
     *
     * @return User
     */
    public function setSituation($situation = null)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation.
     *
     * @return string|null
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * Set country.
     *
     * @param string|null $country
     *
     * @return User
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set userEntity.
     *
     * @param \AppBundle\Entity\UserEntity|null $userEntity
     *
     * @return User
     */
    public function setUserEntity(\AppBundle\Entity\UserEntity $userEntity = null)
    {
        $this->user_entity = $userEntity;

        return $this;
    }

    /**
     * Get userEntity.
     *
     * @return \AppBundle\Entity\UserEntity|null
     */
    public function getUserEntity()
    {
        return $this->user_entity;
    }

    /**
     * To select2 array.
     *
     * @return array
     */
    public function toSelect2Array()
    {
        return [
            "id" => $this->getId(),
            "text" => $this->getProperUsername(),
            "type" => "user"
        ];
    }

    /**
     * Set acceptContact.
     *
     * @param bool $acceptContact
     *
     * @return User
     */
    public function setAcceptContact($acceptContact)
    {
        $this->accept_contact = $acceptContact;

        return $this;
    }

    /**
     * Get acceptContact.
     *
     * @return bool
     */
    public function getAcceptContact()
    {
        return $this->accept_contact;
    }

    /**
     * Add userSkill.
     *
     * @param \AppBundle\Entity\UserSkill $userSkill
     *
     * @return User
     */
    public function addUserSkill(\AppBundle\Entity\UserSkill $userSkill)
    {
        $this->user_skills[] = $userSkill;

        return $this;
    }

    /**
     * Remove userSkill.
     *
     * @param \AppBundle\Entity\UserSkill $userSkill
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUserSkill(\AppBundle\Entity\UserSkill $userSkill)
    {
        return $this->user_skills->removeElement($userSkill);
    }

    /**
     * Get userSkills.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserSkills()
    {
        return $this->user_skills;
    }

    /**
     * Get skills.
     *
     * @return array
     */
    public function getSkills()
    {
        $skills = array();
        foreach ($this->getUserSkills() as $userSkill) {
            $a_skill = $userSkill->getSkill();
            $a_sender = ($userSkill->getSender()) ? $userSkill->getSender()->toMinimalArray() : null;
            if ($a_skill && $a_sender) {
                $added = false;
                foreach ($skills as &$skill) {
                    if ($skill['skill']['id'] == $a_skill->getId()) {
                        $added = true;
                        $skill['senders'][] = $a_sender;
                    }
                }
                if (!$added) {
                    $new_skill = array(
                        'skill' => $a_skill->toArray(),
                        'senders' => array()
                    );
                    $new_skill['senders'][] = $a_sender;
                    $skills[] = $new_skill;
                }
            }
        }
        return $skills;
    }

    /**
     * Get nb skills
     *
     * @return int
     */
    public function getNbSkills()
    {
        return count($this->getSkills());
    }
}
