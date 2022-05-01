<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\AdditionalPicture;
use AppBundle\Entity\Innovation;
use AppBundle\Entity\OpenQuestion;
use AppBundle\Entity\Picture;
use AppBundle\Entity\SearchHistory;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInnovationRight;
use AppBundle\Event\InnovationEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Swagger\Annotations as OA;

class WsController extends Controller implements ActionController
{

    /**
     * Global Api - Get ping
     *
     * @Route("/api/global/ping", name="ws_get_ping", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="ping",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="user_roles",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="main_classes",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="need_update",
     *                  type="boolean"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function getPingAction()
    {
        $user = $this->getUser();
        $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->getCurrentSettings();
        $websiteGlobalDataService = $this->container->get('app.website_global_datas');
        $em = $this->getDoctrine()->getManager();
        $user->setLastLogin(new \DateTime());
        $em->flush();
        $ret = array(
            'ping' => $settings->getPingTimestamp(),
            'user_roles' => $user->getUserRolesArray(),
            'main_classes' => $websiteGlobalDataService->main_classes($user),
            'need_update' => false,
            'csrf' => self::generateCSRFTokens($this)
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Generate CSRF Tokens
     *
     * @param Controller $controller
     * @return array
     */
    public static function generateCSRFTokens($controller)
    {
        $tokenManager = $controller->get('security.csrf.token_manager');
        return array(
            'hub_token' => $tokenManager->getToken('hub_token')->getValue(),
            'create_innovation' => $tokenManager->getToken('create_innovation')->getValue(),
        );
    }

    /**
     * Global Api - Get full data for the current user
     *
     * @Route("/api/global/full-data", name="ws_update_user_innovations", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function updateUserInnovationsAction()
    {
        $user = $this->getUser();
        $ret = $this->get('app.website_global_datas')->user_full_data($user);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Create an innovation
     *
     * @Route("/api/innovation/create", name="ws_create_innovation", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function createInnovationAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();

        if (!$settings->getIsProjectCreationEnabled()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Project creation is currently closed')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('create_innovation', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $innovation = new Innovation();
        $innovation->setTitle(Settings::getXssCleanString($request->request->get('title')));

        if ($request->request->get('is_multi_brand')) {
            $innovation->setIsMultiBrand($request->request->get('is_multi_brand'));
        }
        if ($request->request->get('is_multi_brand') != "1" &&
            $request->request->get('have_a_mother_brand') &&
            intval($request->request->get('have_a_mother_brand')) == 1 &&
            $request->request->get('brand') &&
            is_numeric($request->request->get('brand')) // if is string, it's a new brand, we do nothing about it
        ) {
            $brand = $em->getRepository('AppBundle:Brand')->find($request->request->get('brand'));
            if ($brand) {
                $innovation->setBrand($brand);
            }
        }
        if ($request->request->get('have_a_mother_brand') && intval($request->request->get('have_a_mother_brand')) == 0) {
            $innovation->setIsNewToTheWorld(1);
        }
        if ($request->request->get('growth_model')) {
            $innovation->setGrowthModel($request->request->get('growth_model'));
        }
        if ($request->request->get('replace_existing_product')) {
            $innovation->setIsReplacingExistingProduct($request->request->get('replace_existing_product'));
            if (intval($request->request->get('replace_existing_product')) == 1 && $request->request->get('existing_product')) {
                $innovation->setReplacingProduct($request->request->get('existing_product'));
            }
        }
        if ($request->request->get('start_date')) {
            $innovation->setStartDate(new \DateTime($request->request->get('start_date')));
        }

        if ($request->request->get('classification')) {
            $classification = $em->getRepository('AppBundle:Classification')->find($request->request->get('classification'));
            if ($classification) {
                $innovation->setClassification($classification);
            }
        }

        if ($request->request->get('current_stage')) {
            $stage = $em->getRepository('AppBundle:Stage')->find($request->request->get('current_stage'));
            if ($stage) {
                $innovation->setStage($stage);
            }
        }

        if ($request->request->get('entity')) {
            $entity = $em->getRepository('AppBundle:Entity')->find($request->request->get('entity'));
            if ($entity) {
                $innovation->setEntity($entity);
            }
        }

        $innovation->setContact($user);
        $em->persist($innovation);
        $em->flush();

        if ($user->hasNoInnovations()) {
            $mailer = $this->container->get('app.mailer');
            $mailer->sendNewMakerEmail($innovation);
        }

        if (!$user->hasAdminRights()) {
            $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($user, $innovation, UserInnovationRight::ROLE_CONTACT_OWNER);
        }

        // Updating all data :
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $dispatcher = new EventDispatcher();
        $innovation_event = new InnovationEvent();
        $liip = $this->container->get('liip_imagine.service.filter');

        // Creating activity Activity
        $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, Activity::ACTION_INNOVATION_CREATED);

        $dispatcher->dispatch(InnovationEvent::NAME, $innovation_event)->onCreateInnovationAction($pernodWorker, $innovation->toArray($settings, $liip));

        // refreshing user to adding rights
        $the_user = $em->getRepository('AppBundle:User')->find($user->getId());

        $ret = array(
            'id' => $innovation->getId(),
            'full_data' => $this->get('app.website_global_datas')->getInnovationArrayById($innovation->getId()),
            'optimization_init' => $this->get('app.website_global_datas')->user_full_data($the_user),
            ''
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Check if innovation need local update
     *
     * @Route("/api/innovation/check", name="ws_check_innovation_update", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function checkInnovationUpdateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $updated_at = $request->request->get('updated_at', null);
        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'updated' => false, 'innovation_json' => null)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $ret = array(
            'status' => 'success',
            'data' => array(),
            'updated' => false,
            'innovation_json' => null
        );
        if (!$updated_at || ($updated_at && $innovation->getUpdatedAt()->getTimestamp() > $updated_at)) {
            $ret['updated'] = true;
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $liip = $this->container->get('liip_imagine.service.filter');
            $innovation_array = $innovation->toArray($settings, $liip);
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($innovation_array);
            $ret['innovation_json'] = $innovation_array;
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Get financial data tables html for date
     *
     * @Route("/api/innovation/financial/data-for-date", name="api_innovation_financial_data_for_date", methods={"POST"})
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
     *                  property="not_yet",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="html",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="automated_data",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="date",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function getInnovationFinancialDataForDateAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $ret = array(
            'html' => '',
            'automated_data' => '',
            'not_yet' => false,
        );
        $date = $request->request->get('date', null);
        $id = $request->request->get('id');
        $innovation = ($id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($id) : null;

        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$date) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Date not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if ($innovation && $date) {
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $the_current_date = $settings->getCurrentFinancialDate();
            $ret['not_yet'] = ($date > $the_current_date);
            $automated_data = $em->getRepository('AppBundle:FinancialData')->getHtmlAutomatedFinancialDataForDate($settings, $innovation, $date);
            $ret['automated_data'] = $this->renderView('@App/innovation/financial-data-automated-body.html.twig', $automated_data);
            $ret['html'] = $this->renderView('@App/innovation/financial-data-body.html.twig', $em->getRepository('AppBundle:FinancialData')->getHtmlFinancialDataForDate($user, $settings, $innovation, $date));

        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Load more activities
     *
     * @Route("/api/innovation/activities/load-more", name="ws_get_last_innovation_activities", methods={"POST"})
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
     *                  property="data",
     *                  type="string|array"
     *              ),
     *              @OA\Property(
     *                  property="data-type",
     *                  type="string",
     *                  default="array"
     *              ),
     *              @OA\Property(
     *                  property="offset",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="limit",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="count",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="promote",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="data-type",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false
     *      )
     *
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function getLastInnovationActivitiesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();

        $id = $request->request->get('id', null);
        $data_type = $request->request->get('data-type', 'array');
        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);
        if (!$id) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'data-type' => $data_type)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $ret = array(
            'status' => 'success',
            'data' => array(),
            'data-type' => $data_type,
            'offset' => $offset,
            'limit' => $limit,
            'count' => 0
        );
        $need_innovation_name = false;
        if ($id == 'all') {
            if (!$user->hasAdminRights()) {
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $except_actions = array(
                Activity::ACTION_INNOVATION_CHANGE_STATUS,
                Activity::ACTION_INNOVATION_ONLINE_OFFLINE,
                Activity::ACTION_INNOVATION_TOP_STORY,
                Activity::ACTION_INNOVATION_BIG_BET,
            );
            $activities = $em->getRepository('AppBundle:Activity')->getAllActivitiesForGlobal($offset, $limit, $except_actions, false);
            $need_innovation_name = true;
        } else {
            $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
            if (!$innovation) {
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            if (!$user->canEditThisInnovation($innovation)) {
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $except_actions = array(
                Activity::ACTION_INNOVATION_CHANGE_STATUS,
                Activity::ACTION_INNOVATION_ONLINE_OFFLINE,
                Activity::ACTION_INNOVATION_TOP_STORY,
                Activity::ACTION_INNOVATION_BIG_BET,
                Activity::ACTION_EXPORT_EXCEL,
                Activity::ACTION_EXPORT_PPT
            );
            $activities = $em->getRepository('AppBundle:Activity')->getAllActivitiesForInnovation($id, $offset, $limit, $except_actions, true);
            if ($innovation && $offset == 0) {
                $ret['promote'] = array(
                    'view' => array(
                        'count' => $innovation->getNumberOfPromoteViews(),
                        'html' => $em->getRepository('AppBundle:Activity')->getPromoteActivitiesHTMLForInnovationByActionId($innovation, Activity::ACTION_PROMOTE_INNOVATION_VIEW, 0, 3),
                    ),
                    'export' => array(
                        'count' => $innovation->getNumberOfPromoteExports(),
                        'html' => $em->getRepository('AppBundle:Activity')->getPromoteActivitiesHTMLForInnovationByActionId($innovation, Activity::ACTION_PROMOTE_INNOVATION_EXPORT, 0, 3),
                    ),
                    'share' => array(
                        'count' => $innovation->getNumberOfShares(),
                        'html' => $em->getRepository('AppBundle:Activity')->getShareActivitiesHTMLForInnovation($innovation, 0, 3),
                    )
                );
            }
        }
        $ret['data'] = $activities;
        $ret['count'] = count($activities);
        if ($data_type == 'html') { // je veux un retour en HTML
            $html_data = "";
            foreach ($activities as $activity) {
                $html_data .= $activity->toHtml($need_innovation_name);
            }
            $ret['data'] = $html_data;
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Innovation Api - Get team members
     *
     * @Route("/api/innovation/get-team", name="ws_get_innovation_people", methods={"POST"})
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
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified innovation id"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function getInnovationPeopleAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $members = $em->getRepository('AppBundle:UserInnovationRight')->getUserInnovationRightByInnovation($innovation);
        $html_data = $this->renderView('@App/innovation/team-member-list.html.twig', array('members' => $members));
        $ret = array(
            'status' => 'success',
            'html' => $html_data,
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Innovation Api - Update innovation form
     * @Route("/api/innovation/update-form", name="ws_update_innovation_for", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    function updateInnovationFormAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $value = null;
        $old_value = null;
        $activity_action = Activity::ACTION_INNOVATION_UPDATED;
        $activity_key = null;
        $innovation_id = $request->request->get('id');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;

        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'mesage' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $data = $request->request->all();
        $ret = array(
            'status' => 'success',
            'data' => $data,
        );

        if (array_key_exists('title', $data) && $data['title']) {
            $old_value = $innovation->getTitle();
            $innovation->setTitle(Settings::getXssCleanString($data['title']));
            $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'title', null, $old_value, $data['title']);
        }

        if (array_key_exists('contact', $data) && $data['contact']) {
            $new_contact = $em->getRepository('AppBundle:User')->find($data['contact']);
            if ($new_contact) {
                $old_contact = $innovation->getContact();
                if ($old_contact) {
                    $old_value = array(
                        'id' => $old_contact->getId(),
                        'title' => $old_contact->getProperUsername()
                    );
                }
                $new_value = array(
                    'id' => $new_contact->getId(),
                    'title' => $new_contact->getProperUsername()
                );
                $innovation->setContact($new_contact);
                $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'contact', null, $old_value, $new_value);

                // Update user_innovation_right
                if ($old_contact && !$old_contact->hasAdminRights()) {
                    $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($old_contact, $innovation, UserInnovationRight::ROLE_OTHER);
                }
                if (!$new_contact->hasAdminRights()) {
                    $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($new_contact, $innovation, UserInnovationRight::ROLE_CONTACT_OWNER);
                }

            }
        }

        if (array_key_exists('entity', $data) && $data['entity']) {
            $new_entity = $em->getRepository('AppBundle:Entity')->find($data['entity']);
            if ($new_entity) {
                $old_value = ($innovation->getEntity()) ? $innovation->getEntity()->getId() : null;
                $innovation->setEntity($new_entity);
                $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'entity', null, $old_value, $data['entity']);
            }
        }

        if (array_key_exists('brand', $data) && $data['brand']) {
            $new_brand = $em->getRepository('AppBundle:Brand')->find($data['brand']);
            if ($new_brand) {
                $old_brand = $innovation->getBrand();
                if ($old_brand) {
                    $old_value = array(
                        'id' => $old_brand->getId(),
                        'title' => $old_brand->getTitle()
                    );
                }
                $new_value = array(
                    'id' => $new_brand->getId(),
                    'title' => $new_brand->getTitle()
                );
                $innovation->setBrand($new_brand);
                $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'brand', null, $old_value, $new_value);
            }
        }


        if (array_key_exists('new_to_the_world', $data)) {
            $old_value = $innovation->getIsNewToTheWorld();
            $innovation->setIsNewToTheWorld($data['new_to_the_world']);
            $ret['new_to_the_world_updated'] = $innovation->getIsNewToTheWorld();
            $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'new_to_the_world', null, $old_value, $data['new_to_the_world']);
        }

        if (array_key_exists('is_multi_brand', $data)) {
            $old_value = $innovation->getIsMultiBrand();
            $innovation->setIsMultiBrand($data['is_multi_brand']);
            $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, 'is_multi_brand', null, $old_value, $data['is_multi_brand']);
        }

        $em->flush();


        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        // we don't pass liip because we don't need to resize images
        $ret['full_data'] = $innovation->toArray($settings);

        // Event update all_innovation data
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Update innovation inline
     * TODO : REFACTOR
     * @Route("/api/innovation/update", name="ws_update_innovation_inline", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    function updateInnovationInlineAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $old_value = null;
        $new_value = null;
        $activity_action = Activity::ACTION_INNOVATION_UPDATED;
        $activity_key = null;
        $innovation_id = ($request->request->get('pk')) ? $request->request->get('pk') : $request->request->get('id');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        $name = $request->request->get('name', null);
        $value = $request->request->get('value', null);
        if (!$innovation || !$name) {
            $response = new Response(json_encode(array('status' => 'error', 'mesage' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $new_value = Settings::getXssCleanString($value);
        $activity_key = $name;
        switch ($name) {
            case 'entity':
                $new_entity = $em->getRepository('AppBundle:Entity')->find($new_value);
                if ($new_entity) {
                    $old_value = ($innovation->getEntity()) ? $innovation->getEntity()->getId() : null;
                    $innovation->setEntity($new_entity);
                }
                break;
            case 'growth_model':
                $old_value = $innovation->getGrowthModel();
                $innovation->setGrowthModel($new_value);
                break;
            case 'is_multi_brand':
                $old_value = $innovation->getIsMultiBrand();
                $innovation->setIsMultiBrand($new_value);
                break;
            case 'in_prisma':
                $old_value = $innovation->getIsInPrisma();
                $innovation->setIsInPrisma($new_value);
                break;
            case 'title':
                $old_value = $innovation->getTitle();
                $innovation->setTitle($new_value);
                break;
            case 'Contact':
                $new_contact = $em->getRepository('AppBundle:User')->find($new_value);
                if ($new_contact) {
                    $old_contact = $innovation->getContact();
                    if ($old_contact) {
                        $old_value = array(
                            'id' => $old_contact->getId(),
                            'title' => $old_contact->getProperUsername()
                        );
                    }
                    $new_value = array(
                        'id' => $new_contact->getId(),
                        'title' => $new_contact->getProperUsername()
                    );
                    $innovation->setContact($new_contact);

                    // Update user_innovation_right
                    if ($old_contact && !$old_contact->hasAdminRights()) {
                        $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($old_contact, $innovation, UserInnovationRight::ROLE_OTHER);
                    }
                    if (!$new_contact->hasAdminRights()) {
                        $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($new_contact, $innovation, UserInnovationRight::ROLE_CONTACT_OWNER);
                    }
                }
                break;
            case 'brand':
                if (!$new_value) {
                    $old_brand = $innovation->getBrand();
                    if ($old_brand) {
                        $old_value = array(
                            'id' => $old_brand->getId(),
                            'title' => $old_brand->getTitle()
                        );
                    }
                    $new_value = null;
                    $innovation->setBrand(null);
                } else {
                    $new_brand = $em->getRepository('AppBundle:Brand')->find($new_value);
                    if ($new_brand) {
                        $old_brand = $innovation->getBrand();
                        if ($old_brand) {
                            $old_value = array(
                                'id' => $old_brand->getId(),
                                'title' => $old_brand->getTitle()
                            );
                        }
                        $new_value = array(
                            'id' => $new_brand->getId(),
                            'title' => $new_brand->getTitle()
                        );
                        $innovation->setBrand($new_brand);
                    }
                }
                break;
            case 'story':
                $old_value = $innovation->getStory();
                $innovation->setStory($new_value);
                break;
            case 'classification':
                $new_classification = $em->getRepository('AppBundle:Classification')->findOneBy(array('title' => $new_value));
                if ($new_classification) {
                    $old_value = ($innovation->getClassification()) ? $innovation->getClassification()->getTitle() : null;
                    $innovation->setClassification($new_classification);
                }
                break;
            case 'innovation_type':
                $new_type = $em->getRepository('AppBundle:Type')->findOneBy(array('title' => $new_value));
                if ($new_type) {
                    $old_value = ($innovation->getType()) ? $innovation->getType()->getTitle() : null;
                    $innovation->setType($new_type);
                }
                break;
            case 'start_date':
                $old_value = ($innovation->getStartDate()) ? $innovation->getStartDate()->getTimestamp() + 7200 : null;
                $value_datetime = new \DateTime($new_value);
                $new_value = $value_datetime->getTimestamp() + 7200;
                $innovation->setStartDate($value_datetime);
                break;
            case 'market_date':
                $old_value = ($innovation->getInMarketDate()) ? $innovation->getInMarketDate()->getTimestamp() + 7200 : null;
                $value_datetime = new \DateTime($new_value);
                $new_value = $value_datetime->getTimestamp() + 7200;
                $innovation->setInMarketDate($value_datetime);
                break;
            case 'consumer_opportunity':
                $new_coop = $em->getRepository('AppBundle:ConsumerOpportunity')->find($new_value);
                if ($new_coop) {
                    $old_value = ($innovation->getConsumerOpportunity()) ? $innovation->getConsumerOpportunity()->getId() : null;
                    $innovation->setConsumerOpportunity($new_coop);
                }
                break;
            case 'category':
                $old_value = $innovation->getCategory();
                $innovation->setCategory($new_value);
                break;
            case 'is_frozen':
                $activity_action = Activity::ACTION_INNOVATION_FROZEN;
                $old_value = $innovation->getIsFrozen();
                $new_frozen_value = ($new_value == '1');
                $innovation->setIsFrozen($new_frozen_value);
                break;
            case 'replace_existing_product':
                $old_value = ($innovation->getIsReplacingExistingProduct()) ? '1' : '0';
                $innovation->setIsReplacingExistingProduct($new_value);
                break;
            case 'existing_product':
                $old_value = $innovation->getReplacingProduct();
                $innovation->setReplacingProduct($new_value);
                break;
            case 'moc':
                $new_moc = $em->getRepository('AppBundle:MomentOfConsumption')->findOneBy(array('title' => $new_value));
                if ($new_moc) {
                    $old_value = ($innovation->getMomentOfConsumption()) ? $innovation->getMomentOfConsumption()->getTitle() : null;
                    $innovation->setMomentOfConsumption($new_moc);
                }
                break;
            case 'business_drivers':
                $new_bd = $em->getRepository('AppBundle:BusinessDriver')->findOneBy(array('title' => $new_value));
                if ($new_bd) {
                    $old_value = ($innovation->getBusinessDriver()) ? $innovation->getBusinessDriver()->getTitle() : null;
                    $innovation->setBusinessDriver($new_bd);
                }
                break;
            case 'growth_strategy':
                $new_pp = $em->getRepository('AppBundle:PortfolioProfile')->findOneBy(array('title' => $new_value));
                if ($new_pp) {
                    $old_value = ($innovation->getPortfolioProfile()) ? $innovation->getPortfolioProfile()->getTitle() : null;
                    $innovation->setPortfolioProfile($new_pp);
                }
                break;
            case 'in_prisma':
                $old_value = ($innovation->getIsInPrisma()) ? '1' : '0';
                $innovation->setIsInPrisma($new_value);
                break;
            case 'why_invest_in_this_innovation':
                $old_value = $innovation->getWhyInvestInThisInnovation();
                $innovation->setWhyInvestInThisInnovation($new_value);
                break;
            case 'unique_experience':
                $old_value = $innovation->getUniqueExperience();
                $innovation->setUniqueExperience($new_value);
                break;
            case 'value_proposition':
                $old_value = $innovation->getUniqueness();
                $innovation->setUniqueness($new_value);
                break;
            case 'new_business_opportunity':
                $old_value = $innovation->getNewBusinessOpportunity();
                $innovation->setNewBusinessOpportunity($value);
                break;
            case 'investment_model':
                $old_value = $innovation->getInvestmentModel();
                $innovation->setInvestmentModel($value);
                break;
            case 'as_seperate_pl':
                $old_value = ($innovation->getAsSeperatePl()) ? '1' : '0';
                $innovation->setAsSeperatePl($value);
                break;
            case 'idea_description':
                $old_value = $innovation->getIdeaDescription();
                $innovation->setIdeaDescription($value);
                break;
            case 'strategic_intent_mission':
                $old_value = $innovation->getStrategicIntentMission();
                $innovation->setStrategicIntentMission($value);
                break;
            case 'project_owner_disponibility':
                $old_value = $innovation->getProjectOwnerDisponibility();
                $innovation->setProjectOwnerDisponibility($value);
                break;
            case 'full_time_employees':
                $old_value = $innovation->getFullTimeEmployees();
                $innovation->setFullTimeEmployees($value);
                break;
            case 'external_text':
                $old_value = $innovation->getExternalText();
                $innovation->setExternalText($value);
                break;
            case 'consumer_insight':
                $old_value = $innovation->getConsumerInsight();
                $innovation->setConsumerInsight($new_value);
                break;
            case 'early_adopter_persona':
                $old_value = $innovation->getEarlyAdopterPersona();
                $innovation->setEarlyAdopterPersona($new_value);
                break;
            case 'source_of_business':
                $old_value = $innovation->getSourceOfBusiness();
                $innovation->setSourceOfBusiness($new_value);
                break;
            case 'abv':
                $old_value = $innovation->getAlcoholByVolume();
                $innovation->setAlcoholByVolume($new_value);
                break;
            case 'universal_key_information_1':
                $old_value = $innovation->getUniversalKeyInformation1();
                $innovation->setUniversalKeyInformation1($new_value);
                break;
            case 'universal_key_information_2':
                $old_value = $innovation->getUniversalKeyInformation2();
                $innovation->setUniversalKeyInformation2($new_value);
                break;
            case 'universal_key_information_3':
                $old_value = $innovation->getUniversalKeyInformation3();
                $innovation->setUniversalKeyInformation3($new_value);
                break;
            case 'universal_key_information_3_vs':
                $old_value = $innovation->getUniversalKeyInformation3Vs();
                $innovation->setUniversalKeyInformation3Vs($new_value);
                break;
            case 'universal_key_information_4':
                $old_value = $innovation->getUniversalKeyInformation4();
                $innovation->setUniversalKeyInformation4($new_value);
                break;
            case 'universal_key_information_4_vs':
                $old_value = $innovation->getUniversalKeyInformation4Vs();
                $innovation->setUniversalKeyInformation4Vs($new_value);
                break;
            case 'universal_key_information_5':
                $old_value = $innovation->getUniversalKeyInformation5();
                $innovation->setUniversalKeyInformation5($new_value);
                break;
            case 'proofs_of_traction_picture_1_legend':
                $old_value = $innovation->getPotLegend1();
                $innovation->setPotLegend1($new_value);
                break;
            case 'proofs_of_traction_picture_2_legend':
                $old_value = $innovation->getPotLegend2();
                $innovation->setPotLegend2($new_value);
                break;
            case 'universal_key_learning_so_far':
                $old_value = $innovation->getKeyLearningSoFar();
                $innovation->setKeyLearningSoFar($new_value);
                break;
            case 'universal_next_steps':
                $old_value = $innovation->getNextSteps();
                $innovation->setNextSteps($new_value);
                break;
            case 'have_earned_any_money_yet':
                $old_value = ($innovation->getIsEarningAnyMoneyYet()) ? '1' : '0';
                $innovation->setIsEarningAnyMoneyYet($new_value);
                break;
            case 'plan_to_make_money':
                $old_value = $innovation->getPlanToMakeMoney();
                $innovation->setPlanToMakeMoney($new_value);
                break;
            case 'markets_in':
                $old_value = $innovation->getMarkets();
                $innovation->setMarkets($new_value);
                break;
            case 'video_link':
                $old_value = $innovation->getVideoUrl();
                $innovation->setVideoUrl($new_value);
                break;
            case 'video_password':
                $old_value = $innovation->getVideoPassword();
                $innovation->setVideoPassword($new_value);
                break;
            case 'ibp_link':
                $old_value = $innovation->getIbpUrl();
                $innovation->setIbpUrl($new_value);
                break;
            case 'website_url':
                $old_value = $innovation->getMybrandsUrl();
                $innovation->setMybrandsUrl($new_value);
                break;
            case 'press_release_link':
                $old_value = $innovation->getPressUrl();
                $innovation->setPressUrl($new_value);
                break;
            default:
                break;
        }
        $em->persist($innovation);
        $em->flush();
        if ($activity_key && ($old_value || $new_value)) {
            // Activity
            $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, $activity_key, null, $old_value, $new_value);
        }

        $ret = array(
            'data' => array(
                'old_value' => $old_value,
                'new_value' => $old_value,
                'name' => $name
            ),
            'entity' => array(),
            'id' => $innovation->getId()
        );

        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        // we don't pass liip because we don't need to resize images
        $ret['full_data'] = $innovation->toArray($settings);

        // Event update all_innovation data
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Change stage
     *
     * @Route("/api/innovation/change-stage", name="ws_change_stage_innovation", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function changeStageInnovationtAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $innovation_id = $request->request->get('id');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        $action = $request->request->get('action', null);
        if (!$innovation) {
            $ret = array(
                'status' => 'error',
                'message' => 'A problem appears, please reload the page or contact an administrator',
                'error_id' => 'popup-select-change-stage'
            );
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array(
                'status' => 'error',
                'message' => 'You have no right to do this action',
                'error_id' => 'popup-select-change-stage'
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$action) {
            $ret = array(
                'status' => 'error',
                'message' => 'This field is required',
                'error_id' => 'popup-select-change-stage'
            );
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $title = $innovation->getTitle();
        if ($action == 'delete') {
            // Activity
            $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, Activity::ACTION_INNOVATION_DELETED);
            $innovation->setIsActive(false);
            $em->persist($innovation);
            $em->flush();
            $em->getRepository('AppBundle:UserInnovationRight')->deleteAllForInnovation($innovation);
            $ret = array(
                'message' => "node deleted",
                'title' => $title,
                'id' => $innovation->getId(),
                'full_data' => array()
            );
            // Updating all data :
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $dispatcher = new EventDispatcher();
            $innovation_event = new InnovationEvent();
            $dispatcher->dispatch(InnovationEvent::NAME, $innovation_event)->onDeleteInnovationAction($pernodWorker, $innovation->getId());

            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $current_stage = intval($action);
            $new_stage = $em->getRepository('AppBundle:Stage')->find($current_stage);
            $send_email = false;
            if ($new_stage) {
                $old_value_stage = ($innovation->getStage()) ? $innovation->getStage()->getId() : null;
                $new_value_stage = $current_stage;
                $innovation->setStage($new_stage);
                $em->persist($innovation);
                $em->flush();

                if ($new_value_stage && $new_value_stage != $old_value_stage) {
                    $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, Activity::ACTION_INNOVATION_CHANGE_STAGE, 'current_stage', null, $old_value_stage, $new_value_stage);
                }
                $send_email = true;
            }
            $ret = array(
                'data' => array(),
                'entity' => null,
                'id' => $innovation->getId(),
            );
            $em->clear();
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
            $ret['full_data'] = $innovation->toArray($settings);

            if ($send_email) {
                $mailer = $this->container->get('app.mailer');
                //$mailer->sendChangeStageEmail($innovation);
            }
            // Event update all_innovation data
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * Innovation Api - Update financial data
     *
     * @Route("/api/innovation/financial/update", name="ws_update_financial_data_for_date", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *          )
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateFinancialDataForDateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation_id = $request->request->get('id');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$settings->getIsEditionQuantiEnabled() && !$user->hasAdminRights()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $date = $request->request->get('date', null);
        if (!$date) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Date is missing')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $data = $request->request->all();
        $full_data = null;
        $is_valid = null;
        $to_log = null;
        foreach ($data as $key => $value) {
            if (!in_array($key, array('id', 'date', 'token'))) {
                $value = Settings::getXssCleanString($value);
                $fd = $em->getRepository('AppBundle:FinancialData')->createOrUpdateFinancialData($user, $innovation, $key, $value);
                $to_log = $fd->getValue();
            }
        }
        $ret = array(
            'data' => $data,
            'entity' => null,
            'submit_button' => null,
            'hide_state' => false,
            '$to_log' => $to_log,
            'id' => $innovation_id,
        );
        // Reload innovation
        $em->clear();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        $ret['is_valid'] = $innovation->isValidFinancialData($settings);
        $full_data = $innovation->toArray($settings);
        $automated_data = $em->getRepository('AppBundle:FinancialData')->getHtmlAutomatedFinancialDataForDate($settings, $innovation, $date);
        $ret['automated_data'] = $this->renderView('@App/innovation/financial-data-automated-body.html.twig', $automated_data);
        $ret['full_data'] = $full_data;

        // Event update all_innovation data
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($full_data);


        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Innovation Api - Update performance review
     *
     * @Route("/api/innovation/performance-review/update", name="ws_update_performance_review", methods={"POST"})
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
     *                  property="full_data",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Performance review key"
     *      ),
     *      @OA\Parameter(
     *          name="value",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Performance review value"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updatePerformanceReviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $innovation_id = $request->request->get('id');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $full_data = null;
        $is_valid = null;
        $automated_data = null;

        $data_saved = null;

        $key = $request->request->get('name');
        $value = Settings::getXssCleanString($request->request->get('value'));

        if ($key && $value) {
            $em->getRepository('AppBundle:PerformanceReview')->createOrUpdatePerformanceReview($user, $innovation, $key, $value);
        }
        $ret = array(
            'status' => 'success',
        );
        // Reload innovation
        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        $ret['full_data'] = $innovation->toArray($settings);

        // Event update all_innovation data
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Upload innovation picture
     *
     * @Route("/api/innovation/picture/upload", name="ws_upload_picture_innovation", methods={"POST"})
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
     *                  property="url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="full_data",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="target",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="target picture"
     *      ),
     *      @OA\Parameter(
     *          name="position",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="position, in case of additional_picture"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function uploadPictureInnovationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $files = $request->files->all();
        $activity_key = null;
        $activity_action = Activity::ACTION_INNOVATION_UPDATED;
        $old_value = null;
        $new_value = null;
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $target = $request->request->get('target');
        $position = $request->request->get('position');

        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $enabled_targets = array(
            'beautyshot_picture',
            'additional_picture',
            'packshot_picture',
            'pot_picture_1',
            'pot_picture_2'
        );
        $ret = array(
            'data' => array(),
            'files' => $files,
        );
        if (!$target || ($target && !in_array($target, $enabled_targets))) {
            $ret = array(
                'status' => 'error',
                'message' => 'Wrong target',
            );
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if ($innovation) {
            $awsS3Uploader = $this->get('app.s3_uploader');
            $ret['id'] = $innovation->getId();
            foreach ($files as $a_file) {
                /* @var UploadedFile $file */
                $file = $a_file[0];
                $mimeType = $file->getMimeType();
                if (!in_array($mimeType, array('image/gif', 'image/jpeg', 'image/png'))) {
                    $ret = array(
                        'status' => 'error',
                        'message' => 'Wrong file mime type: please upload a JPG, PNG or GIF file"',
                    );
                    $response = new Response(json_encode($ret));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                $explode = explode('.', $file->getClientOriginalName());
                $extension = $explode[count($explode) - 1];
                $fileName = md5(uniqid()) . '.' . $extension;
                $file->move(
                    $this->getParameter('upload_dir'),
                    $fileName
                );
                $ret['new_name'] = $fileName;
                $ret['file'] = $file;
                $picture = new Picture();
                $picture->setFilename($fileName);
                $em->persist($picture);
                # upload to aws s3
                $path = $this->getParameter('upload_dir') . $fileName;
                $awsS3Uploader->uploadFile('uploads/' . $fileName, $path);
                $ret['url'] = $picture->guessThumbnail();
                if ($file) {
                    $ret['file'] = $file;
                    if ($target == 'beautyshot_picture') {
                        $activity_key = 'beautyshot_picture';
                        $old_value = ($innovation->getBeautyshotPicture()) ? 'picture' : null;
                        if ($innovation->getBeautyshotPicture()) {
                            // delete from aws s3
                            $url = $innovation->getBeautyshotPicture()->getPath();
                            $awsS3Uploader->deleteFile($url);
                            $em->remove($innovation->getBeautyshotPicture());
                        }
                        $innovation->setBeautyshotPicture($picture);
                    } elseif ($target == 'additional_picture' && $position !== null) {
                        $activity_key = 'additional_picture';
                        $additional_picture = new AdditionalPicture();
                        $additional_picture->setInnovation($innovation);
                        $additional_picture->setPicture($picture);
                        $additional_picture->setOrder($position);
                        $em->persist($additional_picture);
                    } elseif ($target == 'packshot_picture') {
                        $activity_key = 'packshot_picture';
                        $old_value = ($innovation->getPackshotPicture()) ? 'picture' : null;
                        if ($innovation->getPackshotPicture()) {
                            // delete from aws s3
                            $url = $innovation->getPackshotPicture()->getPath();
                            $awsS3Uploader->deleteFile($url);
                            $em->remove($innovation->getPackshotPicture());
                        }
                        $innovation->setPackshotPicture($picture);
                    } elseif ($target == 'pot_picture_1') {
                        $activity_key = 'pot_picture_1';
                        $old_value = ($innovation->getPotPicture1()) ? 'picture' : null;
                        if ($innovation->getPotPicture1()) {
                            // delete from aws s3
                            $url = $innovation->getPotPicture1()->getPath();
                            $awsS3Uploader->deleteFile($url);
                            $em->remove($innovation->getPotPicture1());
                        }
                        $innovation->setPotPicture1($picture);
                    } elseif ($target == 'pot_picture_2') {
                        $activity_key = 'pot_picture_2';
                        if ($innovation->getPotPicture2()) {
                            // delete from aws s3
                            $url = $innovation->getPotPicture1()->getPath();
                            $awsS3Uploader->deleteFile($url);
                            $em->remove($innovation->getPotPicture2());
                        }
                        $old_value = ($innovation->getPotPicture2()) ? 'picture' : null;
                        $innovation->setPotPicture2($picture);

                    }
                    $em->persist($innovation);
                    $em->flush();

                    if ($activity_action && $activity_key) {
                        // Activity
                        $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, $activity_key, null, $old_value, $new_value);
                    }
                }
            }
        }
        if ($innovation) {
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $liip = $this->container->get('liip_imagine.service.filter');
            $ret['position'] = $position;
            $ret['full_data'] = $innovation->toArray($settings, $liip);

            // Event update all_innovation data
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Delete innovation picture
     *
     * @Route("/api/innovation/picture/delete", name="ws_delete_picture_innovation", methods={"POST"})
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
     *                  property="full_data",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="target",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="target picture"
     *      ),
     *      @OA\Parameter(
     *          name="position",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="position, in case of additional_picture"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function deletePictureInnovationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $old_value = null;
        $new_value = null;
        $activity_action = null;
        $activity_key = null;
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;

        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $target = $request->request->get('target');
        $position = $request->request->get('position');
        $enabled_targets = array(
            'beautyshot_picture',
            'additional_picture',
            'packshot_picture',
            'pot_picture_1',
            'pot_picture_2'
        );
        $ret = array(
            'data' => array(),
            'position' => $position,
        );
        if (!$target || ($target && !in_array($target, $enabled_targets))) {
            $ret = array(
                'status' => 'error',
                'message' => 'Wrong target',
            );
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if ($innovation) {
            $awsS3Uploader = $this->get('app.s3_uploader');
            $activity_action = Activity::ACTION_INNOVATION_UPDATED;
            if ($target == 'beautyshot_picture' && $innovation->getBeautyshotPicture()) {
                $activity_key = 'beautyshot_picture';
                $old_value = 'picture';
                $picture = $innovation->getBeautyshotPicture();
                // delete from aws s3
                $awsS3Uploader->deleteFile($picture->getPath());
                $em->remove($picture);
                $ret['deleted'] = true;
                $innovation->setBeautyshotPicture();
            } elseif ($target == 'additional_picture' && $position !== null) {
                $additional_picture = $innovation->getAdditionalPictureByOrder(intval($position));
                if ($additional_picture) {
                    // delete from aws s3
                    $awsS3Uploader->deleteFile($additional_picture->getPicture()->getPath());
                    $em->remove($additional_picture);
                    $innovation->removeAdditionalPicture($additional_picture);
                    $order = 0;
                    foreach ($innovation->getAdditionalPictures() as $additionalPicture) {
                        $additionalPicture->setOrder($order);
                        $order++;
                    }
                    $activity_key = 'additional_picture';
                    $ret['deleted'] = true;
                } else {
                    $ret['deleted'] = false;
                }
            } elseif ($target == 'packshot_picture' && $innovation->getPackshotPicture()) {
                $old_value = 'picture';
                $picture = $innovation->getPackshotPicture();
                // delete from aws s3
                $awsS3Uploader->deleteFile($picture->getPath());
                $em->remove($picture);
                $innovation->setPackshotPicture();
                $ret['deleted'] = true;
                $activity_key = 'packshot_picture';
            } elseif ($target == 'pot_picture_1' && $innovation->getPotPicture2()) {
                $activity_key = 'pot_picture_1';
                $old_value = 'picture';
                $picture = $innovation->getPotPicture1();
                // delete from aws s3
                $awsS3Uploader->deleteFile($picture->getPath());
                $em->remove($picture);
                $innovation->setPotPicture1();
                $ret['deleted'] = true;
            } elseif ($target == 'pot_picture_2' && $innovation->getPotPicture2()) {
                $activity_key = 'pot_picture_2';
                $old_value = 'picture';
                $picture = $innovation->getPotPicture2();
                // delete from aws s3
                $awsS3Uploader->deleteFile($picture->getPath());
                $em->remove($picture);
                $innovation->setPotPicture2();
                $ret['deleted'] = true;
            }
            $em->persist($innovation);
            $em->flush();
            if ($activity_action && $activity_key) {
                // Activity
                $em->getRepository('AppBundle:Activity')->createActivity($user, $innovation, $activity_action, $activity_key, null, $old_value, $new_value);
            }
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $liip = $this->container->get('liip_imagine.service.filter');
            $ret['id'] = $innovation->getId();
            $ret['position'] = $position;
            $ret['full_data'] = $innovation->toArray($settings, $liip);

            // Event update all_innovation data
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Update graph picture
     *
     * @Route("/api/innovation/financial/update-graph-picture", name="ws_update_financial_graph_picture", methods={"POST"})
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
     *                  property="full_data",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="data",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="base64",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateFinancialGraphPictureAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $base64 = $request->request->get('base64');
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;

        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => null)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $ret = array(
            'data' => array(),
            'src' => '',
            'base64' => $base64,
        );
        if ($innovation && $base64) {
            $awsS3Uploader = $this->get('app.s3_uploader');
            $fileName = md5(uniqid()) . '.png';
            $ret['filepath'] = $this->getParameter('upload_dir') . $fileName;
            self::saveImgFromBase64($base64, $this->getParameter('upload_dir') . $fileName);
            $picture = new Picture();
            $picture->setFilename($fileName);
            $em->persist($picture);
            # upload to aws s3
            $path = $this->getParameter('upload_dir') . $fileName;
            $awsS3Uploader->uploadFile('uploads/' . $fileName, $path);
            $ret['url'] = $picture->guessThumbnail();
            if ($innovation->getFinancialGraphPicture()) {
                $em->remove($innovation->getFinancialGraphPicture());
            }
            $innovation->setFinancialGraphPicture($picture);
            $em->persist($innovation);
            $em->flush();
        }
        if ($innovation) {
            $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
            $liip = $this->container->get('liip_imagine.service.filter');
            $ret['full_data'] = $innovation->toArray($settings, $liip);
            $ret['id'] = $innovation->getId();

            // Event update all_innovation data
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Save image from base64
     * @param $base64
     * @param $path
     */
    public static function saveImgFromBase64($base64, $path)
    {
        $img = str_replace('data:image/png;base64,', '', $base64);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        //make sure you are the owner and have the rights to write content
        file_put_contents($path, $data);
    }

    /**
     * Innovation Api - Update user innovation right
     *
     * @Route("/api/innovation/user-right/update", name="ws_update_user_innovation_right", methods={"POST"})
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
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="uid",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="User innovation right id"
     *      ),
     *      @OA\Parameter(
     *          name="action",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="user_name",
     *          in="query",
     *          type="string",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="user_email",
     *          in="query",
     *          type="string",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateUserInnovationRightAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $uid = $request->request->get('uid');
        $target_user = ($request->request->get('uid')) ? $em->getRepository('AppBundle:User')->find($request->request->get('uid')) : null;
        $user_name = $request->request->get('user_name');
        $user_email = $request->request->get('user_email');
        $action = $request->request->get('action');
        $role = $request->request->get('role');
        $user = $this->getUser();
        $ret = array(
            'status' => 'error',
            'message' => 'Very strange error [Code : PR-0ZUIR01]',
        );

        if (!$innovation || !$action) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$uid) {
            if ($action == 'add') {
                if (!$user_name) {
                    $ret = array(
                        'status' => 'field_error',
                        'message' => 'This field is required',
                        'error_id' => 'new-ui-name'
                    );
                    $response = new Response(json_encode($ret));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } elseif (!$user_email) {
                    $ret = array(
                        'status' => 'field_error',
                        'message' => 'This field is required',
                        'error_id' => 'new-ui-email'
                    );
                    $response = new Response(json_encode($ret));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } elseif (strpos($user_email, 'pernod-ricard.com') === false) {
                    $ret = array(
                        'status' => 'field_error',
                        'message' => 'You must use a @pernod-ricard.com email address',
                        'error_id' => 'new-ui-email'
                    );
                    $response = new Response(json_encode($ret));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                $target_user = $em->getRepository('AppBundle:User')->createPernodRicardUser($this->container, $user_email, $user_name);
            }
            if (!$target_user) {
                $ret = array('status' => 'error', 'message' => 'User not found');
                $response = new Response(json_encode($ret));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        if ($action == 'add' || $action == 'update') {
            $em->getRepository('AppBundle:UserInnovationRight')->createOrUpdateUserInnovationRight($target_user, $innovation, $role, $this->getUser());
            $members = $em->getRepository('AppBundle:UserInnovationRight')->getUserInnovationRightByInnovation($innovation, $target_user);
            $html_data = $this->renderView('@App/innovation/team-member-list.html.twig', array('members' => $members));
            $ret = array(
                'status' => 'success',
                'html' => $html_data
            );
            $em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
            if ($action == 'add') {
                $mailer = $this->container->get('app.mailer');
                $mailer->sendNewTeamMemberEmail($user, $target_user, $innovation);
            }
        } elseif ($action == 'delete') {
            $deleted = $em->getRepository('AppBundle:UserInnovationRight')->deleteInnovationRight($target_user, $innovation, $this->getUser());
            $ret = array(
                'status' => 'success',
                'html' => '',
                'deleted' => $deleted
            );
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Create simple activity
     *
     * @Route("/api/activity/create", name="ws_generate_activity", methods={"POST"})
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
     *      ),
     *      @OA\Parameter(
     *          name="action_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified action id"
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified type",
     *          default=0
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function generateActivityAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $action_id = $request->request->get('action_id');
        if (!$action_id || !$user) {
            $ret = array('status' => 'error');
        } else {
            $data_array = array();
            if ($action_id == Activity::ACTION_DOWNLOAD_INNOVATION_BOOK) {
                $type = $request->request->get('type');
                $data_array = array(
                    'type' => $type
                );
            }
            $em->getRepository('AppBundle:Activity')->createActivity($user, null, $action_id, null, $data_array);
            $ret = array('status' => 'success');
        }

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Get metrics count for an innovation (views, shares, exports)
     *
     * @Route("/api/innovation/metrics/count", name="ws_get_promote_metrics_values", methods={"POST"})
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
     *                  property="nb_views",
     *                  type="integer",
     *                  default=0
     *              ),
     *              @OA\Property(
     *                  property="nb_exports",
     *                  type="integer",
     *                  default=0
     *              ),
     *              @OA\Property(
     *                  property="nb_shares",
     *                  type="integer",
     *                  default=0
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_d",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="inherit_action",
     *          in="query",
     *          type="boolean",
     *          description="Inherit action",
     *          default=false
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function getPromoteMetricsValuesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $innovation_id = $request->request->get('innovation_d');
        $inherit_action = $request->request->get('inherit_action');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        if (!$innovation || ($innovation && !$innovation->isEnabledOnExplore() && !$user->hasAccessToInnovation($innovation))) {
            $ret = array('status' => 'error');
        } else {
            $ret = array('status' => 'success', 'created' => false);
            if ($inherit_action && !$user->hasAdminRights() && !$user->getUserInnovationRightForAnInnovation($innovation)) {
                $em->getRepository('AppBundle:Activity')->createPromoteActivity($user, $innovation, $inherit_action);
                $ret['created'] = true;
            }
            $ret['nb_views'] = $innovation->getNumberOfPromoteViews();
            $ret['nb_exports'] = $innovation->getNumberOfPromoteExports();
            $ret['nb_shares'] = $innovation->getNumberOfShares();
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Share an innovation
     *
     * @Route("/api/innovation/share", name="ws_share_innovation", methods={"POST"})
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
     *      ),
     *      @OA\Parameter(
     *          name="innovation_d",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="people",
     *          in="query",
     *          type="integer",
     *          description="Target user id",
     *          default=false
     *      ),
     *      @OA\Parameter(
     *          name="message",
     *          in="query",
     *          type="string",
     *          description="Mail message",
     *          default=false
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function shareInnovationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $innovation_id = $request->request->get('innovation_id');
        $target_user_id = $request->request->get('people');
        $target_user = ($target_user_id) ? $em->getRepository('AppBundle:User')->find($target_user_id) : null;
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        $message = Settings::getXssCleanString($request->request->get('message', ''));

        if (!$target_user || !$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'User or Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$innovation->isEnabledOnExplore()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You can\'t share an innovation who isn\'t on Explore')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $activity = $em->getRepository('AppBundle:Activity')->createShareActivity($user, $innovation, $target_user, $message);

        $mailer = $this->container->get('app.mailer');
        $mailer->sendShareInnovationEmail($user, $target_user, $innovation, $message, $activity->getId());

        $response = new Response(json_encode(['status' => 'success']));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Get more promoted activities for an innovation
     *
     * @Route("/api/innovation/activity/promoted", name="ws_get_promote_infos_users", methods={"POST"})
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
     *                  property="count",
     *                  type="integer",
     *                  default=0
     *              ),
     *              @OA\Property(
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="data-type",
     *          in="query",
     *          type="string",
     *          description="Data type",
     *          default="html"
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *      ),
     *      @OA\Parameter(
     *          name="target",
     *          in="query",
     *          type="string",
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function getPromoteInfosUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $innovation_id = $request->request->get('id');
        $offset = $request->request->get('offset');
        $limit = $request->request->get('limit');
        $target = $request->request->get('target');
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        if (!$innovation) {
            $ret = array('status' => 'error');
        } elseif (!$user->hasAdminRights() && !$user->getUserInnovationRightForAnInnovation($innovation)) {
            $ret = array('status' => 'error', 'message' => 'no right');
        } else {
            $true_target = ($target == 'view') ? Activity::ACTION_PROMOTE_INNOVATION_VIEW : Activity::ACTION_PROMOTE_INNOVATION_EXPORT;
            if ($target == 'share') {
                $true_target = Activity::ACTION_INNOVATION_SHARE;
                $html = $em->getRepository('AppBundle:Activity')->getShareActivitiesHTMLForInnovation($innovation, $offset, $limit, 'big');
            } else {
                $html = $em->getRepository('AppBundle:Activity')->getPromoteActivitiesHTMLForInnovationByActionId($innovation, $true_target, $offset, $limit, 'big');
            }
            $ret = array(
                'status' => 'success',
                'count' => $em->getRepository('AppBundle:Activity')->getPromoteActivitiesHTMLForInnovationByActionId($innovation, $true_target, $offset, $limit, '', true),
                'html' => $html
            );
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Update innovation tags
     *
     * @Route("/api/innovation/tags/update", name="ws_innovation_tags_update", methods={"POST"})
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
     *                  property="full_data",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="tag_titles",
     *          in="query",
     *          type="array",
     *          required=true,
     *          description="Array of tags ids or strings"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateTagsToInnovationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $tag_titles = $request->request->get('tag_titles', array());
        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation or Tag not found [Code : PR-0ITA1]')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $innovation->removeAllTags();
        foreach ($tag_titles as $tag_title) {
            $tag_title = Settings::getXssCleanString($tag_title);
            /* @var Tag $tag */
            $tag = ($tag_title) ? $em->getRepository('AppBundle:Tag')->getOrCreateTag($tag_title) : null;
            $innovation->addTag($tag);
        }
        $em->flush();

        $ret = array(
            'tag_titles' => $tag_titles,
            'status' => 'success',
            'id' => $innovation->getId()
        );

        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id'));
        // we don't pass liip because we don't need to resize images
        $ret['full_data'] = $innovation->toArray($settings);

        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Create simple search history
     *
     * @Route("/api/search-history/create", name="ws_search_history_create", methods={"POST"})
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
     *                  property="search_history",
     *                  type="array",
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="url",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="css_class",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function createSearchHistoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $title = Settings::getXssCleanString($request->request->get('title'));
        $url = $request->request->get('url');
        $css_class = $request->request->get('css_class');

        if (!$title || !$url || !$css_class || !$user) {
            $ret = array('status' => 'error', 'search_history' => []);
        } else {
            $search_history = new SearchHistory();
            $search_history->setTitle($title);
            $search_history->setUrl($url);
            $search_history->setCssClass($css_class);
            $search_history->setUser($user);
            $em->persist($search_history);
            $user->addSearchHistory($search_history);
            $em->flush();
            $ret = array(
                'status' => 'success',
                'search_history' => $user->getLastSearchHistories()
            );
        }

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Update user walkthrough enability
     *
     * @Route("/api/user/update/walkthrough", name="ws_user_update_walkthrough", methods={"POST"})
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
     *      ),
     *      @OA\Parameter(
     *          name="disabled",
     *          in="query",
     *          type="integer",
     *          required=true
     *      ),
     * )
     * @OA\Tag(name="Global Api")
     */
    public function updateUserWalkthroughAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $the_user = $em->getRepository('AppBundle:User')->find($user->getId());
        if ($the_user) {
            $disabled = ($request->request->get('disabled') == 1);
            $the_user->setHasSeenWalkthrough($disabled);
            $em->persist($the_user);
            $em->flush();
        }
        $response = new Response(json_encode(array('status' => 'success', 'data' => $disabled, 'disabled' => $the_user->getHasSeenWalkthrough())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Innovation Api - Update key city
     *
     * @Route("/api/innovation/key-city/update", name="ws_update_key_city", methods={"POST"})
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
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="kcid",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Key city id"
     *      ),
     *      @OA\Parameter(
     *          name="action",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateKeyCityAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $kcid = $request->request->get('kcid');
        $target_city = ($kcid) ? $em->getRepository('AppBundle:City')->find($kcid) : null;
        $action = $request->request->get('action');
        $user = $this->getUser();
        $ret = array(
            'status' => 'error',
            'message' => 'Very strange error [Code : PR-0ZUIR01]',
        );

        if (!$innovation || !$action) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$user->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$innovation->isANewBusinessAcceleration()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'This innovation is not a New Business Acceleration')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$kcid || !$target_city) {
                $ret = array('status' => 'error', 'message' => 'City not found');
                $response = new Response(json_encode($ret));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
        }
        if ($action == 'add') {
            if($innovation->hasKeyCity($target_city)){
                $ret = array('status' => 'error', 'message' => 'Your innovation already have this key city');
                $response = new Response(json_encode($ret));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $innovation->addKeyCity($target_city);
            $em->flush();
            $ret = array(
                'status' => 'success',
                'key_cities' => $innovation->getKeyCitiesArray()
            );
        } elseif ($action == 'delete') {
            if(!$innovation->hasKeyCity($target_city)){
                $ret = array('status' => 'error', 'message' => 'Your innovation doesn\'t have this key city');
                $response = new Response(json_encode($ret));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $innovation->removeKeyCity($target_city);
            $em->flush();
            $ret = array(
                'status' => 'success',
                'key_cities' => $innovation->getKeyCitiesArray()
            );
        }
        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id'));
        // we don't pass liip because we don't need to resize images
        $ret['full_data'] = $innovation->toArray($settings);

        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * City search.
     *
     * @Route("/city/search", name="city_search", methods={"POST"})
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
     *                  property="items",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Specified search word"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified limit",
     *          default=20
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function searchCityAction()
    {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $keyword = strtolower($request->request->get('search'));
        $cities = ($keyword) ? $em->getRepository('AppBundle:City')->searchCityByCityName($keyword, true, 0, 50) : [];
        $ret = array(
            'status' => 'success',
            'items' => $cities
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Innovation Api - Update open question
     *
     * @Route("/api/innovation/open-question/update", name="ws_update_open_question", methods={"POST"})
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
     *                  property="html",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="message",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="open question message"
     *      )
     * )
     * @OA\Tag(name="Innovation Api")
     */
    public function updateOpenQuestionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        /* @var Innovation $innovation */
        $innovation = ($request->request->get('id')) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id')) : null;
        $message = $request->request->get('message', '');
        $user = $this->getUser();
        $ret = array(
            'status' => 'error',
            'message' => 'Very strange error [Code : PR-0OQR01]',
        );

        if (!$innovation ) {
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$user->hasAdminRights()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$innovation->isANewBusinessAcceleration()) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'This innovation is not a New Business Acceleration')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $open_question = $innovation->getOpenQuestion();
        $new_open_question = (!$open_question);
        if(!$open_question){
            $open_question = new OpenQuestion();
        }
        $open_question->setContact($user);
        $open_question->setInnovation($innovation);
        $open_question->setMessage($message);
        $innovation->setOpenQuestion($open_question);
        if($new_open_question){
            $em->persist($open_question);
        }
        $em->flush();


        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('id'));
        // we don't pass liip because we don't need to resize images
        $ret['status'] = 'success';
        $ret['full_data'] = $innovation->toArray($settings);

        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
