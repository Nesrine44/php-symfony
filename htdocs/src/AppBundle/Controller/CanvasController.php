<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Canvas;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as OA;

class CanvasController extends Controller
{
    /**
     *  Get canvas innovation. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/innovation/canvas/get", name="explore_get_canvas", methods={"POST"})
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
     *                  property="canvas_array",
     *                  type="array",
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="canvas_id",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified canvas id"
     *      )
     * )
     * @OA\Tag(name="Canvas Api")
     */
    public function getInnovationCanvasAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $sender = $this->getUser();

        $innovation = $em->getRepository('AppBundle:Innovation')
            ->findActiveInnovation($request->request->get('innovation_id'));

        $canvas_id = $request->request->get('canvas_id');

        if (!$innovation) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $canvas = null;
        if($canvas_id){
            $canvas = $em->getRepository('AppBundle:Canvas')
                ->findOneBy(['id' => $canvas_id]);
            if (!$canvas || !$canvas->getInnovation() || ($canvas->getInnovation() && $canvas->getInnovation()->getId() != $innovation->getId())) {
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'Canvas or Innovation not found [2]')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }

        if (!$canvas && !$sender->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if(!$canvas) {
            $canvas_array = $innovation->getCanvasCollectionArray();
            $response = new Response(json_encode(array('status' => 'success', 'canvas_array' => $canvas_array)));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $canvas_array = $canvas->toArray();
            $response = new Response(json_encode(array('status' => 'success', 'canvas' => $canvas_array)));
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }


    /**
     *  Update canvas innovation. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/innovation/canvas/update", name="explore_update_canvas", methods={"POST"})
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
     *                  property="canvas",
     *                  type="array",
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="canvas_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified canvas id"
     *      ),
     *      @OA\Parameter(
     *          name="update_datas",
     *          in="query",
     *          type="array",
     *          required=true,
     *          description="Data to update"
     *      )
     * )
     * @OA\Tag(name="Canvas Api")
     */
    public function updateInnovationCanvasAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $sender = $this->getUser();

        $innovation = $em->getRepository('AppBundle:Innovation')
            ->findActiveInnovation($request->request->get('innovation_id'));

        $canvas_id = $request->request->get('canvas_id');
        $update_datas = $request->request->get('update_datas', array());

        if ($canvas_id == 'new') {
            $canvas = new Canvas();
            if ($innovation) {
                $canvas->setInnovation($innovation);
            }
        } else {
            $canvas = $em->getRepository('AppBundle:Canvas')
                ->findOneBy(['id' => $request->request->get('canvas_id')]);
        }

        if (!$innovation || !$canvas) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Canvas or Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$canvas->getInnovation() || ($canvas->getInnovation() && $canvas->getInnovation()->getId() != $innovation->getId())) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Canvas or Innovation not found [2]')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (!$sender->canEditThisInnovation($innovation)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (count($update_datas) == 0) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Nothing to update')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $old_values_canvas = $canvas->toArray();
        $canvas->updateWithDatas($update_datas);
        if ($canvas_id == 'new') {
            $em->persist($canvas);
        }
        $em->flush();
        if ($canvas_id == 'new') {
            $em->getRepository('AppBundle:Activity')->createCanvasActivity($sender, $innovation, Activity::ACTION_CANVAS_CREATED, $canvas);
        }else {
            foreach ($update_datas as $update_data) {
                if (!array_key_exists('name', $update_data) || !array_key_exists('value', $update_data)) {
                    continue;
                }
                $activity_data = array(
                    'field_name' => $update_data['name'],
                    'old_value' => ((array_key_exists($update_data['name'], $old_values_canvas)) ? $old_values_canvas[$update_data['name']] : null),
                    'new_value' => $update_data['value'],
                );
                $em->getRepository('AppBundle:Activity')->createCanvasActivity($sender, $innovation, Activity::ACTION_CANVAS_UPDATED, $canvas, $activity_data);
            }
        }
        $canvas_array = $canvas->toArray();
        $em->clear();
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $innovation = $em->getRepository('AppBundle:Innovation')->findActiveInnovation($request->request->get('innovation_id'));
        // we don't pass liip because we don't need to resize images
        $ret = array();
        $ret['status'] = 'success';
        $ret['full_data'] = $innovation->toArray($settings);
        $ret['canvas'] = $canvas_array;
        $ret['update_datas'] = $update_datas;

        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->updateAllInnovationsAndConsolidationByInnovation($ret['full_data']);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
