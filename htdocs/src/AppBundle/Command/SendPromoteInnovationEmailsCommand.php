<?php

namespace AppBundle\Command;

use AppBundle\Entity\Notification;
use AppBundle\Worker\PernodWorker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendPromoteInnovationEmailsCommand extends ContainerAwareCommand
{

    private $em;
    private $worker;

    public function __construct(EntityManagerInterface $em, PernodWorker $worker)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        parent::__construct();
        $this->em = $em;
        $this->worker = $worker;
    }

    protected function configure()
    {
        $this->setName('pri:send-promote-innovation-emails')
            ->setDescription('Send promote innovation emails to contact owners.')
            ->addOption('debug-mode', null, InputOption::VALUE_NONE, 'Would you send email to developer instead of contact owner?');
    }

    /**
     * Order innovations by entity
     * @param $innovations
     * @return array
     */
    public static function orderInnovationsByEntity($innovations)
    {
        $ret = array();
        foreach ($innovations as $innovation) {
            $ret[$innovation['entity_id']][] = $innovation;
        }
        return $ret;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->info('[Launch -> pri:send-promote-innovation-emails]');
        try {
            $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $debug_mode = $input->getOption('debug-mode');
            if ($settings->getIsPromoteInnovationEmailsEnabled()) {
                $mailer = $this->getContainer()->get('app.mailer');
                $promote_notifications = $this->em->getRepository('AppBundle:Notification')->getPromoteInnovationNotificationsToSend();
                foreach ($promote_notifications as $notification) {
                    $send_mail = ($debug_mode) ? 1 : $this->em->getRepository('AppBundle:Notification')->setNotificationInProgress($notification->getId());
                    if ($send_mail > 0) {
                        $mailer->sendPromoteEmailNotification($notification, $debug_mode);
                        if(!$debug_mode){
                            $notification->setStatus(Notification::STATUS_SENT);
                            $this->em->persist($notification);
                            $this->em->flush();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->getContainer()->get('logger')->error('[ERROR ON pri:send-promote-innovation-emails] : ' . $e->getMessage());
            $this->getContainer()->get('logger')->error('[Stack-Trace=' . $e->getTraceAsString() . ']');
        }
        $this->getContainer()->get('logger')->info('[End -> pri:send-promote-innovation-emails]');
    }

}