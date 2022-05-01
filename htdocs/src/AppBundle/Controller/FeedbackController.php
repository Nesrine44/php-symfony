<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\FeedbackInvitation;
use AppBundle\Entity\Feedback;
use Swagger\Annotations as OA;

class FeedbackController extends Controller implements ActionController
{

    /**
     *  Create feedback invitation. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/feedback/invite", name="explore_feedback_invite", methods={"POST"})
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
     *                  property="message",
     *                  type="string",
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="people",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified user id"
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     */
    public function feedbackInviteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $sender = $this->getUser();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $mailer = $this->container->get('app.mailer');

        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['id' => $request->request->get('people')]);

        $innovation = $em->getRepository('AppBundle:Innovation')
            ->findOneBy(['id' => $request->request->get('innovation_id')]);

        if(!$user || !$innovation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'User or Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$sender->canEditThisInnovation($innovation)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$user->getAcceptContact()){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'This user doesn\'t want to be contacted. Please select another one.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $feedbackInvitation = $em->getRepository('AppBundle:FeedbackInvitation')
            ->findOneBy([
                'user' => $user,
                'innovation' => $innovation,
                'status' => FeedbackInvitation::STATUS_PENDING
            ]);

        if($feedbackInvitation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'An invitation is already pending for this user.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $feedbackInvitation = new FeedbackInvitation();
        $feedbackInvitation->setToken($user->getId(), $sender->getId(), $innovation->getId());
        $feedbackInvitation->setUser($user);
        $feedbackInvitation->setSender($sender);
        $feedbackInvitation->setInnovation($innovation);
        $feedbackInvitation->setMessage(Settings::getXssCleanString($request->request->get('message')));

        $em->persist($feedbackInvitation);
        $em->flush();

        $mailer->sendFeedbackInvitationEmail($feedbackInvitation);
        $em->getRepository('AppBundle:Activity')->createFeedbackActivity($sender, $innovation, Activity::ACTION_FEEDBACK_REQUEST, $user);

        $response = new Response(json_encode(array('status' => 'success')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     *  Create feedback from feedback invitation.
     *
     * @Route("/api/feedback/answer", name="explore_feedback_answer", methods={"POST"})
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
     *                  property="message",
     *                  type="string",
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="invitation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Feedback invitation id"
     *      ),
     *      @OA\Parameter(
     *          name="message",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Feedback message"
     *      ),
     *      @OA\Parameter(
     *          name="rating",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Feedback innovation rating"
     *      ),
     *      @OA\Parameter(
     *          name="role",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="User role"
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     */
    public function feedbackAnswerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $feedbackInv = $em->getRepository('AppBundle:FeedbackInvitation')
            ->findOneBy(['id' => $request->request->get('invitation_id')]);

        if(!$feedbackInv){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if($feedbackInv->getStatus() == FeedbackInvitation::STATUS_REMOVED ||
            $feedbackInv->getStatus() == FeedbackInvitation::STATUS_ANSWERED){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You already gave a feedback or your access has been revoked')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $feedback = new Feedback();
        $feedback->setInvitation($feedbackInv);
        $feedback->setMessage(Settings::getXssCleanString($request->request->get('message')));
        $feedback->setRating($request->request->get('rating'));
        $feedback->setRole(Settings::getXssCleanString($request->request->get('role')));

        $feedbackInv->setStatus(FeedbackInvitation::STATUS_ANSWERED);

        $em->persist($feedback);
        $em->flush();

        $mailer = $this->container->get('app.mailer');
        $mailer->sendFeedbackEmail($feedback);
        $em->getRepository('AppBundle:Activity')->createFeedbackActivity($user, $feedbackInv->getInnovation(), Activity::ACTION_FEEDBACK_ANSWER);

        $response = new Response(json_encode(array('status' => 'success')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *  Get feedbacks infos for innovation. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/feedback/explore/infos", name="explore_feedbacks_info_ws", methods={"POST"})
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
     *                  property="message",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="total_feedbackers",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="total_feedbacks",
     *                  type="integer"
     *              ),
     *              @OA\Property(
     *                  property="feedbackers",
     *                  type="array"
     *              ),
     *              @OA\Property(
     *                  property="feedbacks",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function feedbacksInfoWsAction() {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $id = $request->request->get('innovation_id');

        $innovation = $em->getRepository('AppBundle:Innovation')
            ->findOneBy(['id' => $id]);

        if(!$innovation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$user->canEditThisInnovation($innovation)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $feedbackers = $em->getRepository('AppBundle:FeedbackInvitation')->getFeedbackersForInnovation($id);
        $total_feedbackers = $em->getRepository('AppBundle:FeedbackInvitation')->getCountFeedbackersForInnovation($id);
        $feedbacks = $em->getRepository('AppBundle:Feedback')->getFeedbacksForInnovation($id);
        $total_feedbacks = $em->getRepository('AppBundle:Feedback')->getCountFeedbacksForInnovation($id);

        $feedbackersList = [];
        foreach ($feedbackers as $feedbacker) {
            array_push($feedbackersList, $feedbacker->toArray());
        }

        $feedbacksList = [];
        foreach ($feedbacks as $feedback) {
            array_push($feedbacksList, $feedback->toArray());
        }

        $response = new Response(json_encode([
            'status' => 'success',
            'total_feedbackers' => $total_feedbackers,
            'total_feedbacks' => $total_feedbacks,
            'feedbackers' => $feedbackersList,
            'feedbacks' => $feedbacksList,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *  Load more feedbackers. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/feedback/feedbackers/load-more", name="explore_get_feedbackers_ws", methods={"POST"})
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
     *                  property="message",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="list",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFeedbackersWsAction($id) {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $id = $request->request->get('innovation_id');

        $innovation = $em->getRepository('AppBundle:Innovation')->find($id);

        if(!$innovation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$user->canEditThisInnovation($innovation)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);

        $feedbackers = $em->getRepository('AppBundle:FeedbackInvitation')->getFeedbackersForInnovation($id, $offset, $limit);

        $feedbackersList = [];
        foreach ($feedbackers as $feedbacker) {
            array_push($feedbackersList, $feedbacker->toArray());
        }

        $response = new Response(json_encode(['status' => 'succes', 'list' => $feedbackersList]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *  Load more feedbacks. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/feedback/feedbacks/load-more", name="explore_get_feedbacks_ws", methods={"POST"})
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
     *                  property="message",
     *                  type="string",
     *                  default="success"
     *              ),
     *              @OA\Property(
     *                  property="list",
     *                  type="array"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="innovation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          required=false
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          required=false
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFeedbacksWsAction() {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $id = $request->request->get('innovation_id');

        $innovation = $em->getRepository('AppBundle:Innovation')->find($id);

        if(!$innovation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Innovation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$user->canEditThisInnovation($innovation)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $offset = $request->request->get('offset', 0);
        $limit = $request->request->get('limit', 20);
        $feedbacks = $em->getRepository('AppBundle:Feedback')->getFeedbacksForInnovation($id, $offset, $limit);

        $feedbacksList = [];
        foreach ($feedbacks as $feedback) {
            array_push($feedbacksList, $feedback->toArray());
        }

        $response = new Response(json_encode(['status' => 'success', 'list' => $feedbacksList]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *  Remove feedbacker access by invitation. (Only available for users who can edit specified innovation)
     *
     * @Route("/api/feedback/remove-access", name="explore_remove_feedbacker_access_ws", methods={"POST"})
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
     *                  property="message",
     *                  type="string",
     *                  default="success"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="invitation_id",
     *          in="query",
     *          type="integer",
     *          required=true,
     *          description="Specified innovation id"
     *      )
     * )
     * @OA\Tag(name="Feedback Api")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFeedbackerAccessWsAction() {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $csrf_token = $request->request->get('token');
        if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $user = $this->getUser();
        $id = $request->request->get('invitation_id');
        $feedbackInvitation = $em->getRepository('AppBundle:FeedbackInvitation')->find($id);

        if(!$feedbackInvitation){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'Feedback invitation not found')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if(!$user->canEditThisInnovation($feedbackInvitation->getInnovation())){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $target_user = $feedbackInvitation->getUser();
        $innovation = $feedbackInvitation->getInnovation();
        $em->getRepository('AppBundle:FeedbackInvitation')->removeAccessForUserAndInnovation($target_user, $innovation);
        $ret = array('status' => 'success');

        $response = new Response(json_encode($ret));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
