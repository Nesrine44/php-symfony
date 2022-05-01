<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\UserInnovationRight;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class AdminWsController extends Controller
{
    /**
     * Admin Backoffice - Update JS and CSS cache version.
     *
     * @Route("/api/admin/ws/up-cache", name="ws_admin_up_cache", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="old_cache_version",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="new_cache_version",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function upCacheVersionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $old_version = $settings->getCacheVersion();
        $settings->updateCacheVersion();
        $em->flush();
        $new_version = $settings->getCacheVersion();
        $ret = array(
            'old_cache_version' => $old_version,
            'new_cache_version' => $new_version,
            'status' => 'success'
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Update ping (to manually launch user full data cache).
     *
     * @Route("/api/admin/ws/up-ping", name="ws_admin_up_ping", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="old_ping",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="new_ping",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function upPingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $old_ping = $settings->getPingTimestamp();
        $settings->updatePing();
        $em->flush();
        $new_ping = $settings->getPingTimestamp();
        $ret = array(
            'old_ping' => $old_ping,
            'new_ping' => $new_ping,
            'status' => 'success'
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Manually launch Redis full data cache.
     *
     * @Route("/api/admin/ws/up-redis", name="ws_admin_up_redis", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="launched"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function updateRedisDataAction()
    {
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $redis = $this->container->get('snc_redis.default');
        $redis_prefix = $this->container->getParameter('redis_prefix');
        $redis->del($redis_prefix . 'redis_cache_progress');
        $redis->del($redis_prefix . 'pri_otherdatas');
        $redis->del($redis_prefix . 'pri_allinnovations_excel');
        $redis->del($redis_prefix . 'pri_allinnovations');
        $redis->del($redis_prefix . 'pri_allconsolidation');
        $pernodWorker->initRedisCacheProgress();
        $pernodWorker->later()->generateAllInnovationsAndConsolidation(true);
        $ret = array('status' => 'launched');
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Manually launch Redis other data cache (like users, brands, entities..).
     *
     * @Route("/api/admin/ws/generate-other-datas", name="ws_admin_generate_other_datas", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function generateOtherDatasAction()
    {
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();
        $ret = array('status' => 'success');
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Admin Backoffice - Load user rights for a specified user.
     *
     * @Route("/api/admin/ws/load-user-rights", name="ws_admin_load_user_rights", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  default="No result"
     *              ),
     *              @OA\Property(
     *                  property="html",
     *                  type="string",
     *                  default=""
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      )
     *
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function loadUserRightsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = ($request->request->get('user_id')) ? $em->getRepository('AppBundle:User')->find($request->request->get('user_id')) : null;
        if (!$user) {
            $response = new Response(json_encode(array('status' => 'error', 'html' => '', 'message' => 'No result')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $message = $user->getAdminUserRightsMessage();
        
        $items = $em->getRepository('AppBundle:UserInnovationRight')->getAdminUserInnovationRightArrayByUser($user);
        $html_data = $this->renderView('@App/admin/user_rights_inner_td.html.twig', ['items' => $items]);
        $ret = array(
            'status' => 'success',
            'message' => $message,
            'html' => $html_data,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Create, Update or Delete user rights for a specified user on specified innovations.
     *
     * @Route("/api/admin/ws/update-user-rights", name="ws_admin_update_user_rights", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="value",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_ids",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      ),
     *      @OA\Parameter(
     *          name="right",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="New right, or action (remove, read, write)"
     *      ),
     *      @OA\Parameter(
     *          name="innovation_ids",
     *          in="query",
     *          type="array",
     *          required=true,
     *          description="Array of innovations ids."
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function updateUserRightsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = ($request->request->get('user_id')) ? $em->getRepository('AppBundle:User')->find($request->request->get('user_id')) : null;
        $action = $request->request->get('right');
        if (!$user) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'No result')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $value = false;
        $innovations_ids = ($request->request->get('innovation_ids')) ? $request->request->get('innovation_ids') : array();
        if ($action == 'remove') {
            foreach ($innovations_ids as $innovations_id) {
                $user_innovation_right = $em->getRepository('AppBundle:UserInnovationRight')->findOneBy(array('user' => $user, 'innovation' => $innovations_id));
                if ($user_innovation_right) {
                    $em->remove($user_innovation_right);

                }
            }
            $em->flush();
        } else if($action == 'owner') {
            $websiteGlobalDataService = $this->container->get('app.website_global_datas');
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            foreach ($innovations_ids as $innovations_id) {
                $innovation = $em->getRepository('AppBundle:Innovation')->find($innovations_id);
                if($innovation){
                    $old_contact = $innovation->getContact();
                    if ($old_contact) {
                        $old_value = array(
                            'id' => $old_contact->getId(),
                            'title' => $old_contact->getProperUsername()
                        );
                    }
                    $new_value = array(
                        'id' => $user->getId(),
                        'title' => $user->getProperUsername()
                    );
                    $innovation->setContact($user);
                    // Update user_innovation_right
                    if ($old_contact && !$old_contact->hasAdminRights()) {
                        $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($old_contact, $innovation, UserInnovationRight::ROLE_OTHER);
                    }
                    if (!$user->hasAdminRights()) {
                        $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($user, $innovation, UserInnovationRight::ROLE_CONTACT_OWNER);
                    }
                    if ($old_value || $new_value) {
                        // Activity
                        $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, Activity::ACTION_INNOVATION_UPDATED, 'Contact', null, $old_value, $new_value);
                    }
                    $em->flush();
                    $innovation_array = $websiteGlobalDataService->getInnovationArrayById($innovations_id);
                    if($innovation_array){
                        $innovation_array['updated_at'] = $innovation->getUpdatedAt()->getTimestamp();
                        $innovation_array['contact'] = array(
                            'uid' => $user->getId(),
                            'username' => $user->getProperUsername(),
                            'email' => $user->getEmail(),
                            'picture' => $user->getPictureUrl()
                        );
                        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($innovation_array);
                    }

                }
            }
        } else {
            $value = $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRightByInnovationIds($user, $innovations_ids);
        }
        $em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
        $ret = array(
            'innovations_ids' => $innovations_ids,
            'status' => 'success',
            'value' => $value
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Update user role for a specified user on a specified innovation.
     *
     * @Route("/api/admin/ws/update-user-rights-role", name="ws_admin_update_user_rights_role", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="value",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovation_id",
     *                  type="integer"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      ),
     *      @OA\Parameter(
     *          name="role",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="New role"
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="array",
     *          required=true,
     *          description="Specified innovation id."
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function updateUserRightsRoleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = ($request->request->get('user_id')) ? $em->getRepository('AppBundle:User')->find($request->request->get('user_id')) : null;
        $role = $request->request->get('role');
        $innovation_id = $request->request->get('innovation_id');
        if (!$user || !$role || !$innovation_id) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'No result')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user_innovation_right = $em->getRepository('AppBundle:UserInnovationRight')->findOneBy(array('user' => $user, 'innovation' => $innovation_id));
        $value = false;
        if ($user_innovation_right) {
            $user_innovation_right->setRole($role);
            $em->flush();
            $value = true;
        }
        $em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
        $ret = array(
            'innovation_id' => $innovation_id,
            'status' => 'success',
            'value' => $value
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Admin Backoffice - Search innovations by title and/or entity for a specific user.
     *
     * @Route("/api/admin/ws/search-innovations", name="ws_admin_search_innovation", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  default="No result"
     *              ),
     *              @OA\Property(
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      ),
     *      @OA\Parameter(
     *          name="entity_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Entity id of the target innovations"
     *      ),
     *      @OA\Parameter(
     *          name="search_word",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="search by %title%"
     *      )
     * )
     * @OA\Tag(name="Admin Api")
     */
    public function searchInnovationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = ($request->request->get('user_id')) ? $em->getRepository('AppBundle:User')->find($request->request->get('user_id')) : null;
        if (!$user) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'No result')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $entity_id = $request->request->get('entity_id');
        $search_word = $request->request->get('search_word', '');

        $items = $em->getRepository('AppBundle:UserInnovationRight')->getAdminPossibleUserInnovationRightArrayByUser($user, $entity_id, $search_word);
        $html_data = $this->renderView('@App/admin/user_rights_inner_td.html.twig', ['items' => $items]);
        $message = ($html_data == '') ? 'No innovation found.' : null;
        $ret = array(
            'status' => 'success',
            'message' => $message,
            'html' => $html_data,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
