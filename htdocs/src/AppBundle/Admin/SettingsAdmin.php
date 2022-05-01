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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Route\RouteCollection;

class SettingsAdmin extends AbstractAdmin
{
    public function configure()
    {
        $this->setTemplate('edit', 'AppBundle:admin:edit_settings.html.twig');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        // OR remove all route except named ones
        $collection->remove('delete');
        $collection->remove('create');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('contact_email', TextType::class);
        $formMapper->add('is_https_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_video_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_tidio_chat_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_maintenance_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_beta_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_data_capture_toast_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_edition_quanti_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_edition_quali_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_project_creation_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_closed_message_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_myportal_authentication_enabled', CheckboxType::class, ['required' => false]);
        $formMapper->add('is_walkthrough_enabled', CheckboxType::class, ['required' => false]);
        $current_financial_date_options = array(
            'help' => '<div class="content-picker-buttons"><button class="btn btn-sm past">Past</button> <button class="btn btn-sm next">Next</button></div>',
            'attr' => array('class' => 'field_financial_date'),
            'required' => false
        );
        $formMapper->add('current_financial_date', TextType::class, $current_financial_date_options);
        $formMapper->add('current_trimester', IntegerType::class, array('attr' => array('class' => 'field_current_trimester')));
        $formMapper->add('open_date', TextType::class, ['required' => false]);
        $formMapper->add('close_date', TextType::class, ['required' => false]);
        $formMapper->add('open_date_libelle', TextType::class, ['required' => false, 'label' => 'Before seizure date libelle']);
        $formMapper->add('close_date_libelle', TextType::class, ['required' => false, 'label' => 'During seizure date libelle']);
        $formMapper->add('last_ns_group', IntegerType::class, ['required' => false,
            'label' => 'Last Net Sales Group (in k€)']);
        $formMapper->add('notifier_email', TextType::class, ['required' => false]);
        $formMapper->add('developer_email', TextType::class, ['required' => false]);
        $formMapper->add('is_promote_innovation_emails_enabled', CheckboxType::class, ['required' => false,
            'help' => "<p class='help-block'>If enabled, a mail will be sent every friday 12:00 UTC to each Innovation contacts with view or download during this week.</p>"
        ]);
        $formMapper->add('is_emails_sent_to_developer_enabled', CheckboxType::class, ['required' => false,
            'help' => "<p class='help-block'>If enabled, all emails, except for Developer user accounts, will be sent to developer email.</p>"
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('contact_email');
        $datagridMapper->add('is_https_enabled');
        $datagridMapper->add('is_video_enabled');
        $datagridMapper->add('is_tidio_chat_enabled');
        $datagridMapper->add('is_maintenance_enabled');
        $datagridMapper->add('is_beta_enabled');
        $datagridMapper->add('is_edition_quanti_enabled');
        $datagridMapper->add('is_edition_quali_enabled');
        $datagridMapper->add('is_project_creation_enabled');
        $datagridMapper->add('is_closed_message_enabled');
        $datagridMapper->add('is_myportal_authentication_enabled');
        $datagridMapper->add('current_financial_date');
        $datagridMapper->add('current_trimester');
        $datagridMapper->add('open_date');
        $datagridMapper->add('close_date');
        $datagridMapper->add('open_date_libelle');
        $datagridMapper->add('close_date_libelle');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('contact_email');
    }
}
