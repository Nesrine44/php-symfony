<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class SearchController extends Controller
{
    /**
     * Search results URL.
     *
     * @Route("/content/search", name="search_results_url", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to search results page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function indexAction()
    {
        $request = Request::createFromGlobals();
        $search = $request->query->get('search', '');
        return $this->render('@App/search/results.html.twig', ['search_word' => $search]);
    }


    /**
     * Global search in website.
     *
     * @Route("/api/search", name="api_search", methods={"POST"})
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
     *                  property="innovations",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="users",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="trending_tags",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="brands",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="no_result",
     *                  type="boolean"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Specified search word"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified limit",
     *          default=20
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $ret = array(
            'status' => 'success',
            'innovations' => [],
            'users' => [],
            'trending_tags' => [],
            'brands' => [],
            'no_result' => true,
            'nb_results' => 0
        );
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $search = $request->request->get('search');
        $offset = 0;
        $limit = $request->request->get('limit', 20);
        $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUser($user, $search, $offset, $limit);
        $innovations_ids = $em->getRepository('AppBundle:Innovation')->searchIdsForUser($user, $search);
        $ret['users'] = $em->getRepository('AppBundle:User')->search($search, $offset, $limit, $innovations_ids);
        $users_ids = $em->getRepository('AppBundle:User')->searchIds($search, $innovations_ids);
        $ret['brands'] = $em->getRepository('AppBundle:Brand')->search($search);
        $ret['trending_tags'] = $em->getRepository('AppBundle:Tag')->searchTagByTitle($search, true, $offset, 5);
        $websiteGlobalDataService = $this->container->get('app.website_global_datas');
        $pictures = $em->getRepository('AppBundle:Innovation')->searchAllPicturesByInnovationIds($websiteGlobalDataService, $innovations_ids);
        $ret['nb_results'] = count($innovations_ids) + count($users_ids) + count($pictures);
        $ret['no_result'] = ((count($ret['innovations']) + count($ret['users']) + count($ret['brands']) + count($ret['trending_tags'])) == 0);

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Global search in website.
     *
     * @Route("/api/search/results", name="api_search_results", methods={"POST"})
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
     *                  property="innovations",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="users",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="pictures",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Specified search word"
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified offset",
     *          default=0
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified limit",
     *          default=20
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Content target type (innovations, pictures or users)"
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function searchResultsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $ret = array(
            'status' => 'success',
            'innovations' => [],
            'users' => [],
            'pictures' => [],
        );
        $search = $request->request->get('search');
        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);
        $type = $request->request->get('type');
        if($type){
            switch ($type){
                case 'innovations':
                    $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUser($user, $search, $offset, $limit);
                    break;
                case 'users':
                    $innovations_ids = $em->getRepository('AppBundle:Innovation')->searchIdsForUser($user, $search);
                    $ret['users'] = $em->getRepository('AppBundle:User')->search($search, $offset, $limit, $innovations_ids);
                    break;
                case 'pictures':
                    $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUser($user, $search, $offset, $limit);
                    $ret['pictures'] = $em->getRepository('AppBundle:Innovation')->searchPicturesForUser($user, $search, $offset, $limit);
                    break;
                default:
                    $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Unknown search type')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
            }
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUser($user, $search, $offset, $limit);
        $innovations_ids = $em->getRepository('AppBundle:Innovation')->searchIdsForUser($user, $search);
        $ret['users'] = $em->getRepository('AppBundle:User')->search($search, $offset, $limit, $innovations_ids);
        $ret['pictures'] = $em->getRepository('AppBundle:Innovation')->searchPicturesForUser($user, $search, $offset, $limit);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Search results tags URL.
     *
     * @Route("/content/search/tags", name="search_results_tags_url", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to search tags results page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function tagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $search = $request->query->get('search', null);
        $tag = ($search) ? $em->getRepository('AppBundle:Tag')->findOneBy(['title' => $search]) : null;
        if(!$tag){
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/search/results-tags.html.twig', ['tag_array' => $tag->toArray()]);
    }


    /**
     * Global tag search in website.
     *
     * @Route("/api/search/tag/results", name="api_search_tag_results", methods={"POST"})
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
     *                  property="innovations",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="pictures",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Specified tag title"
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified offset",
     *          default=0
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified limit",
     *          default=20
     *      ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Content target type (innovations, pictures or users)"
     *      )
     * )
     * @OA\Tag(name="Global Api")
     */
    public function searchTagsResultsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $ret = array(
            'status' => 'success',
            'innovations' => [],
            'pictures' => [],
        );
        $title = $request->request->get('title');
        $tag = ($title) ? $em->getRepository('AppBundle:Tag')->findOneBy(['title' => $title]) : null;
        if(!$tag){
            $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Tag not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);
        $type = $request->request->get('type');
        if($type){
            switch ($type){
                case 'innovations':
                    $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUserByTag($user, $tag, $offset, $limit);
                    break;
                case 'pictures':
                    $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUserByTag($user, $tag, $offset, $limit);
                    $ret['pictures'] = $em->getRepository('AppBundle:Innovation')->searchPicturesForUserByTag($user, $tag, $offset, $limit);
                    break;
                default:
                    $response = new Response(json_encode(array('status' => 'error', 'data' => array(), 'message' => 'Unknown search type')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
            }
            $response = new Response(json_encode($ret));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $ret['innovations'] = $em->getRepository('AppBundle:Innovation')->searchForUserByTag($user, $tag, $offset, $limit);
        $ret['pictures'] = $em->getRepository('AppBundle:Innovation')->searchPicturesForUserByTag($user, $tag, $offset, $limit);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
