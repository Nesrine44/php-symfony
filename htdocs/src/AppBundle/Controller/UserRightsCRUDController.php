<?php

namespace AppBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class UserRightsCRUDController extends CRUDController
{
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("AppBundle:User")->getAllForJson();
        $entities = $em->getRepository("AppBundle:Entity")->getAllForJson();
        return $this->render('@App/admin/user_rights.html.twig', [
            'users' => $users,
            'entities' => $entities
        ]);
    }
}