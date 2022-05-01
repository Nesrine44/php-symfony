<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\FeedbackInvitation;
use AppBundle\Entity\Feedback;
use Swagger\Annotations as OA;

class ExploreController extends Controller
{
    /**
     * Explore list page.
     *
     * @Route("/content/explore", name="explore_list", methods={"GET"})
     * @Route("/content/explore/tab/{tab}", name="explore_list_tab", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to explore list page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param string $tab
     * @return Response
     */
    public function indexAction($tab = 'products')
    {
        $user = $this->getUser();
        if(!$user->hasNewBusinessModelAccess()){
            $tab = 'products';
        }
        return $this->render('@App/explore/list.html.twig', ['tab' => $tab]);
    }

    /**
     * Compare list page.
     *
     * @Route("/content/compare", name="explore_compare", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to compare list page or compare detail page depending on the case."
     *      ),
     *      @OA\Parameter(
     *          name="inno",
     *          in="path",
     *          type="string",
     *          required=false,
     *          description="2 innovation ids separated by a comma"
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function compareAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $inno = $request->query->get('inno');
        $innovation_ids = explode(',', $inno);
        $user = $this->getUser();

        if($innovation_ids && count($innovation_ids) == 2){
            $is_valid = true;
            $ids = array();
            $globalService = $this->get('app.website_global_datas');
            foreach($innovation_ids as $innovation_id){
                $ids[] = intval($innovation_id);
                $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
                if(!$innovation){
                    $is_valid = false;
                    break;
                }
                if(!$innovation->isEnabledOnExplore() &&
                    !$user->canEditThisInnovation($innovation) &&
                    !$user->hasAdminRights() &&
                    !$user->hasManagementRights()
                ){
                    $is_valid = false;
                    break;
                }
                if(!$globalService->getInnovationArrayById(intval($innovation_id))){
                    $is_valid = false;
                    break;
                }
            }
            if($is_valid){ // COMPARE DETAIL
                return $this->render('@App/explore/compare-detail.html.twig', [
                    'innnovations_ids' => $ids
                ]);
            }
        }
        return $this->render('@App/explore/compare.html.twig', []);
    }

    /**
     * Explore detail innovation page
     *
     * @Route("/explore/{id}", name="explore_detail", requirements={"id"="\d+"}, methods={"GET"})
     * @Route("/explore/{id}/tab/{tab}", name="explore_detail_tab", requirements={"id"="\d+"}, methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to explore detail innovation page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param int $id
     * @param string $tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id, $tab = null)
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
        if(!$innovation){
            $this->addFlash(
                'error',
                '[404] - Innovation does not exist.'
            );
            return $this->redirectToRoute('homepage');
        }
        
        if(!$innovation->isEnabledOnExplore() &&
            !$user->canEditThisInnovation($innovation) &&
            !$user->hasAdminRights() &&
            !$user->hasManagementRights()
        ){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }

        $key = $request->query->get('key');
        if($key){
            $key_exploder = explode('-', $key);
            if(count($key_exploder) > 1) {
                $activity_id = $key_exploder[1];
                if ($activity_id) {
                    $activity = $em->getRepository('AppBundle:Activity')->find($activity_id);
                    if ($activity && $activity->getActionId() == Activity::ACTION_INNOVATION_SHARE) {
                        $data = $activity->getDataArray();
                        $data['clicked'] = true;
                        $activity->setDataArray($data);
                        $em->flush();
                    }
                }
            }
        }

        $metrics_key = $request->query->get('m-key');
        if($metrics_key){
            MetricsController::generateMetricsByKey($em, $metrics_key);
        }

        if($user->hasDeveloperRights()){
            $wsGlobalDatas = $this->container->get('app.website_global_datas');
            $request = Request::createFromGlobals();
            if($request->query->get('debug')  == 'full'){
                $wsGlobalDatas = $this->container->get('app.website_global_datas');
                $innovation_array = $wsGlobalDatas->getInnovationArrayById($id);
                $response = new Response(json_encode($innovation_array));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }elseif($request->query->get('debug')  == 'excel'){
                $wsGlobalDatas = $this->container->get('app.website_global_datas');
                $innovation_array = $wsGlobalDatas->getInnovationsExcelByArrayIds([$id]);
                $response = new Response(json_encode($innovation_array));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }elseif($request->query->get('debug')  == 'generate' && $wsGlobalDatas->checkRedisDatas()){
                $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
                $liip = $this->container->get('liip_imagine.service.filter');
                $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
                $innovation_array = $innovation->toArray($settings, $liip);
                $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($innovation_array);
                $response = new Response(json_encode($innovation_array));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }elseif($request->query->get('debug')  == 'nb_fields'){
                $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
                $ret = array(
                    'project_id' => $innovation->getNumberOfEmptyProjectIDFields(),
                    'elevator_pitch' => $innovation->getNumberOfEmptyElevatorPitchFields(),
                    'assets' => $innovation->getNumberOfEmptyAssetsFields(),
                    'financial' => $innovation->getNumberOfEmptyFinancialFields($settings),
                );
                $response = new Response(json_encode($ret));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }

        return $this->render('@App/explore/detail.html.twig', ['id' => $id, 'tab' => $tab, 'innovation_title' => $innovation->getTitle()]);
    }
    
    /**
     * Explore detail innovation page with feedback invitation
     *
     * @Route("/explore/{id}/feedback-invitation/{token}", name="explore_detail_from_feedback_inv", requirements={"id"="\d+"}, methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to explore detail innovation page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param int $id
     * @param int $token Token from feedback invitation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function feedbackInvitationLinkAction($id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
        $feedbackInv = $em->getRepository('AppBundle:FeedbackInvitation')->findOneBy([
            'token' => $token,
        ]);

        // Innovation not found if frozen or just not found
        if (!$innovation) {
            $this->addFlash(
                'error',
                '[404] - Innovation does not exist.'
            );
            return $this->redirectToRoute('homepage');
        }

        if (!$feedbackInv || ($feedbackInv && $feedbackInv->getInnovation()->getId() != $innovation->getId())) {
            return $this->redirectToRoute('explore_detail', ['id' => $id]);
        }
        $params = ['id' => $id, 'innovation_title' => $innovation->getTitle()];
        switch($feedbackInv->getStatus()) {
            case 'PENDING':
                $params['isFeedbackInv'] = true;
                $params['feedbackInvitation'] = $feedbackInv;
                $params['additional_read_ids'] = [$innovation->getId()];
                break;
            case 'REMOVED':
                if(!$innovation->isEnabledOnExplore()) {
                    $this->addFlash(
                        'notice',
                        'Your early access to this innovation has been revoked. Thanks for your help! 
                        Feedback is priceless when it comes to making great innovations!'
                    );
                    return $this->redirectToRoute('explore_list');
                }
                break;
            case 'ANSWERED':
                break;
            default:
        }
        $metrics_key = $request->query->get('m-key');
        if($metrics_key){
            MetricsController::generateMetricsByKey($em, $metrics_key);
        }

        return $this->render('@App/explore/detail.html.twig', $params);
    }
}
