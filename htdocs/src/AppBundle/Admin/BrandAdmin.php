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
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BrandAdmin extends AbstractOtherDataAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $groupsChoices = self::getGroupsForm();

        $formMapper->add('title', TextType::class);
        $formMapper->add('group_id', 'choice', array(
                'choices' => $groupsChoices,
                'multiple' => false
            )
        );

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('group_id');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $groupsChoices = self::getGroups();

        $listMapper->addIdentifier('title');
        $listMapper->addIdentifier('group_id', 'choice', array(
            'choices' => $groupsChoices,
            'multiple' => false
        ));
    }

    /**
     * Get groups for list.
     * @return array
     */
    protected static function getGroups()
    {
        $groups = [];
        $groups[0] ='Others (0)';
        $groups[1] = 'Strategic International (1)';
        $groups[2] = 'Strategic Local (2)';
        $groups[3] = 'Wines (3)';
        return $groups;
    }

    /**
     * Get groups form.
     * @return array
     */
    protected static function getGroupsForm()
    {
        $groups = [];
        $groups['Others (0)'] = 0;
        $groups['Strategic International (1)'] = 1;
        $groups['Strategic Local (2)'] = 2;
        $groups['Wines (3)'] = 3;
        return $groups;
    }
}
