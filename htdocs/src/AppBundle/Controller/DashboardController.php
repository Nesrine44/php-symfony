<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as OA;


class DashboardController extends Controller
{
    const DASHBOARD_TYPE_NEW_BACK = 'new_back';
    const DASHBOARD_TYPE_NEW = 'new';
    const DASHBOARD_TYPE_OUT = 'out';
    const DASHBOARD_TYPE_IN_MARKET_SOON = 'in_market_soon';
    const DASHBOARD_TYPE_IN_MARKET_UPDATE = 'in_market_update';
    const DASHBOARD_TYPE_INCOMPLETE_FINANCIAL_DATA = 'incomplete_financial_data';

    /**
     * Dashboard URL (only available for Management or HQ).
     *
     * @Route("/content/dashboard", name="dashboard", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to dashboard."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function indexAction()
    {
        /* @var User $user */
        $user = $this->getUser();
        if(!$user->hasAdminRights() && !$user->hasManagementRights()){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/dashboard/index.html.twig',[]);
    }

    
    /**
     * Dashboard API - Load dashboard page datas (only available for Management or HQ).
     *
     * @Route("/api/dashboard/data", name="dashboard_data", methods={"POST"})
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
     *                  property="innovations_new",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_new_back",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_out",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_in_market_date_modifications",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="innovations_in_market_soon",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Dashboard Api")
     */
    public function dashboardDataAction()
    {
        /* @var User $user */
        $user = $this->getUser();
        if(!$user->hasAdminRights() && !$user->hasManagementRights()){
            $response = new Response(json_encode(array('status'=>'error', 'message' => 'Wrong user rights')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $em = $this->getDoctrine()->getManager();
        $globalService = $this->get('app.website_global_datas');
        if($user->hasManagementRights()){
            $innovations_new_back = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsNewBackInPipeline(0, 10);
            $innovations_in_market_soon = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsInMarketSoon(0, 10);
            $ret = array(
                'status' => 'success',
                'innovations_new' => self::dashboardArrayToHtml($globalService, $innovations_new_back),
                'innovations_in_market_soon' => self::dashboardArrayToHtml($globalService, $innovations_in_market_soon),
            );
        }else {
            $innovations_new_back = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsNewBackInPipeline();
            $innovations_out = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsOutOfPipeline();
            $innovations_in_market_date_modifications = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsInMarketDateModification();
            $innovations_in_market_soon = $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsInMarketSoon();
            $innovations_incomplete_financial_data = $globalService->dashboardGetInnovationsIncompleteFinancialData();
            $ret = array(
                'status' => 'success',
                'innovations_new_back' => self::dashboardArrayToHtml($globalService, $innovations_new_back),
                'innovations_out' => self::dashboardArrayToHtml($globalService, $innovations_out),
                'innovations_in_market_date_modifications' => self::dashboardArrayToHtml($globalService, $innovations_in_market_date_modifications),
                'innovations_in_market_soon' => self::dashboardArrayToHtml($globalService, $innovations_in_market_soon),
                'innovations_incomplete_financial_data' => self::dashboardArrayToHtml($globalService, $innovations_incomplete_financial_data, 'incomplete_financial_data'),
            );
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Dashboard array to html.
     *
     * @param $globalService
     * @param $innovations
     * @param string $case
     * @return string
     */
    public static function dashboardArrayToHtml($globalService, $innovations, $case = ''){
        $html_data = "";
        foreach ($innovations as $innovation) {
            if ($case === 'incomplete_financial_data'){
                $html_data .= $globalService->get_dashboard_html_table_tr_incomplete_financial_data($innovation);
            }else{
                $html_data .= $globalService->get_dashboard_html_table_tr($innovation);
            }

        }
        return $html_data;
    }

    /**
     * Dashboard Detail URL (only available for Management or HQ).
     *
     * @Route("/content/dashboard/{type}", name="dashboard_detail", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to dashboard detail."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($type)
    {
        /* @var User $user */
        $user = $this->getUser();
        if(!$user->hasAdminRights() && !$user->hasManagementRights()){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }
        if(!in_array($type, self::getDashboardTypes())){
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('@App/dashboard/detail.html.twig', ['type' => $type]);
    }

    /**
     * Dashboard API - Load dashboard detail page datas (only available for Management or HQ).
     *
     * @Route("/api/dashboard/detail/data", name="dashboard_detail_ajax", methods={"POST"})
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
     *                  property="type",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="offset",
     *                  type="integer",
     *                  default=0
     *              ),
     *              @OA\Property(
     *                  property="limit",
     *                  type="integer",
     *                  default=50
     *              ),
     *              @OA\Property(
     *                  property="count",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Innovation type"
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
     * )
     * @OA\Tag(name="Dashboard Api")
     */
    public function detailAjaxAction()
    {
        /* @var User $user */
        $user = $this->getUser();
        if(!$user->hasAdminRights() && !$user->hasManagementRights()){
            $response = new Response(json_encode(array('status'=>'error', 'message' => 'wrong user rights')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $em = $this->getDoctrine()->getManager();
        $globalService = $this->get('app.website_global_datas');
        $request = Request::createFromGlobals();
        $type = $request->request->get('type', 'none');
        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 50);
        $ret = array(
            'status' => 'success',
            'type' => $type,
            'offset' => $offset,
            'limit' => $limit,
            'count' => 0
        );
        $innovations = self::getDashboardInnovationsForType($em, $globalService, $type, $offset, $limit);
        $ret['data'] = $innovations;
        $ret['count'] = count($innovations);
        $html_data = "";
        foreach ($innovations as $innovation) {
            if ($type === 'incomplete_financial_data'){
                $html_data .= $globalService->get_dashboard_html_table_tr_incomplete_financial_data($innovation, true);
            }else{
                $html_data .= $globalService->get_dashboard_html_table_tr($innovation, true);
            }
        }
        $ret['data'] = $html_data;
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * All activities URL (only available for HQ).
     *
     * @Route("/content/activities", name="activities", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to All activities page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function activitiesAction()
    {
        /* @var User $user */
        $user = $this->getUser();
        if(!$user->hasAdminRights() && !$user->hasManagementRights()){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/dashboard/activities.html.twig', []);
    }

    /**
     * Get dashboard types.
     *
     * @return array
     */
    public static function getDashboardTypes()
    {
        return array(
            self::DASHBOARD_TYPE_NEW_BACK,
            self::DASHBOARD_TYPE_NEW,
            self::DASHBOARD_TYPE_OUT,
            self::DASHBOARD_TYPE_IN_MARKET_SOON,
            self::DASHBOARD_TYPE_IN_MARKET_UPDATE,
            self::DASHBOARD_TYPE_INCOMPLETE_FINANCIAL_DATA
        );
    }

    /**
     * Get dashboard title for type.
     *
     * @param string $type
     * @return string
     */
    public static function getDashboardTitleForType($type)
    {
        switch ($type) {
            case self::DASHBOARD_TYPE_NEW_BACK:
                return 'New/back in the pipeline';
            case self::DASHBOARD_TYPE_NEW:
                return 'New in the pipeline';
            case self::DASHBOARD_TYPE_OUT:
                return 'Out of pipeline';
            case self::DASHBOARD_TYPE_IN_MARKET_SOON:
                return 'In market soon';
            case self::DASHBOARD_TYPE_IN_MARKET_UPDATE:
                return 'In market date modification';
            case self::DASHBOARD_TYPE_INCOMPLETE_FINANCIAL_DATA:
                return 'Incomplete financial data capture';
            default:
                return 'Innovations in the pipeline';
        }
    }

    /**
     * Get dashboard innovations for type.
     *
     * @param EntityManager $em
     * @param $globalService
     * @param string $type
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public static function getDashboardInnovationsForType($em, $globalService,  $type, $offset = 0, $limit = 6)
    {
        switch ($type) {
            case self::DASHBOARD_TYPE_NEW_BACK:
                return $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsNewBackInPipeline($offset, $limit);
            case self::DASHBOARD_TYPE_NEW:
                return $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsNewBackInPipeline($offset, $limit);
            case self::DASHBOARD_TYPE_OUT:
                return $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsOutOfPipeline($offset, $limit);
            case self::DASHBOARD_TYPE_IN_MARKET_SOON:
                return $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsInMarketSoon($offset, $limit);
            case self::DASHBOARD_TYPE_IN_MARKET_UPDATE:
                return $em->getRepository('AppBundle:Activity')->dashboardGetInnovationsInMarketDateModification($offset, $limit);
            case self::DASHBOARD_TYPE_INCOMPLETE_FINANCIAL_DATA:
                return $globalService->dashboardGetInnovationsIncompleteFinancialData($offset, $limit);
            default:
                return array();
        }
    }
}
