<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInnovationRight
 *
 * @ORM\Table(name="user_innovation_right")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserInnovationRightRepository")
 */
class UserInnovationRight
{
    const RIGHT_READ = 'read';
    const RIGHT_WRITE = 'write';
    const ROLE_OPERATIONS= 'Operations';
    const ROLE_LEGAL= 'Legal';
    const ROLE_CONSUMER_INSIGHTS = 'Consumer Insights';
    const ROLE_MANAGEMENT= 'Management';
    const ROLE_RESEARCH_AND_DEVELOPMENT = 'R&D';
    const ROLE_CONTACT_OWNER = 'Owner';
    const ROLE_FINANCE_CONTACT = 'Finance contact';
    const ROLE_OTHER = 'Other';
    
    /**
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $old_id;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_innovation_rights")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="user_innovation_rights")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="user_role", type="string", length=255, nullable=true)
     */
    private $user_role;

    /**
     * @var string
     *
     * @ORM\Column(name="user_right", type="string", columnDefinition="ENUM('read', 'write')")
     */
    private $user_right = self::RIGHT_WRITE;


    /**
     * Set role.
     *
     * @param string|null $role
     *
     * @return UserInnovationRight
     */
    public function setRole($role = null)
    {
        $this->user_role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string|null
     */
    public function getRole()
    {
        return $this->user_role;
    }

    /**
     * Set right.
     *
     * @param string $right
     *
     * @return UserInnovationRight
     */
    public function setRight($right)
    {
        $this->user_right = $right;

        return $this;
    }

    /**
     * Get right.
     *
     * @return string
     */
    public function getRight()
    {
        return $this->user_right;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserInnovationRight
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation $innovation
     *
     * @return UserInnovationRight
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
     * Set userRole.
     *
     * @param string|null $userRole
     *
     * @return UserInnovationRight
     */
    public function setUserRole($userRole = null)
    {
        $this->user_role = $userRole;

        return $this;
    }

    /**
     * Get userRole.
     *
     * @return string|null
     */
    public function getUserRole()
    {
        return $this->user_role;
    }

    /**
     * Set userRight.
     *
     * @param string $userRight
     *
     * @return UserInnovationRight
     */
    public function setUserRight($userRight)
    {
        $this->user_right = $userRight;

        return $this;
    }

    /**
     * Get userRight.
     *
     * @return string
     */
    public function getUserRight()
    {
        return $this->user_right;
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
     * Get proper user role to display
     *
     * @return string
     */
    public function getProperRole()
    {
        $role = $this->user_role;

        switch ($role) {
            case self::ROLE_CONTACT_OWNER:
                return self::ROLE_CONTACT_OWNER;
            case self::ROLE_FINANCE_CONTACT:
                return self::ROLE_FINANCE_CONTACT;
            case self::ROLE_OTHER:
                return self::ROLE_OTHER;
            case self::ROLE_RESEARCH_AND_DEVELOPMENT:
                return self::ROLE_RESEARCH_AND_DEVELOPMENT; 
            case self::ROLE_MANAGEMENT:
                return self::ROLE_MANAGEMENT;   
            case self::ROLE_CONSUMER_INSIGHTS:
                return self::ROLE_CONSUMER_INSIGHTS;   
            case self::ROLE_LEGAL:
                return self::ROLE_LEGAL;  
            case self::ROLE_OPERATIONS:
                return self::ROLE_OPERATIONS;  
            default:
                return self::ROLE_OTHER;
        }
    }

    /**
     * Is contact owner.
     *
     * @return bool
     */
    public function isContactOwner(){
        return ($this->getRole() === UserInnovationRight::ROLE_CONTACT_OWNER);
    }

    /**
     * Get array for table tr.
     *
     * @return array|null
     */
    function getArrayForTableTr(){
        if(!$this->getUser()){
            return null;
        }
        return array(
            'uir_id' => $this->getUser()->getId().'-'.$this->getInnovation()->getId(),
            'url' => $this->getUser()->getUserUrl(),
            'user_id' => $this->getUser()->getId(),
            'picture' => $this->getUser()->getPictureUrl(),
            'situation_and_entity' => $this->getUser()->getSituationAndEntity(),
            'username' => $this->getUser()->getProperUsername(),
            'role' => $this->getProperRole(),
            'extends_rights' => $this->isContactOwner(),
            'innovation_title' => (($this->getInnovation()) ? $this->getInnovation()->getTitle() : ''),
        );
    }

    /**
     * Get array for admin table tr.
     *
     * @return array|null
     */
    function getArrayForAdminTableTr(){
        if(!$this->getUser()){
            return null;
        };
        $proper_right = ($this->isContactOwner()) ? 'Contact Owner' : ucfirst($this->getRight());
        return array(
            'innovation_id' => $this->getInnovation()->getId(),
            'is_owner' => $this->isContactOwner(),
            'innovation_title' => $this->getInnovation()->getTitle(),
            'innovation_entity' => (($this->getInnovation()->getEntity()) ? $this->getInnovation()->getEntity()->getTitle() : ''),
            'right' => $proper_right,
            'role' => $this->getProperRole(),
        );
    }
}
