<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Activity;
use AppBundle\Entity\Export;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class ExportController extends Controller implements ActionController
{
    /**
     * Get export progression.
     *
     * @Route("/api/export/get-progress", name="export_get_progress", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="progress",
     *                  type="integer",
     *                  default=0
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="export_id",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Export ID"
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function getProgressAction()
    {
        $request = Request::createFromGlobals();
        $redis = $this->get("snc_redis.default");
        $export_id = $request->request->get('export_id');
        $activity_id = $request->request->get('activity_id');
        $ret = array(
            'progress' => Settings::EXPORT_PROGRESS_MIN,
            'status' => "progress"
        );
        if($export_id){
            $progress = $redis->get($export_id);
            if($progress) {
                $ret['progress'] = $progress;
            }
            if($progress == Settings::EXPORT_PROGRESS_MAX){
                $redis->del($export_id);
                $em = $this->getDoctrine()->getManager();
                $em->getRepository('AppBundle:Activity')->terminateExportActivity($activity_id);
                $ret['status'] = 'finished';
            }
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    

    /**
     * Get export directory for user.
     *
     * @param Controller $controller
     * @return string
     */
    public static function getExportDirectoryForUser($controller){
        $root_dir =  $controller->get('kernel')->getRootDir();
        $user = $controller->getUser();
        $directory_path = $root_dir.'/../web/exports/'.$user->getId().'/';
        return $directory_path;
    }
    /**
     * Get relative path for user and filename.
     *
     * @param User $user
     * @param string $filename
     * @return string
     */
    public static function getRelativePathForUserAndFilename($user, $filename){
        return $_ENV['AWS_BASE_URL'].$_ENV['CURRENT_PLATFORM'].'/exports/'.$user->getId().'/'.$filename;
    }

    /**
     * Delete file if exist.
     *
     * @param string $file_path
     * @return string
     */
    public static function deleteFileIfExist($file_path){
        if(is_file($file_path)) {
            unlink($file_path); // delete file
        }
    }

    /**
     * Generate export key for user
     *
     * @param User $user
     * @return string
     */
    public static function generateExportKeyForUser($user){
        return md5($user->getId().'-'.time());
    }

    /**
     * Launch "active users" export (xls). (only available for HQ)
     *
     * @Route("/api/export/active-users", name="export_active_users", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportActiveUsersAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if(!$user->hasAdminRights()){
            throw new \Exception('[403] - You have no right to this page.');
        }
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Active users from ' . date("Y-m-d", strtotime("-3 Months")) . ' to ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);

        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, null, Activity::ACTION_EXPORT_EXCEL, 'active-users');

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => 'active_user',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key
            )
        );

        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Launch "newsletter users" export (xls). (only available for HQ)
     *
     * @Route("/api/export/newsletter-users", name="export_newsletter_users", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportNewsletterUsersAction()
    {
        $user = $this->getUser();
        if(!$user->hasAdminRights()){
            throw new \Exception('[403] - You have no right to this page.');
        }
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Newsletter users - ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => 'newsletter_user',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key
            )
        );

        $ret = array(
            'export_id' => $export_key,
            'status' => 'progress',
            'url' => $relative_path
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "team matrix update" export (xls). (only available for HQ)
     *
     * @Route("/api/export/team-matrix-update", name="export_team_matrix_update", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportTeamMatrixUpdateAction()
    {
        $user = $this->getUser();
        if(!$user->hasAdminRights()){
            throw new \Exception('[403] - You have no right to this page.');
        }
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Team Matrix update - ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => 'matrix',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key
            )
        );

        $ret = array(
            'export_id' => $export_key,
            'status' => 'progress',
            'url' => $relative_path
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "team matrix update without duplicate" export (xls). (only available for HQ)
     *
     * @Route("/api/export/team-matrix-update-without-duplicate", name="export_team_matrix_update_without_duplicate", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportTeamMatrixUpdateWithoutDuplicateAction()
    {
        $user = $this->getUser();
        if(!$user->hasAdminRights()){
            throw new \Exception('[403] - You have no right to this page.');
        }
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Team Matrix update without duplicate - ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => 'matrix_without_duplicate',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
            )
        );

        $ret = array(
            'export_id' => $export_key,
            'status' => 'progress',
            'url' => $relative_path
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "Innovations excel" export (xls).
     *
     * @Route("/api/export/innovations-excel", name="export_innovations_excel", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_ids",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="params_string",
     *          in="query",
     *          type="string",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="string",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportInnovationsExcelAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $params_string = $request->request->get('params_string');
        $type = $request->request->get('type', 'innovations-excel');
        $params_array = ($params_string) ? json_decode($params_string, true) : array();
        $innovations_ids = (array_key_exists('innovations_ids', $params_array)) ? $params_array['innovations_ids'] : array();

        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Innovations - ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);
        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, null, Activity::ACTION_EXPORT_EXCEL, $type);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => 'innovations',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
                '--user_id' => $user->getId(),
                '--innovations_ids' => json_encode($innovations_ids)
            )
        );
        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path,
            'type' => $type,
            'innovations_ids' => $innovations_ids,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "innovations complete excel" export (xls). (only available for HQ)
     *
     * @Route("/api/export/innovations-complete-excel", name="export_innovations_complete_excel", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportInnovationsCompleteExcelAction()
    {
        $user = $this->getUser();
        if(!$user->hasAdminRights()){
            throw new \Exception('[403] - You have no right to this page.');
        }
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $em = $this->getDoctrine()->getManager();
        $type = 'complete';

        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Complete innovations - ' . date('Y-m-d') . '.xlsx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);
        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, null, Activity::ACTION_EXPORT_EXCEL, $type);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_excel',
                'export_type' => $type,
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
                '--user_id' => $user->getId()
            )
        );
        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path,
            'type' => $type,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "entity performance review" export (ppt).
     *
     * @Route("/api/export/entity-performance-review", name="export_entity_performance_review", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportEntityPerformanceReviewAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $type = 'entity-performance-review';
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Entity performance review - ' . date('Y-m-d').'.pptx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);
        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, null, Activity::ACTION_EXPORT_PPT, $type);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_ppt',
                'export_type' => 'contributor',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
                '--user_id' => $user->getId()
            )
        );
        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path,
            'type' => $type,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Launch "Innovation to ppt" export (ppt).
     *
     * @Route("/api/export/innovation-to-ppt", name="export_innovation_to_ppt", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="export_type",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportInnovationToPPTAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $innovation_id = $request->request->get('innovation_id');
        $export_type = $request->request->get('export_type');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        if(!$innovation){
            throw new \Exception('[403] - You have no right to this page.');
        }

        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = ExportController::cleanFilename($innovation->getTitle().' - ' . date('Y-m-d').'.pptx');
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);
        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, $innovation, Activity::ACTION_EXPORT_PPT, $export_type);
        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_ppt',
                'export_type' => $export_type,
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
                '--user_id' => $user->getId(),
                '--innovation_id' => $innovation->getId(),
            )
        );
        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path,
            'type' => $export_type,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Clean filename
     * @param $filename
     * @return string|string[]|null
     */
    public static function cleanFilename($filename){
        return preg_replace('/[\s]+/mu', ' ',   // Remove multiple spaces
            str_replace(array('\\','/','?','%','*',':','|', '"','<','>', '=', "'", ';', '#'),'',  // Remove reserved filename characters
                $filename
            )
        );
    }


    /**
     * Launch "Innovations ppt" export (ppt).
     *
     * @Route("/api/export/innovations-ppt", name="export_innovations_ppt", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="export_id",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="activity_id",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="progress"
     *              ),
     *              @OA\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="params_array",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="params_string",
     *          in="query",
     *          type="string",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="string",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function exportInnovationsPptAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $params_string = $request->request->get('params_string');
        $type = $request->request->get('type', 'innovations-ppt');
        $params_array = ($params_string) ? json_decode($params_string, true) : array();

        $directory_path = ExportController::getExportDirectoryForUser($this);
        $filename = 'Export overview quali - ' . date('Y-m-d').'.pptx';
        $relative_path = ExportController::getRelativePathForUserAndFilename($user, $filename);
        ExportController::deleteFileIfExist($directory_path.$filename);

        $redis = $this->get("snc_redis.default");
        $export_key = self::generateExportKeyForUser($user);
        $redis->set($export_key, Settings::EXPORT_PROGRESS_CREATED);
        $activity = $em->getRepository('AppBundle:Activity')->createExportActivity($user, null, Activity::ACTION_EXPORT_EXCEL, $type);

        $pernodWorker = $this->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->later()->runCommand(
            array(
                'command' => 'pri:generate_ppt',
                'export_type' => 'overview_quali',
                '--path' => $directory_path.$filename,
                '--export_id' => $export_key,
                '--user_id' => $user->getId(),
                '--params' => json_encode($params_array)
            )
        );
        $ret = array(
            'export_id' => $export_key,
            'activity_id' => $activity->getId(),
            'status' => 'progress',
            'url' => $relative_path,
            'type' => $type,
            'params_array' => $params_array,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }




}