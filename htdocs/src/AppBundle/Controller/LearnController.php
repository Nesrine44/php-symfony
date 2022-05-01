<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LearnController extends Controller
{
    /**
     * Learn articles URL.
     *
     * @Route("/learn", name="learn_list", methods={"GET"})
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction() {
        return $this->render('@App/learn/list.html.twig');
    }

    /**
     * Detail article URL.
     *
     * @Route("/learn/article/{slug}", name="learn_article", methods={"GET"})
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function detailAction(EngineInterface $templeEngine, $slug) {
        if ($templeEngine->exists('@App/learn/article/'.$slug.'.html.twig')) {
            return $this->render('@App/learn/article/'.$slug.'.html.twig');
        }
        throw new NotFoundHttpException;
    }
}
