<?php

namespace AppBundle\Command;

use AppBundle\Entity\Notification;
use AppBundle\Worker\PernodWorker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class SendFeedbackPromotionEmailsCommand extends ContainerAwareCommand
{

    private $em;
    private $worker;
    const CMD_NAME = 'pri:send-feedback-promotion-emails';

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
        $this->setName(self::CMD_NAME)
            ->setDescription('Send feedback feature promotion emails to innovation owners.')
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'target user id or "makers"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->info('[Launch -> ' . self::CMD_NAME . ']');
        try {
            $mailer = $this->getContainer()->get('app.mailer');
            $to = $input->getOption('to');
            if ($to == 'makers') {
                $all_makers = $this->em->getRepository('AppBundle:User')->getAllMakers();
                foreach ($all_makers as $maker) {
                    $mailer->sendFeedbackFeaturePromotionEmail($maker);
                }
            } elseif (is_int($to)) {
                $target_user = $this->em->getRepository('AppBundle:User')->find($to);
                if($target_user) {
                    $mailer->sendFeedbackFeaturePromotionEmail($target_user);
                }
            }
        } catch (\Exception $e) {
            $this->getContainer()->get('logger')->error('[ERROR ON ' . self::CMD_NAME . ' : ' . $e->getMessage());
            $this->getContainer()->get('logger')->error('[Stack-Trace=' . $e->getTraceAsString() . ']');
        }
        $this->getContainer()->get('logger')->info('[End -> ' . self::CMD_NAME . ']');
    }

}