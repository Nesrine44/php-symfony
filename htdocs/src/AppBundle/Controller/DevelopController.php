<?php

namespace AppBundle\Controller;

use AppBundle\Command\DevelopCommand;
use AppBundle\Entity\Activity;
use AppBundle\Entity\UserEntity;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DevelopController extends Controller
{
    /**
     * Display environment variables.
     *
     * @Route("/develop", name="develop_action")
     */
    public function developAction()
    {
        $websiteGlobalDataService = $this->container->get('app.website_global_datas');
        $ret = array(
            'sys_get_temp_dir' => sys_get_temp_dir(),
            'env' => $_ENV,
            "redis-data-status" => $websiteGlobalDataService->checkRedisDatas(),
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display full data cache.
     *
     * @Route("/develop/cache", name="develop_cache_action")
     */
    public function fullDataAction()
    {
        $user = $this->getUser();
        $websiteGlobalDataService = $this->container->get('app.website_global_datas');
        $ret = array(
            "status" => $websiteGlobalDataService->checkRedisDatas(),
            "full_data" => $websiteGlobalDataService->user_full_data($user),
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Send a mail.
     *
     * @Route("/develop/sendmail", name="develop_sendmail_action")
     */
    public function sendMailAction()
    {
        $mailer = $this->container->get('app.mailer');
        $request = Request::createFromGlobals();
        $to = $request->query->get('type','florian.nicolas@corellis.eu');
        $subject = $request->query->get('subject','Email test');
        $content = $request->query->get('content','This is a test for sending emails');
        $ret = array('mail-send' => $mailer->sendDefaultEmail($to, $subject, $content));
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Send a promote mail.
     *
     * @Route("/develop/promote-email/{id}", name="develop_promote_email_action", requirements={"id"="\d+"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendPromoteMailAction($id)
    {
        $mailer = $this->container->get('app.mailer');
        $em = $this->getDoctrine()->getManager();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
        if(!$innovation){
            $ret = array('status' => 'error', 'message' => 'Innovation with id='.$id.' not found.');
        }else{
            $ret = array('mail-send' => $mailer->sendPromoteEmail($innovation, true));
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Send a promote mail.
     *
     * @Route("/develop/promote-email-cron", name="develop_promote_email_cron_action")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendPromoteMailCronAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $live = $request->query->get('live', false);
        $debug = $request->query->get('debug', true);
        $innovations_mailed = array();
        if ($live) {
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            if ($settings->getIsPromoteInnovationEmailsEnabled()) {
                $mailer = $this->container->get('app.mailer');
                $promote_notifications = $em->getRepository('AppBundle:Notification')->getPromoteInnovationNotificationsToSend();
                foreach ($promote_notifications as $notification) {
                    if(!$debug) {
                        $mailer->sendPromoteEmailNotification($notification);
                    }
                    $innovations_mailed[] = $notification->getInnovation()->getTitle();
                }
            }
        }else {
            $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->later()->runCommand(
                array(
                    'command' => 'pri:send-promote-innovation-emails',
                    '--debug-mode' => $debug
                )
            );
        }
        $ret = array(
            'live' => $live,
            'innovations_mailed' => $innovations_mailed
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Delete all files from /web/exports/ directory.
     *
     * @Route("/develop/purge-exports", name="develop_purge_exports_action")
     */
    public function purgeExportsAction()
    {
        $ret = array();
        try{
            $root_dir =  $this->get('kernel')->getRootDir();
            $directory_path = $root_dir.'/../web/exports/';
            $ret['deleted'] = array();
            $objects = scandir($directory_path); // get all file names
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != '.gitkeep') {
                    $file = $directory_path.$object;
                    if(is_file($file)) {
                        unlink($file); // delete file
                    }elseif(is_dir($file)) {
                        DevelopController::deleteDirectory($file); // delete directory
                        $ret['deleted'][] = $object;
                    }
                }
            }
            reset($objects);
            $ret['files'] = $objects;
            $ret['directory_path'] = $directory_path;
        }catch (\Exception $e){
            $ret = array('error' => $e->getMessage());
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Do a ls -alF to a $_GET['dir'].
     *
     * @Route("/develop/ls", name="develop_ls_action")
     */
    public function lsAction()
    {
        $request = Request::createFromGlobals();
        $dir = $request->query->get('dir', '/web/exports/');
        $limit = $request->query->get('limit', 10);
        try{
            $root_dir =  $this->get('kernel')->getRootDir();
            $directory_path = $root_dir.'/../'.$dir;
            $ret = DevelopController::lsDirectory($directory_path, $limit);
        }catch (\Exception $e){
            $ret = array('error' => $e->getMessage());
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display logs files.
     *
     * @Route("/develop/logs", name="develop_logs_action")
     */
    public function logsAction()
    {
        $request = Request::createFromGlobals();
        $env = $request->query->get('env', 'prod');
        $ddl = $request->query->get('ddl', 0);
        $root_dir =  $this->get('kernel')->getRootDir();
        $file_directory = $root_dir.'/../var/logs/'.$env.'.log';
        try{
            if($ddl == 1){
                $response = new BinaryFileResponse($file_directory);
                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

                return $response;
            }
            echo nl2br(file_get_contents($file_directory));
            die;
        }catch (\Exception $e){
            $ret = array('error' => $e->getMessage());
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Return a ls -alF directory into an array.
     *
     * @param $dir
     * @param bool $limit
     * @return array|null
     */
    public static function lsDirectory($dir, $limit = false){
        if (!is_dir($dir)) {
            return null;
        }
        $ret = array();
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..' || $item == '.gitkeep') {
                continue;
            }
            $file = $dir.$item;
            $infos = null;
            if(is_file($file) || is_dir($file)) {
                $type = (is_file($file)) ? "FILE" : "DIR";
                $user_infos = posix_getpwuid(fileowner($file));
                $infos = $type;
                $infos .= ' | '. substr(sprintf('%o', fileperms($file)), -4); // permissions
                $infos .= ' | '.$user_infos['uid']; // uid
                $infos .= ' | '.$user_infos['name']; // ui name
                $infos .= ' | '.filesize($file);  // filesize
                $infos .= ' | '.date ("Y-m-d H:i:s.", filemtime($file)); // last_update
                $infos .= ' | '.$file; // path
            }
            if(is_file($file)) {
                $ret[] = $infos;
            }elseif(is_dir($file)) {
                $ret[] = array(
                    'infos' => $infos,
                    'content' => DevelopController::lsDirectory($file.'/', $limit)
                );
            }
            if($limit && count($ret) > $limit){
                $ret[] = '...';
                break;
            }
        }
        return $ret;
    }

    /**
     * Delete a directory and its content.
     *
     * @param $dir
     * @return bool
     */
    public static function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..' || $item == '.gitkeep') {
                continue;
            }
            if (!DevelopController::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }
        return rmdir($dir);
    }

    /**
     * Update users.
     *
     * @Route("/develop/update-users", name="develop_update_users_action")
     */
    public function updateUsersAction()
    {
        $request = Request::createFromGlobals();
        $update = $request->query->get('update', 1);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => DevelopCommand::CMD_NAME,
                'action' => DevelopCommand::ACTION_UPDATE_USERS_WITH_EMPLOYEE_API,
                '--update-full-data' => ($update == 1)
            )
        );
        $response = new Response(json_encode(array('status' => 'launched')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Update users.
     *
     * @Route("/develop/send-feature-feedback", name="develop_send_feature_feedback_action")
     */
    public function sendFeatureFeedbackAction()
    {
        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $request = Request::createFromGlobals();
        $to = $request->query->get('to', null);
        $status = 'undefined to';
        $send = false;
        if($to) {
            if($to === 'makers'){
                $send = true;
            }else{
                $to = intval($to);
                $em = $this->getDoctrine()->getManager();
                $user = ($to) ? $em->getRepository('AppBundle:User')->find($to) : null;
                if(!$user){
                    $status = 'unknown user';
                }else{
                    $send = true;
                }
            }
        }
        if($send){
            $status = 'command launched';
            $pernodWorker->later()->runCommand(
                array(
                    'command' => 'pri:send-feedback-promotion-emails',
                    '--to' => $to
                )
            );
        }
        $response = new Response(json_encode(array('status' => $status, 'to' => $to)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Fix markets activities.
     *
     * @Route("/develop/fix-markets-activities", name="develop_fix_markets_activities_action")
     */
    public function fixMarketsActivitiesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();
        $query->select('i')
            ->from('AppBundle:Innovation', 'i')
            ->where('i.markets IS NOT NULL');
        $innovations = $query->getQuery()->getResult();
        $changes = array();
        foreach ($innovations as $innovation){
            if($innovation->getMarkets()) {
                $activity = $innovation->getActivities()->filter(function ($activity) {
                    return $activity->getActionId() == Activity::ACTION_INNOVATION_UPDATED && \strpos($activity->getData(), '{"key":"markets_in"') !== false;
                })->first();
                if ($activity) {
                    $markets = $innovation->getMarkets();
                    $data_array = $activity->getDataArray();
                    if ($data_array['new_value'] != $markets) {
                        $data_array['new_value'] = $markets;
                        $activity->setDataArray($data_array);
                        $em->flush();
                        $changes[] = $innovation->getTitle();
                    }
                }
            }
        }

        $response = new Response(json_encode(array('status' => 'success', 'changes' => $changes)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Fix additional_pictures order.
     *
     * @Route("/develop/fix-additional_pictures", name="develop_additional_pictures_action")
     */
    public function fixAdditionalPicturesOrderAction()
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder();
        $query =  $queryBuilder
            ->select([
                'i',
                'additional_pictures'
            ])
            ->from('AppBundle:Innovation', 'i')
            ->leftJoin('i.additional_pictures', 'additional_pictures');
        $query->where('i.is_active = :is_active')
            ->setParameter('is_active', true);
        $innovations = $query->getQuery()->getResult();
        $changes = array();
        foreach ($innovations as $innovation){
            if($innovation->getAdditionalPictures() && count($innovation->getAdditionalPictures()) > 0) {
                $order = 0;
                foreach ($innovation->getAdditionalPictures() as $additionalPicture){
                    $additionalPicture->setOrder($order);
                    $order++;
                }
                $changes[] = $innovation->getTitle();
            }
        }
        $em->flush();

        $response = new Response(json_encode(array('status' => 'success', 'changes' => $changes)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }



    /**
     * Update innovations sort_score
     *
     * @Route("/develop/update-sort-score", name="develop_update_sort_score_action")
     */
    public function updateInnovationSortScoreAction()
    {
        $request = Request::createFromGlobals();
        $update = $request->query->get('update', 1);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => DevelopCommand::CMD_NAME,
                'action' => DevelopCommand::ACTION_UPDATE_SORT_SCORE,
                '--update-full-data' => ($update == 1)
            )
        );
        $response = new Response(json_encode(array('status' => 'launched')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Update innovations sort_score
     *
     * @Route("/develop/update-city-pictures", name="develop_update_sort_score_action")
     */
    public function updateCityPicturesAction()
    {
        $request = Request::createFromGlobals();
        $update = $request->query->get('update', 1);
        $force = $request->query->get('force', 1);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => DevelopCommand::CMD_NAME,
                'action' => DevelopCommand::ACTION_UPDATE_UPDATE_CITY_PICTURES,
                '--force-update' => ($force == 1),
                '--update-full-data' => ($update == 1)
            )
        );
        $response = new Response(json_encode(array('status' => 'launched')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * UpdateRoleFinanceContact
     *
     * @Route("/develop/update-role-finance-contact", name="develop_update_role_finance_contact")
     */
    public function updateRoleFinanceContact()
    {
        $request = Request::createFromGlobals();
        $update = $request->query->get('update', 1);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => DevelopCommand::CMD_NAME,
                'action' => DevelopCommand::ACTION_UPDATE_ROLE_FINANCE_CONTACT,
                '--update-full-data' => ($update == 1)
            )
        );
        $response = new Response(json_encode(array('status' => 'launched')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


}
