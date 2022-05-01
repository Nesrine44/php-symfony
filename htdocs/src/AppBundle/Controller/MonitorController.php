<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as OA;

class MonitorController extends Controller
{
    /**
     * Monitor list URL. (only available for user who manage projects)
     *
     * @Route("/content/monitor", name="monitor_list", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to monitor list."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function indexAction()
    {
        if(!$this->getUser()->hasManageAccess() ){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/monitor/list.html.twig', []);
    }
}
