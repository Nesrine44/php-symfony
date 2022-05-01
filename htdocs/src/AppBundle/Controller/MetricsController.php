<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Metrics;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Swagger\Annotations as OA;

class MetricsController extends Controller
{
    /**
     * Metrics API - Return a pixel image to generate metrics (mail opened).
     *
     * @Route("/pm/{global_key}.png", name="pixel_mail_opened_metrics_action", methods={"GET"})
     * @OA\Get(
     *      produces={"image/png"},
     *      @OA\Response(
     *          response=200,
     *          description="Metrics pixel image."
     *      )
     * )
     * @OA\Tag(name="Metrics Api")
     *
     * @param string $global_key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pixelMailOpenedMetricsAction($global_key)
    {
        if($global_key !== 'pixel') {
            $global_key_exploder = explode('-', $global_key);
            $key = $global_key_exploder[0];
            $user_id = intval($global_key_exploder[1]);
            $innovation_id = intval($global_key_exploder[2]);
            $action = Metrics::getActionByActiondId(intval($global_key_exploder[3]));

            $em = $this->getDoctrine()->getManager();
            $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
            $user = $em->getRepository('AppBundle:User')->find($user_id);
            if ($innovation && $user) {
                $em->getRepository('AppBundle:Metrics')->createMetrics($user, $innovation, $action, $key);
            }
        }

        $root_dir =  $this->get('kernel')->getRootDir();
        $file_path = $root_dir.'/../web/images/masks/pixel.png';
        if(file_exists($file_path)){
            $response = new Response();
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, 'pixel.png');
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', 'image/png');
            $response->setContent(file_get_contents($file_path));
            return $response;
        }
        return new Response('', 404);
    }

    /**
     * Metrics API - Generate a mail clicked metrics.
     *
     * @Route("/im/{global_key}", name="mail_clicked_metrics_action", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=302,
     *          description="Redirect to good page."
     *      )
     * )
     * @OA\Tag(name="Metrics Api")
     *
     * @param string $global_key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mailClickedMetricsAction($global_key)
    {
        $global_key_exploder = explode('-', $global_key);
        $key = $global_key_exploder[0];
        $user_id = intval($global_key_exploder[1]);
        $innovation_id = intval($global_key_exploder[2]);
        $action = Metrics::getActionByActiondId(intval($global_key_exploder[3]));

        $em = $this->getDoctrine()->getManager();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        $user = $em->getRepository('AppBundle:User')->find($user_id);
        if($innovation && $user) {
            $em->getRepository('AppBundle:Metrics')->createMetrics($user, $innovation, $action, $key);
            if($action == Metrics::ACTION_MAIL_PROMOTE_CLICKED) {
                return $this->redirect($this->generateUrl('explore_detail_tab', array('id' => $innovation_id, 'tab' => 'activities',  'key' => $key)));
            }else if($action == Metrics::ACTION_MAIL_CHANGE_STAGE_EXPERIMENT_CLICKED){
                return $this->redirect($this->generateUrl('learn_article', array('slug' => 'stage-experimentation')));
            }else if($action == Metrics::ACTION_MAIL_CHANGE_STAGE_INCUBATE_CLICKED){
                return $this->redirect($this->generateUrl('learn_article', array('slug' => 'stage-incubation')));
            }
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * Generate metrics by key.
     *
     * @param $em
     * @param $global_key
     */
    public static function generateMetricsByKey($em, $global_key){
        $global_key_exploder = explode('-', $global_key);
        $key = $global_key_exploder[0];
        $user_id = intval($global_key_exploder[1]);
        $innovation_id = intval($global_key_exploder[2]);
        $action = Metrics::getActionByActiondId(intval($global_key_exploder[3]));
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id);
        $user = $em->getRepository('AppBundle:User')->find($user_id);
        if($innovation && $user) {
            $em->getRepository('AppBundle:Metrics')->createMetrics($user, $innovation, $action, $key);
        }
    }

    /**
     * Metrics API - Generate a metrics for an innovation and an action.
     *
     * @Route("/api/metrics/generate", name="generate_metrics_action", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="unchanged"
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
     *          name="key",
     *          in="query",
     *          type="string",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="action_id",
     *          in="query",
     *          type="string",
     *          required=true
     *      )
     * )
     * @OA\Tag(name="Metrics Api")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateMetricsAction()
    {
        $request = Request::createFromGlobals();
        $innovation_id = $request->request->get('innovation_id', null);
        $key = $request->request->get('key', null);
        $action_id = $request->request->get('action_id', null);
        $action = Metrics::getActionByActiondId($action_id);

        $em = $this->getDoctrine()->getManager();
        $innovation = ($innovation_id) ? $em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
        $user = $this->getUser();
        $ret = array('status' => 'unchanged');
        if($user) {
            $em->getRepository('AppBundle:Metrics')->createMetrics($user, $innovation, $action, $key);
            $ret = array('status' => 'success');
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
