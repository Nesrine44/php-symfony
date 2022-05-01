<?php
/**
 * Created by PhpStorm.
 * User: FlorianNicolas
 * Date: 21/08/2018
 * Time: 17:47
 */
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Route\RouteCollection;

class NotificationAdmin extends AbstractAdmin
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
        $formMapper->add('innovation', 'sonata_type_model', ['required' => false]);
        $formMapper->add('status', TextType::class, ['required' => false]);
        $formMapper->add('action', TextType::class, ['required' => false]);
        $formMapper->add('type', TextType::class, ['required' => false]);
        $formMapper->add('data', TextType::class, ['required' => false]);
        $formMapper->add('created_at', DateTimeType::class, ['required' => false]);
        $formMapper->add('updated_at', DateTimeType::class, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('innovation');
        $datagridMapper->add('status');
        $datagridMapper->add('action');
        $datagridMapper->add('type');
        $datagridMapper->add('created_at');
        $datagridMapper->add('data');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('innovation', 'sonata_type_model', array(
            'sortable' => 'innovation.title',
        ));
        $listMapper->addIdentifier('status');
        $listMapper->addIdentifier('action');
        $listMapper->addIdentifier('type');
        $listMapper->addIdentifier('created_at');
    }

}
