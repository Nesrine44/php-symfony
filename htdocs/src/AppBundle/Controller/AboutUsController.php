<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AboutUsController extends Controller
{
    /**
     * Learn articles URL.
     *
     * @Route("/about-us", name="about_us", methods={"GET"})
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction() {
        return $this->render('@App/about-us.html.twig');
    }
}
