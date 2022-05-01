<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as OA;

class HomepageController extends Controller
{
    /**
     * Homepage URL.
     *
     * @Route("/", name="homepage", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to homepage."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function indexAction()
    {
        return $this->render('@App/homepage.html.twig', []);
    }
}
