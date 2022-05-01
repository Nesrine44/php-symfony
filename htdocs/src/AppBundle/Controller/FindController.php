<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class FindController extends Controller
{
    /**
     * Search tags by term
     *
     * @Route("/api/find/tags", name="default_get_tags", methods={"GET"})
     * @OA\Get(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="term",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="items",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="term",
     *          in="path",
     *          type="string",
     *          required=true,
     *          description="Searching term"
     *      )
     * )
     * @OA\Tag(name="Find Api")
     */
    public function findTagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $term = $request->query->get('term', '');
        $items = $em->getRepository('AppBundle:Tag')->searchTagByTitle($term, true);
        $ret = [
            'term' => $term,
            'items' => $items
        ];
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Search skills by term
     *
     * @Route("/api/find/skills", name="default_get_skills", methods={"GET"})
     * @OA\Get(
     *      produces={"application/json"},
     *      @OA\Response(
     *          response=200,
     *          @OA\Schema (
     *              @OA\Property(
     *                  property="items",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="term",
     *          in="path",
     *          type="string",
     *          required=true,
     *          description="Searching term"
     *      )
     * )
     * @OA\Tag(name="Find Api")
     */
    public function findSkillsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $term = $request->query->get('term', '');
        $items = $em->getRepository('AppBundle:Skill')->searchSkillByTitle($term, true);
        $ret = array(
            'results' => $items
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
