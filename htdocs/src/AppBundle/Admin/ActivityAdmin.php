<?php
/**
 * Created by PhpStorm.
 * User: FlorianNicolas
 * Date: 21/08/2018
 * Time: 17:47
 */
namespace AppBundle\Admin;

use AppBundle\Entity\Activity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Route\RouteCollection;

class ActivityAdmin extends AbstractAdmin
{

    protected $datagridValues = [

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'created_at',
    ];


    protected function configureRoutes(RouteCollection $collection)
    {
        // OR remove all route except named ones
        $collection->remove('create');
    }
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $actionChoices = Activity::getFlattenActions();
        
        $formMapper->add('innovation', 'sonata_type_model', ['required' => false]);
        $formMapper->add('user', 'sonata_type_model', ['required' => false]);
        $formMapper->add('action_id', 'choice', array(
            'choices' => $actionChoices,
            'multiple' => false
            )
        );
        $formMapper->add('created_at', DateTimeType::class, ['required' => false]);
        $formMapper->add('data', TextType::class, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $actionChoices = Activity::getFiltersActions();

        $datagridMapper->add('innovation');
        $datagridMapper->add('user');
        $datagridMapper->add('action_id', 'doctrine_orm_choice', array('label' => 'Action id',
            'field_options' => array(
                'required' => false,
                'choices' => $actionChoices
            ),
            'field_type' => 'choice'
        ));
        $datagridMapper->add('created_at');
        $datagridMapper->add('data');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $actionChoices = Activity::getFlattenActions();
        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('innovation', 'sonata_type_model', array(
            'sortable' => 'innovation.title',
        ));
        $listMapper->addIdentifier('user', 'sonata_type_model', array(
            'sortable' => 'user.firstname',
        ));
        $listMapper->addIdentifier('action_id', 'choice', array(
            'choices' => $actionChoices,
            'multiple' => false
        ));
        $listMapper->addIdentifier('key');
        $listMapper->addIdentifier('created_at');
    }

    public function getExportFields()
    {
        return array(
            'ID' => 'id',
            'Innovation' => 'innovation.title',
            'User' => 'user.getProperUsername',
            'User role' => 'user.getProperRole',
            'Action' => 'getActionLibelle',
            'Created at' => 'created_at',
            'Message' => 'getClearMessage',
            'Data' => 'data',
        );
    }
}
