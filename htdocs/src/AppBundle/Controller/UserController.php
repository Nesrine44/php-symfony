<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

class UserController extends Controller
{
    /**
     * Current user profile page.
     *
     * @Route("/user", name="current_user_profile", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to current user profile page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function indexAction()
    {
        return $this->render('@App/user/detail.html.twig', ['the_user' => $this->getUser()->toArray()]);
    }

    /**
     * Current user settings page.
     *
     * @Route("/user/settings", name="current_user_settings", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to current user settings page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     */
    public function settingsAction()
    {
        return $this->render('@App/user/settings.html.twig', ['the_user' => $this->getUser()->toArray()]);
    }

    /**
     * User profile page.
     *
     * @Route("/user/{id}", name="user_profile", requirements={"id"="\d+"}, methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to specified user profile page."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $the_user = $em->getRepository('AppBundle:User')->find($id);
        if (!$the_user) {
            $message = '[404] - User does not exist.';
            $this->addFlash(
                'error',
                $message
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/user/detail.html.twig', ['the_user' => $the_user->toArray()]);
    }

    /**
     * Get user infos for user profile page.
     *
     * @Route("/api/user/infos", name="user_get_infos", methods={"POST"})
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
     *                  property="proper_role",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="last_connected",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="team_ids",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="skills",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="nb_feedbacks",
     *                  type="integer"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function getInfosUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user_id = $request->request->get('user_id');
        $the_user = $em->getRepository('AppBundle:User')->find($user_id);
        if (!$the_user) {
            $message = '[404] - User does not exist.';
            $this->addFlash(
                'error',
                $message
            );
            return $this->redirectToRoute('homepage');
        }
        $ret = array('status' => 'success');
        $ret['proper_role'] = $the_user->getProperUserRole();
        $ret['last_connected'] = $the_user->getRelativeLastLoginDate();
        $innovation_team_ids = $the_user->getTeamMemberInnovationsIds();
        $ret['team_ids'] = $innovation_team_ids;
        $ret['nb_feedbacks'] = $em->getRepository('AppBundle:User')->getUserFeedBackCount($the_user);
        $ret['skills'] = $the_user->getSkills();
        if ($this->getUser()->hasAdminRights() && $the_user->getIsPrEmploye()) {
            $secret_key = $this->container->getParameter('pr_auth')['password_key'];
            $ret['local_password'] = $the_user->getGeneratedLocalPassword($secret_key);
        }

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Connect page URL.
     *
     * @Route("/content/connect", name="user_connect", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to connect list."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectAction()
    {
        return $this->render('@App/user/connect.html.twig', []);
    }

    /**
     * Load more users for connect page.
     *
     * @Route("/api/user/connect/load-more", name="user_connect_get_infos", methods={"POST"})
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
     *                  property="users",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="total",
     *                  type="integer"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=true
     *      ),
     *      @OA\Parameter(
     *          name="filters",
     *          in="query",
     *          type="array",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function getInfosConnectAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);
        $filters = $request->request->get('filters', array());
        $user_innovations = $this->get('app.website_global_datas')->user_all_secured_innovations($user);
        $proper_filters = self::getProperFiltersForSearch($user_innovations, $filters);
        $total = $em->getRepository('AppBundle:User')->getConnectCount($user, $proper_filters);
        $users = $em->getRepository('AppBundle:User')->getConnectUsers($user, $offset, $limit, $proper_filters);
        $ret = array('status' => 'success', 'users' => $users, 'total' => $total, 'filters' => $proper_filters, 'offset' => $offset, 'limit' => $limit);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Get proper filters for search.
     *
     * @param $innovations
     * @param $filters
     * @return array
     */
    public static function getProperFiltersForSearch($innovations, $filters)
    {
        $proper_filters = array();
        foreach ($innovations as $innovation) {
            foreach ($filters as $filter) {
                $exploder = explode('-', $filter);
                $type = $exploder[0];
                $true_filter_id = intval($exploder[1]);
                switch ($type) {
                    case "e": // ENTITY
                        if ($innovation['entity'] && $innovation['entity']['id'] && $innovation['entity']['id'] == $true_filter_id) {
                            $id = $filter;
                            if (!array_key_exists($id, $proper_filters)) {
                                $proper_filters[$id] = array(
                                    'type' => 'entity',
                                    'innovations_ids' => array()
                                );
                            }
                            if (!in_array($innovation['id'], $proper_filters[$id]['innovations_ids'])) {
                                $proper_filters[$id]['innovations_ids'][] = $innovation['id'];
                            }
                        }
                        break;
                    case "b": // BRAND
                        if ($innovation['brand'] && $innovation['brand']['id'] && $innovation['brand']['id'] == $true_filter_id) {
                            $id = $filter;
                            if (!array_key_exists($id, $proper_filters)) {
                                $proper_filters[$id] = array(
                                    'type' => 'brand',
                                    'innovations_ids' => array()
                                );
                            }
                            if (!in_array($innovation['id'], $proper_filters[$id]['innovations_ids'])) {
                                $proper_filters[$id]['innovations_ids'][] = $innovation['id'];
                            }
                        }
                        break;
                    case "i": // INNOVATION
                        if ($innovation['id'] && $innovation['id'] == $true_filter_id) {
                            $id = $filter;
                            if (!array_key_exists($id, $proper_filters)) {
                                $proper_filters[$id] = array(
                                    'type' => 'innovation',
                                    'innovations_ids' => array()
                                );
                            }
                            if (!in_array($innovation['id'], $proper_filters[$id]['innovations_ids'])) {
                                $proper_filters[$id]['innovations_ids'][] = $innovation['id'];
                            }
                        }
                    case "s": // SKILL
                        $id = $filter;
                        if (!array_key_exists($id, $proper_filters)) {
                            $proper_filters[$id] = array(
                                'type' => 'skill',
                                'skill_id' => $true_filter_id
                            );
                        }
                        break;
                }
            }
        }
        return $proper_filters;
    }


    /**
     * Global search in website.
     *
     * @Route("/user/search", name="user_search", methods={"POST"})
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
     *                  property="items",
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
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Specified limit",
     *          default=20
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function searchAction()
    {
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $keyword = strtolower($request->request->get('search'));
        $filters = $request->request->get('filters', array());
        $user_innovations = $this->get('app.website_global_datas')->user_all_secured_innovations($user);
        $ret = array(
            'status' => 'success',
            'items' => self::getUsersSortsByKeyword($this, $user_innovations, $keyword, $filters)
        );
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Get Users Sorts by keyword.
     *
     * @param Controller $controller
     * @param $innovations
     * @param $keyword
     * @param array $filters
     * @return array
     */
    public static function getUsersSortsByKeyword($controller, $innovations, $keyword, $filters = array())
    {
        $filtered = array();
        $already = array();
        $innovations_ids = array();
        $keyword_array = explode(' ', $keyword);
        if (count($filters) > 0) {
            foreach ($filters as $filter) {
                $already[] = $filter;
            }
        }

        $em = $controller->getDoctrine()->getManager();
        $skills = $em->getRepository('AppBundle:Skill')->searchSkillByTitle($keyword);
        foreach ($skills as $skill) {
            $id = 's-' . $skill->getId();
            if (!in_array($id, $already)) {
                $already[] = $id;
                $filtered[] = array(
                    "id" => $id,
                    "text" => $skill->getTitle(),
                );
            }
        }

        foreach ($innovations as $innovation) {
            if (count($innovations_ids) == 0 || (count($innovations_ids) > 0 && in_array($innovation['id'], $innovations_ids))) {
                if ($innovation['entity'] && $innovation['entity']['title'] && self::stringContainsTermsArray($innovation['entity']['title'], $keyword_array)) {
                    $id = 'e-' . $innovation['entity']['id'];
                    if (!in_array($id, $already)) {
                        $already[] = $id;
                        $filtered[] = array(
                            "id" => $id,
                            "text" => $innovation['entity']['title'],
                        );
                    }
                }
                if ($innovation['brand'] && $innovation['brand']['title'] && self::stringContainsTermsArray($innovation['brand']['title'], $keyword_array)) {
                    $id = 'b-' . $innovation['brand']['id'];
                    if (!in_array($id, $already)) {
                        $already[] = $id;
                        $filtered[] = array(
                            "id" => $id,
                            "text" => $innovation['brand']['title'],
                        );
                    }
                }
                if ($innovation['title'] && self::stringContainsTermsArray($innovation['title'], $keyword_array)) {
                    $id = 'i-' . $innovation['id'];
                    if (!in_array($id, $already)) {
                        $already[] = $id;
                        $filtered[] = array(
                            "id" => $id,
                            "text" => $innovation['title'],
                        );
                    }
                }
            }
        }
        usort($filtered, function ($a, $b) use ($keyword) {
            $keyword = strtolower($keyword);
            $a_text = strtolower($a['text']);
            $b_text = strtolower($b['text']);
            // prioritize exact matches first
            if ($a_text == $keyword) return -1;
            if ($b_text == $keyword) return 1;

            // prioritize terms containing the keyword next
            $x = strpos($a_text, $keyword);
            $y = strpos($b_text, $keyword);
            if ($x !== false && $y === false) return -1;
            if ($y !== false && $x === false) return 1;
            if ($x !== false && $y !== false) {  // both terms contain the keyword, so...
                if ($x != $y) {  // prioritize matches closer to the beginning of the term
                    return $x > $y ? 1 : -1;
                }

                /*
                // both terms contain the keyword at the same position, so...
                $al = strlen($a_text);
                $bl = strlen($b_text);
                $min = ($bl - 2);
                $max = ($bl + 2);
                if (!in_array($al, range($min, $max))) { // prioritize terms with fewer characters other than the keyword
                    return $al > $bl ? 1 : -1;
                }
                */
                // both terms contain the same number of additional characters
                return (strcmp($a_text, $b_text) > 0) ? 1 : -1;
                // or do additional checks...
            }
            // neither terms contain the keyword

            // check the character similarity...
            $ac = levenshtein($keyword, $a_text);
            $bc = levenshtein($keyword, $b_text);
            if ($ac != $bc) {
                return $ac > $bc ? 1 : -1;
            }

            return (strcmp($a_text, $b_text) > 0) ? 1 : -1;
            // or sort alphabetically with strcmp($a, $b);
            // or do additional checks, similar_text, etc.
        });
        return $filtered;
    }

    /**
     * String contains term array
     * @param $string
     * @param $terms
     * @return bool
     */
    public static function stringContainsTermsArray($string, $terms)
    {
        $check = true;
        foreach ($terms as $term) {
            if (strpos(strtolower($string), strtolower($term)) === false) {
                $check = false;
            }
        }
        return $check;
    }


    /**
     * Get user infos for user settings page.
     *
     * @Route("/api/user/settings/infos", name="user_settings_get_infos", methods={"POST"})
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
     *                  property="accept_scheduled_emails",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="accept_newsletter",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="accept_contact",
     *                  type="boolean"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function getInfosUserSettingsAction()
    {
        $user = $this->getUser();
        $ret = array('status' => 'success');
        $ret['accept_scheduled_emails'] = $user->getAcceptScheduledEmails();
        $ret['accept_newsletter'] = $user->getAcceptNewsletter();
        $ret['accept_contact'] = $user->getAcceptContact();
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Update user infos for user settings page.
     *
     * @Route("/api/user/settings/infos/update", name="user_settings_update", methods={"POST"})
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
     *                  property="accept_scheduled_emails",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="accept_newsletter",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="accept_contact",
     *                  type="boolean"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function updateUserSettingsAction()
    {
        $user = $this->getUser();
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $target = $request->request->get('target');
        $value = $request->request->get('value');
        $value = ($value == "1");
        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $pernodWorker = $this->container->get('AppBundle\Worker\PernodWorker');
        $update = false;
        if ($target) {
            switch ($target) {
                case 'accept_scheduled_emails':
                    if ($user->getAcceptScheduledEmails() != $value) {
                        $user->setAcceptScheduledEmails($value);
                        $em->flush();
                        $update = true;
                    }
                    break;
                case 'accept_newsletter':
                    if ($user->getAcceptNewsletter() != $value) {
                        $user->setAcceptNewsletter($value);
                        $em->flush();
                        $update = true;
                    }
                    break;
                case 'accept_contact':
                    if ($user->getAcceptContact() != $value) {
                        $user->setAcceptContact($value);
                        $em->flush();
                        $update = true;
                    }
                    break;
            }
        }
        if ($update) {
            $pernodWorker->later()->generateOtherDatas();
        }
        $ret = array('status' => 'success', 'update' => $update, 'new_value' => $value);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Update user infos for user settings page.
     *
     * @Route("/api/user/skill/update", name="user_skill_update", methods={"POST"})
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
     *                  property="skills",
     *                  type="array"
     *              )
     *          )
     *      )
     * )
     * @OA\Tag(name="User Api")
     */
    public function updateUserSkillsAction()
    {
        $current_user = $this->getUser();
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $target_user_id = $request->request->get('user_id');
        $skill_id = $request->request->get('skill_id');
        $action = $request->request->get('action');

        $csrf_token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('hub_token', $csrf_token)) {
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $target_user = ($target_user_id) ? $em->getRepository('AppBundle:User')->find($target_user_id) : null;
        if (!$target_user || !$skill_id || !$action || ($action && !in_array($action, ['add', 'remove', 'remove-all']))) {
            $response = new Response(json_encode(array(
                'status' => 'error',
                'data' => array(
                    'target_user_id' => $target_user_id,
                    'skill_id' => $skill_id,
                    'action' => $action
                ),
                'message' => 'Unknow skill error [RSXC-302]. Please try again or contact us using tchat box on the bottom right.',
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if ($target_user && $skill_id && $action) {
            $skill = $em->getRepository('AppBundle:Skill')->getOrCreateSkill($skill_id);
            $userSkill = $em->getRepository('AppBundle:UserSkill')->findOneBy(['user' => $target_user, 'sender' => $current_user, 'skill' => $skill]);
            switch ($action) {
                case 'add':
                    if ($userSkill) {
                        $response = new Response(json_encode(array(
                            'status' => 'error',
                            'message' => 'You already added this skill to ' . $target_user->getProperUsername(),
                        )));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    $userSkill = new UserSkill();
                    $userSkill->setUser($target_user);
                    $userSkill->setSkill($skill);
                    $userSkill->setSender($current_user);
                    $em->persist($userSkill);
                    $em->flush();
                    break;
                case 'remove':
                    if (!$userSkill) {
                        $response = new Response(json_encode(array(
                            'status' => 'error',
                            'message' => 'This skill was already removed.'
                        )));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    $em->remove($userSkill);
                    $em->flush();
                    break;
                case 'remove-all':
                    if ($target_user->getId() !== $current_user->getId()) {
                        $response = new Response(json_encode(array(
                            'status' => 'error',
                            'message' => 'You can\'t remove this skill.'
                        )));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    $em->getRepository('AppBundle:UserSkill')->removeSkillForUser($skill, $target_user);
                    break;
            }
        }
        $target_user = $em->getRepository('AppBundle:User')->find($target_user_id);
        $skills = ($target_user) ? $target_user->getSkills() : [];
        $ret = array('status' => 'success', 'skills' => $skills);
        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
