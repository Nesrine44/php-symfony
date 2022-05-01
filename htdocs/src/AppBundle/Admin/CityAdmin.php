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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CityAdmin extends AbstractOtherDataAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('geoname_id', IntegerType::class, ['required' => false]);
        $formMapper->add('continent_code', TextType::class, ['required' => false]);
        $formMapper->add('continent_name', TextType::class, ['required' => false]);
        $formMapper->add('country_iso_code', TextType::class, ['required' => false]);
        $formMapper->add('country_name', TextType::class, ['required' => false]);
        $formMapper->add('city_name', TextType::class, ['required' => false]);
        $formMapper->add('time_zone', TextType::class, ['required' => false]);
        $formMapper->add('is_in_european_union', CheckboxType::class, ['required' => false]);
        $formMapper->add('picture_url', TextType::class, ['required' => false]);

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('city_name');
        $datagridMapper->add('country_name');
        $datagridMapper->add('picture_url');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('city_name');
        $listMapper->addIdentifier('country_name');
        $listMapper->addIdentifier('picture_url');
    }
}
