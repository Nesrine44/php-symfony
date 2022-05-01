<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class DefaultController extends Controller
{
    /**
     * Reset export progression.
     *
     * @Route("/api/export/reset-progress", name="default_reset_export_progress", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="progress",
     *                  type="integer",
     *                  default=0
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="export_id",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Export ID to reset"
     *      )
     * )
     * @OA\Tag(name="Export Api")
     */
    public function resetProgressAction()
    {
        $request = Request::createFromGlobals();
        $redis = $this->get("snc_redis.default");
        $export_id = $request->request->get('export_id');
        $ret = array(
            'progress' => Settings::EXPORT_PROGRESS_MIN
        );
        if($export_id){
            $redis->remove($export_id);
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
