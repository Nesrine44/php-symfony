<?php

namespace AppBundle\Service;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Innovation;
use AppBundle\Entity\Metrics;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Stage;
use AppBundle\Entity\User;
use \Swift_Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class Mailer
 * @package AppBundle\Service
 */
class Mailer
{
    private $mailer;
    private $templating;

    private $reply;
    private $noreply;
    private $name;

    private $base_url = "https://innovation.pernod-ricard.com";
    private $em;
    private $settings;
    private $container;
    private $redis;


    public function __construct(
        Swift_Mailer $mailer,
        EngineInterface $templating,
        $reply,
        $noreply,
        $name,
        EntityManagerInterface $em,
        ContainerInterface $container,
        Client $redis
    )
    {
        $this->mailer = $mailer;
        $this->templating = $templating;

        $this->reply = $reply;
        $this->noreply = $noreply;
        $this->name = $name;


        $this->em = $em;
        $this->container = $container;
        $this->redis = $redis;
        $this->settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();

        if ($_ENV['CURRENT_PLATFORM'] == 'staging') {
            $this->base_url = "https://innovation-staging.pernod-ricard.io";
        }
        if (array_key_exists('CURRENT_MODE', $_ENV) && $_ENV['CURRENT_MODE'] == 'dev') {
            $this->base_url = "http://127.0.0.1";
        }
    }


    /**
     * Send mail.
     *
     * @param string|array $to
     * @param string $subject
     * @param $body
     * @return int
     */
    protected function sendMail($to, $subject, $body)
    {
        if ($this->settings->getIsEmailsSentToDeveloperEnabled()) {
            $to = $this->settings->getDeveloperEmail();
        }
        $mail = \Swift_Message::newInstance();
        $mail->setFrom($this->noreply, $this->name)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($this->reply, $this->name)
            ->setContentType('text/html');

        return $this->mailer->send($mail);
    }


    /**
     * Send email with default template.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @return int
     */
    public function sendDefaultEmail($to, $subject, $content)
    {
        // app/Resources/views/Emails/default.html.twig
        $template = 'emails/default.html.twig';
        $body = $this->templating->render($template, array(
            'content' => $content,
            'subject' => $subject,
            'base_url' => $this->base_url
        ));
        return $this->sendMail($to, $subject, $body);
    }


    /**
     * Send email with default template.
     *
     * @param Innovation $innovation
     * @return int
     */
    public function sendNewMakerEmail($innovation)
    {
        // app/Resources/views/Emails/new-maker.html.twig
        $to = $this->settings->getNotifierEmail();
        $template = 'emails/new-maker.html.twig';
        $subject = "New maker on the pipeline!";
        $body = $this->templating->render($template, array(
            'innovation' => $innovation,
            'subject' => $subject,
            'base_url' => $this->base_url
        ));
        return $this->sendMail($to, $subject, $body);
    }


    /**
     * Send promote email.
     *
     * @param Innovation $innovation
     * @param bool $is_debug
     * @return int
     */
    public function sendPromoteEmail($innovation, $is_debug = false)
    {
        // app/Resources/views/Emails/promote-innovation.html.twig
        $template = 'emails/promote-innovation.html.twig';
        $contact = $innovation->getContact();
        if (!$contact->getAcceptScheduledEmails()) {
            return 0;
        }
        $to = ($is_debug) ? $this->settings->getDeveloperEmail() : $contact->getEmail();
        $subject = "Hey " . $contact->getFirstname() . "! People are looking at your innovation!";
        $metrics_key = Metrics::generateKeyForUserAndInnovation($contact, $innovation);
        $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $contact, $innovation, Metrics::ACTION_ID_MAIL_PROMOTE_OPENED);
        $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $contact, $innovation, Metrics::ACTION_ID_MAIL_PROMOTE_CLICKED);
        $this->em->getRepository('AppBundle:Metrics')->createMetrics($contact, $innovation, Metrics::ACTION_MAIL_PROMOTE_SENT, $metrics_key);

        $body = $this->templating->render($template, array(
            'innovation' => $innovation,
            'contact' => $contact,
            'subject' => $subject,
            'pixel_key' => $pixel_key,
            'clicked_key' => $clicked_key,
            'nb_views' => $innovation->getNumberOfPromoteViewsThisWeek(),
            'nb_exports' => $innovation->getNumberOfPromoteExportsThisWeek(),
            'nb_shares' => $innovation->getNumberOfSharesThisWeek(),
            'base_url' => $this->base_url
        ));
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Send promote email notification.
     *
     * @param Notification $notification
     * @return int
     */
    public function sendPromoteEmailNotification(Notification $notification)
    {
        // app/Resources/views/Emails/promote-innovation.html.twig
        $template = 'emails/promote-innovation.html.twig';
        $innovation = $notification->getInnovation();
        if (!$innovation) {
            return false;
        }
        $data_notification = $notification->getDataArray();
        if (!array_key_exists('share', $data_notification)) {
            $data_notification['share'] = 0;
        }
        $sent = 1;
        $users_to_send = array();
        if ($this->settings->getIsEmailsSentToDeveloperEnabled()) { // Si les mails sont envoyés aux développeurs, on ne l'envoit qu'une fois par innovation
            $users_to_send[] = $innovation->getContact();
        } else {
            $users_to_send[] = $innovation->getContact();  // $users_to_send = $innovation->getUsersTeam(); Update POST 6.1 : on envoit seulement au contact.
        }
        foreach ($users_to_send as $user_to_send) {
            if ($user_to_send->getAcceptScheduledEmails()) {
                $to = $user_to_send->getEmail();
                $subject = "Hey " . $user_to_send->getFirstname() . "! People are looking at your innovation!";
                $metrics_key = Metrics::generateKeyForUserAndInnovation($user_to_send, $innovation);
                $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user_to_send, $innovation, Metrics::ACTION_ID_MAIL_PROMOTE_OPENED);
                $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user_to_send, $innovation, Metrics::ACTION_ID_MAIL_PROMOTE_CLICKED);
                $this->em->getRepository('AppBundle:Metrics')->createMetrics($user_to_send, $innovation, Metrics::ACTION_MAIL_PROMOTE_SENT, $metrics_key);
                $body = $this->templating->render($template, array(
                    'innovation' => $innovation,
                    'contact' => $user_to_send,
                    'subject' => $subject,
                    'pixel_key' => $pixel_key,
                    'clicked_key' => $clicked_key,
                    'nb_views' => $data_notification['views'],
                    'nb_exports' => $data_notification['exports'],
                    'nb_shares' => $data_notification['share'],
                    'base_url' => $this->base_url
                ));
                if (!$this->sendMail($to, $subject, $body)) {
                    $sent = 0;
                }
            }
        }
        return $sent;
    }

    /**
     * Send feedback feature promotion email.
     *
     * @param User $user
     * @return int
     * @throws \Exception
     */
    public function sendFeedbackFeaturePromotionEmail($user)
    {
        // app/Resources/views/emails/feedback-promotion.html.twig
        $template = 'emails/feedback-promotion.html.twig';
        $subject = "Get some feedback on your innovations with the Innovation Hub";
        $to = $user->getEmail();

        $user_innovations = $user->getOwnInnovations();
        $target_innovation = null;
        if ($user_innovations) {
            foreach ($user_innovations as $innovation) {
                if (!$innovation->isOutOfFunnel()) {
                    $target_innovation = $innovation;
                    break;
                }
            }
        }
        // No innovation in funnel = link to -> /content/manage
        if (!$target_innovation) {
            $pixel_key = 'pixel';
            $cta = '/content/manage';

        } else {
            $metrics_key = Metrics::generateKeyForUserAndInnovation($user, $target_innovation);
            $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user, $target_innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_OPENED);
            $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user, $target_innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_NEW_FEATURE_CLICKED);
            $this->em->getRepository('AppBundle:Metrics')->createMetrics($user, $target_innovation, Metrics::ACTION_MAIL_FEEDBACK_NEW_FEATURE_SENT, $metrics_key);
            $cta = '/explore/' . $target_innovation->getId() . '/tab/feedback' . '?m-key=' . $clicked_key;
        }
        $body = $this->templating->render($template, [
            'subject' => $subject,
            'pixel_key' => $pixel_key,
            'user' => $user,
            'base_url' => $this->base_url,
            'cta' => $cta,
        ]);
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Send feedback invitation email.
     *
     * @param string $to Email recipient.
     * @return int
     */
    public function sendFeedbackInvitationEmail($feedbackInvitation)
    {
        // app/Resources/views/emails/feedback-invitation.html.twig
        $template = 'emails/feedback-invitation.html.twig';
        $to = $feedbackInvitation->getUser()->getEmail();
        $subject = $feedbackInvitation->getSender()->getProperUsername() . " would like your feedback on an innovation project";

        $innovation = $feedbackInvitation->getInnovation();

        $metrics_key = Metrics::generateKeyForUserAndInnovation($feedbackInvitation->getUser(), $innovation);
        $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $feedbackInvitation->getUser(), $innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_INVITE_OPENED);
        $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $feedbackInvitation->getUser(), $innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_INVITE_CLICKED);
        $this->em->getRepository('AppBundle:Metrics')->createMetrics($feedbackInvitation->getUser(), $innovation, Metrics::ACTION_MAIL_FEEDBACK_INVITE_SENT, $metrics_key);

        $body = $this->templating->render($template, [
            'subject' => $subject,
            'pixel_key' => $pixel_key,
            'user' => $feedbackInvitation->getUser(),
            'sender' => $feedbackInvitation->getSender(),
            'invitation' => $feedbackInvitation,
            'base_url' => $this->base_url,
            'cta' => '/explore/' . $innovation->getId() . '/feedback-invitation/' . $feedbackInvitation->getToken() . '?m-key=' . $clicked_key
        ]);
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Send feedback answer email.
     *
     * @param $feedback
     * @return int
     */
    public function sendFeedbackEmail($feedback)
    {
        // app/Resources/views/emails/feedback-answer.html.twig
        $feedbackInvitation = $feedback->getInvitation();
        $template = 'emails/feedback-answer.html.twig';
        $to = $feedbackInvitation->getSender()->getEmail();
        $subject = $feedbackInvitation->getUser()->getProperUsername() . " gave you some feedback about your project on the Innovation Hub";

        $innovation = $feedbackInvitation->getInnovation();

        $metrics_key = Metrics::generateKeyForUserAndInnovation($feedbackInvitation->getSender(), $innovation);
        $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $feedbackInvitation->getSender(), $innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_ANSWER_OPENED);
        $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $feedbackInvitation->getSender(), $innovation, Metrics::ACTION_ID_MAIL_FEEDBACK_ANSWER_CLICKED);
        $this->em->getRepository('AppBundle:Metrics')->createMetrics($feedbackInvitation->getSender(), $innovation, Metrics::ACTION_MAIL_FEEDBACK_ANSWER_SENT, $metrics_key);

        $body = $this->templating->render($template, [
            'subject' => $subject,
            'pixel_key' => $pixel_key,
            'user' => $feedbackInvitation->getUser(),
            'feedback' => $feedback,
            'invitation' => $feedbackInvitation,
            'base_url' => $this->base_url,
            'cta' => '/explore/' . $innovation->getId() . '/tab/feedback' . '?m-key=' . $clicked_key
        ]);
        return $this->sendMail($to, $subject, $body);
    }


    /**
     * Send new team member email.
     *
     * @param User $sender
     * @param User $team_member
     * @param Innovation $innovation
     * @return int
     */
    public function sendNewTeamMemberEmail($sender, $team_member, $innovation)
    {
        // app/Resources/views/emails/emails/new-team-member.html.twig
        $template = 'emails/new-team-member.html.twig';
        $to = $team_member->getEmail();
        $subject = $sender->getProperUsername() . " invited you in their innovation team";

        $body = $this->templating->render($template, array(
            'innovation' => $innovation,
            'subject' => $subject,
            'team_member' => $team_member,
            'base_url' => $this->base_url
        ));
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Send new team member email.
     *
     * @param User $sender
     * @param User $user
     * @param Innovation $innovation
     * @param string $message
     * @param int|null $activity_id
     * @return int
     */
    public function sendShareInnovationEmail($sender, $user, $innovation, $message, $activity_id = null)
    {
        // app/Resources/views/emails/emails/share-innovation.html.twig
        $template = 'emails/share-innovation.html.twig';
        $to = $user->getEmail();
        $subject = $sender->getProperUsername() . " would like to share with you this project from the Innovation Hub";
        $cta = '/explore/' . $innovation->getId();
        if ($activity_id) {
            $cta .= '?key=' . Activity::generateRandomString() . '-' . $activity_id . '-' . Activity::generateRandomString();
        }
        $body = $this->templating->render($template, array(
            'innovation' => $innovation,
            'subject' => $subject,
            'user' => $user,
            'base_url' => $this->base_url,
            'message' => $message,
            'cta' => $cta
        ));
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Send promote email notification.
     *
     * @param Innovation $innovation
     * @return int
     */
    public function sendChangeStageEmail($innovation)
    {
        if ($innovation->getStage()->getId() == Stage::STAGE_ID_EXPERIMENT) {
            // app/Resources/views/Emails/emails/change-stage/experiment.html.twig
            $template = 'emails/change-stage/experiment.html.twig';
            $subject = $innovation->getTitle() . ": great content to start your experimentation journey!";
            $action_id_mail_opened = Metrics::ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_OPENED;
            $action_id_mail_clicked = Metrics::ACTION_ID_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED;
            $action_mail_sent = Metrics::ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_SENT;
        } else if ($innovation->getStage()->getId() == Stage::STAGE_ID_INCUBATE) {
            // app/Resources/views/Emails/emails/change-stage/incubate.html.twig
            $template = 'emails/change-stage/incubate.html.twig';
            $subject = $innovation->getTitle() . ": great content to start your incubation journey!";
            $action_id_mail_opened = Metrics::ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_OPENED;
            $action_id_mail_clicked = Metrics::ACTION_ID_MAIL_CHANGE_STAGE_INCUBATE_CLICKED;
            $action_mail_sent = Metrics::ACTION_MAIL_CHANGE_STAGE_INCUBATE_SENT;
        } else {
            // We don't have mail to sent to other stages...
            return 0;
        }
        $sent = 1;
        $users_to_send = array();
        if ($this->settings->getIsEmailsSentToDeveloperEnabled()) { // Si les mails sont envoyés aux développeurs, on ne l'envoit qu'une fois par innovation
            $users_to_send[] = $innovation->getContact();
        } else {
            $users_to_send = $innovation->getUsersTeam();
        }
        foreach ($users_to_send as $user_to_send) {
            $to = $user_to_send->getEmail();
            $metrics_key = Metrics::generateKeyForUserAndInnovation($user_to_send, $innovation);
            $pixel_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user_to_send, $innovation, $action_id_mail_opened);
            $clicked_key = Metrics::generateGlobalKeyForUserInnovationAndAction($metrics_key, $user_to_send, $innovation, $action_id_mail_clicked);
            $this->em->getRepository('AppBundle:Metrics')->createMetrics($user_to_send, $innovation, $action_mail_sent, $metrics_key);

            $body = $this->templating->render($template, array(
                'innovation' => $innovation,
                'contact' => $user_to_send,
                'subject' => $subject,
                'pixel_key' => $pixel_key,
                'clicked_key' => $clicked_key,
                'base_url' => $this->base_url
            ));
            if (!$this->sendMail($to, $subject, $body)) {
                $sent = 0;
            }
        }
        return $sent;
    }
}

