<?php

namespace PrAuthBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

abstract class PrAuthUser extends BaseUser
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pr_employe", type="boolean")
     */
    protected $is_pr_employe = false;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    protected $lastname;

    /**
     * Sets the email.
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * Set isPrEmploye.
     *
     * @param bool $isPrEmploye
     *
     * @return User
     */
    public function setIsPrEmploye($isPrEmploye)
    {
        $this->is_pr_employe = $isPrEmploye;

        return $this;
    }


    /**
     * remove all roles
     *
     * @return User
     */
    public function removeAllRoles()
    {
        $this->roles = array();

        return $this;
    }

    /**
     * Get isPrEmploye.
     *
     * @return bool
     */
    public function getIsPrEmploye()
    {
        return $this->is_pr_employe;
    }

    /**
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return User
     */
    public function setFirstname($firstname = null)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname.
     *
     * @param string|null $lastname
     *
     * @return User
     */
    public function setLastname($lastname = null)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * generateFirstnameFromEmail
     *
     * @return $this
     */
    public function generateFirstnameFromEmail()
    {
        $mail_explode = explode('@', $this->email);
        $username = ucwords(str_replace(".", " ", $mail_explode[0]));
        $this->firstname = $username;
        return $this;
    }

    /**
     * getProperUsername
     * Note : we don't use $this->username. it's only used by fosuserbundle to connexion
     * @return string
     */
    public function getProperUsername()
    {
        if (!$this->firstname) {
            $this->generateFirstnameFromEmail();
        }
        $username = $this->firstname;
        if ($this->lastname) {
            $username .= ' ' . $this->lastname;
        }
        return $username;
    }

    /**
     * Get generated local password.
     *
     * @param string|null $secret_key
     *
     * @return string|null
     */
    public function getGeneratedLocalPassword($secret_key = null)
    {
        if ($secret_key && $this->getIsPrEmploye()) {
            return md5($secret_key . strtolower($this->getEmail()));
        }
        return null;
    }

    /**
     * Has pernod ricard email.
     *
     *
     * @return bool
     */
    public function hasPernodRicardEmail()
    {
        return (strpos($this->getEmail(), 'pernod-ricard.com') !== false);
    }


    /**
     * Create pernod ricard sso user.
     *
     * @param $container
     * @param string $email
     * @param array $roles
     * @param string|null $firstname
     * @param string|null $lastname
     * @return User
     */
    public static function createPernodRicardSsoUser($container, $email, $roles = array(), $firstname = null, $lastname = null)
    {
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($email);
        if ($user) {
            $changes = false;
            if ($firstname && $firstname != $user->getFirstname()) {
                $user->setFirstname($firstname);
                $changes = true;
            }
            if ($lastname && $lastname != $user->getLastname()){
                $user->setLastname($lastname);
                $changes = true;
            }
            if($changes) {
                $userManager->updateUser($user);
            }
            return $user;
        }
        if (count($roles) === 0) {
            $roles = $container->getParameter('pr_auth')['default_roles'];
        }
        $secret_key = $container->getParameter('pr_auth')['password_key'];
        $user = $userManager->createUser();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        $user->setIsPrEmploye(true);
        $user->setPlainPassword($user->getGeneratedLocalPassword($secret_key));
        if ($firstname || $lastname) {
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
        } else {
            $user->generateFirstnameFromEmail();
        }
        $userManager->updateUser($user);

        // Generate other datas :
        $pernodWorker = $container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();

        return $user;
    }

}
