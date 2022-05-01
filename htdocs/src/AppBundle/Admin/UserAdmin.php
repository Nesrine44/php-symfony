<?php
/**
 * Created by PhpStorm.
 * User: FlorianNicolas
 * Date: 21/08/2018
 * Time: 17:47
 */
namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserAdmin extends AbstractOtherDataAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');

        $rolesChoices = self::flattenRoles($roles);
        $user = $this->getSubject();

        $formMapper->add('email', TextType::class);
        $formMapper->add('firstname', TextType::class, ['required' => false]);
        $formMapper->add('lastname', TextType::class, ['required' => false]);
        if (!$user->getId()) {
            $formMapper->add('plainPassword', PasswordType::class, [
                'required' => false,
                'help' => "<p class='help-block'>Don't fill this field for a PR employe.</p>"
            ]);
        }
        $formMapper->add('is_pr_employe', CheckboxType::class, ['required' => false]);
        $formMapper->add('roles', 'choice', array(
                'choices' => $rolesChoices,
                'multiple' => true
            )
        );
        if ($user->getId()) {
            $formMapper->add('enabled', CheckboxType::class, ['required' => false,
                'help' => "<p class='help-block'>If not enabled, user will lose access to website.</p>"
            ]);
        }
        $formMapper->add('perimeter', 'sonata_type_model', ['required' => false]);
        $formMapper->add('user_entity', 'sonata_type_model', ['required' => false, 'attr' => array('readonly' => true)]);
        $formMapper->add('accept_scheduled_emails', CheckboxType::class, [
            'required' => false,
            'help' => "<p class='help-block'>If unchecked, user will not receive scheduled emails* anymore.<br>(Promote email every friday 12:00.)</p>"
        ]);
        $formMapper->add('accept_newsletter', CheckboxType::class, [
            'required' => false
        ]);
        $formMapper->add('accept_contact', CheckboxType::class, [
            'required' => false
        ]);
        $formMapper->add('has_seen_walkthrough', CheckboxType::class, [
            'required' => false,
            'help' => "<p class='help-block' style='padding-bottom: 50px;'>If checked, user has already seen walkthrough and it will not appear anymore.</p>"
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
        $datagridMapper->add('firstname');
        $datagridMapper->add('lastname');
        $datagridMapper->add('is_pr_employe');
        $datagridMapper->add('roles');
        $datagridMapper->add('user_entity');
        $datagridMapper->add('lastLogin');
        $datagridMapper->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');

        $rolesChoices = self::flattenRoles($roles, true);

        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('firstname');
        $listMapper->addIdentifier('lastname');
        $listMapper->addIdentifier('email');
        $listMapper->addIdentifier('is_pr_employe');
        $listMapper->addIdentifier('roles', 'choice', array(
            'choices' => $rolesChoices,
            'multiple' => true
        ));
        $listMapper->addIdentifier('lastLogin');
    }

    /**
     * Turns the role's array keys into string <ROLES_NAME> keys.
     * @param array $rolesHierarchy
     * @param bool $niceName
     * @param bool $withChildren
     * @param bool $withGrandChildren
     * @return array
     */
    protected static function flattenRoles($rolesHierarchy, $niceName = false, $withChildren = false)
    {
        $flatRoles = [];
        foreach ($rolesHierarchy as $key => $roles) {
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    if (!isset($flatRoles[$role])) {
                        $flatRoles[$role] = $niceName ? self::niceRoleName($role) : $role;
                    }
                }
            }
            $flatRoles[$key] = $niceName ? self::niceRoleName($key) : $key;
            if ($withChildren && !empty($roles)) {
                if ($niceName) {
                    array_walk($roles, function (&$item) {
                        $item = self::niceRoleName($item);
                    });
                }
                $flatRoles[$key] .= ' (' . join(', ', $roles) . ')';
            }
        }
        return $flatRoles;
    }

    /**
     * Remove underscors, ROLE_ prefix and uppercase words
     * @param string $role
     * @return string
     */
    protected static function niceRoleName($role)
    {
        $role = str_replace('SUPER_ADMIN', 'DEVELOPER', $role);
        return ucwords(strtolower(preg_replace(['/\AROLE_/', '/_/'], ['', ' '], $role)));
    }


    public function prePersist($object){
        $container = $this->getConfigurationPool()->getContainer();
        $secret_key = $container->getParameter('pr_auth')['password_key'];
        if($object->getIsPrEmploye()){
            $object->setPlainPassword($object->getGeneratedLocalPassword($secret_key));
        }
        $object->setEnabled(1);
        parent::prePersist($object);
    }


    /**
     * Get export fields
     * @return array
     */
    public function getExportFields()
    {
        return array(
            'ID' => 'id',
            'Email' => 'email',
            'Firstname' => 'firstname',
            'Lastname' => 'lastname',
            'Enabled' => 'enabled',
            'Is pr employe' => 'is_pr_employe',
            'Created at' => 'created_at',
            'Last login' => 'lastLogin',
            'Accept newsletter' => 'accept_newsletter',
            'Accept scheduled emails' => 'accept_scheduled_emails',
            'Accept contact' => 'accept_contact',
        );
    }
}
