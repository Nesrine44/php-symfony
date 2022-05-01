<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as OA;

class GlobalController extends Controller
{
    /**
     * Custom page not found.
     * Used to override Twig 404 exception to get user in 404 not found page.
     */
    public function pageNotFoundAction()
    {
        throw new NotFoundHttpException();
    }

    /**
     * Display AWS file.
     * (currently not used but usefull in case of private files)
     *
     * @Route("/aws-file/{filename}", name="aws_images_action", requirements={"filename"=".+"})
     * @param string $filename
     *
     *
     * @return Response
     */
    public function displayAwsFileAction($filename)
    {
        $awsS3Uploader  = $this->get('app.s3_uploader');
        $result = $awsS3Uploader->getFile($filename);
        if ($result) {
            // Display the object in the browser
            header("Content-Type: {$result['ContentType']}");
            #header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60))); // 1 hour
            echo $result['Body'];

            return new Response();
        }
        return new Response('', 404);
    }


    /**
     * Check full data progress loading state.
     *
     * @Route("/api/global/check-all-data", name="check_all_data", methods={"POST"})
     * @OA\Post(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="launched"
     *              ),
     *              @OA\Property(
     *                  property="progress",
     *                  type="integer",
     *                  default=0
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function checkAllDataAction()
    {
        $websiteGlobalDataService = $this->container->get('app.website_global_datas');
        $ret = array('status' => 'launched');
        if($websiteGlobalDataService->checkRedisDatas()){
            $ret = array('status' => 'success');
        }else{
            $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
            $redis_cache_progress = $pernodWorker->getRedisCacheProgress();
            if(!$redis_cache_progress) {
                $redis_cache_progress = $pernodWorker->initRedisCacheProgress();
                $pernodWorker->later()->generateAllInnovationsAndConsolidation(true);
            }
            $ret['progress'] = $redis_cache_progress;
        }
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}
