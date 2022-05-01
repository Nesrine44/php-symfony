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

class SearchHistoryAdmin extends AbstractAdmin
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
        $formMapper->add('title', TextType::class, ['required' => false]);
        $formMapper->add('user', 'sonata_type_model', ['required' => false]);
        $formMapper->add('url', TextType::class, ['required' => false]);
        $formMapper->add('css_class', TextType::class, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('user');
        $datagridMapper->add('url');
        $datagridMapper->add('css_class');
        $datagridMapper->add('created_at');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('title');
        $listMapper->addIdentifier('user', 'sonata_type_model', array(
            'sortable' => 'user.firstname',
        ));
        $listMapper->addIdentifier('url');
        $listMapper->addIdentifier('created_at');
    }

    public function getExportFields()
    {
        return array(
            'ID' => 'id',
            'Title' => 'title',
            'User' => 'user.getProperUsername',
            'Url' => 'url',
            'CSS Class' => 'css_class',
            'Created at' => 'created_at',
        );
    }
}
