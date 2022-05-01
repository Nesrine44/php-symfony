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
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserEntityAdmin extends AbstractOtherDataAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        // OR remove all route except named ones
        $collection->remove('create');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', TextType::class, ['required' => false,
            'help' => "<p class='help-block'>If not empty, this will be the displayed libelle.</p>"
        ]);
        $formMapper->add('pr_title', TextType::class, [
            'required' => true,
            'attr' => array(
                'readonly' => true
            ),
            'help' => "<p class='help-block'>Libelle returned by Pernod Ricard Employee API.</p>"
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('pr_title');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title');
        $listMapper->addIdentifier('pr_title');
    }

}
