<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use AppBundle\Event\InnovationEvent;
use Dtc\QueueBundle\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as OA;

use AppBundle\Entity\MDRecommandation;
use AppBundle\Entity\MDCompetitor;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Picture;

class ManageController extends Controller
{

    /**
     * Manage list URL.
     *
     * @Route("/content/manage", name="manage_list", methods={"GET"})
     * @Route("/content/manage/tab/{tab}", name="manage_list_tab", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=200,
     *          description="Go to manage list."
     *      )
     * )
     * @OA\Tag(name="Routing")
     *
     * @param string $tab
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($tab = 'overview')
    {
        $user = $this->getUser();
        $hasManageAccess = $user->hasManageAccess();
        $isMD = $user->isManagingDirector();
        if(!$hasManageAccess && !$isMD){
            $this->addFlash(
                'error',
                '[403] - You have no right to this page.'
            );
            return $this->redirectToRoute('homepage');
        }

        if (!$hasManageAccess && $isMD) {
            $tab = 'feedback';
        }

        return $this->render('@App/manage/list.html.twig', [
            'tab' => $tab,

        ]);
    }

    /**
     * Manage MD :  Get or Update recommandation
     *
     * @Route("/api/manage/md/recommandation", name="manage_ws_recommandation", methods={"GET", "POST"})
     *
     * @OA\Tag(name="Manage MD")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recommandationsWsAction() {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $recommandationRepo = $em->getRepository('AppBundle:MDRecommandation');
        $settingsRepo = $em->getRepository('AppBundle:Settings');
        $periode = $settingsRepo->getCurrentSettings()->getCurrentFinancialDate();


        if(!$user->isManagingDirector()){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        // Returning recommandations for this period
        if ($request->getMethod() === 'GET') {
            $reco = $recommandationRepo->getRecommandationForUser($user);
            $response = new Response(json_encode([
                'recommandation' => ($reco) ? $reco->toArray() : null,
            ]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        if ($request->getMethod() === 'POST') {
            $csrf_token = $request->request->get('token');
            if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $reco = $recommandationRepo->getRecommandationForUser($user);
            if(!$reco) {
                $reco = new MDRecommandation();
            }
            $reco->setRecommandation($request->request->get('recommandation'));
            $reco->setFeedback(Settings::getXssCleanString($request->request->get('feedback')));
            $reco->setPeriode($periode);
            $reco->setUser($user);
            $em->persist($reco);
            $em->flush();

            $response = new Response(json_encode($reco->toArray()));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $response = new Response('', 405);
        return $response;
    }

    /**
     * Manage MD :  Get or Update competitor
     *
     * @Route("/api/manage/md/competitors", name="manage_ws_competitors", methods={"GET", "POST"})
     *
     * @OA\Tag(name="Manage MD")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function competitorsWsAction() {
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $user = $this->getUser();

        $competitorsRepo = $em->getRepository('AppBundle:MDCompetitor');
        $tagRepo = $em->getRepository('AppBundle:Tag');
        $settingsRepo = $em->getRepository('AppBundle:Settings');
        $currentPeriod = $settingsRepo->getCurrentSettings()->getCurrentFinancialDate();

        if(!$user->isManagingDirector()){
            $response = new Response(json_encode(array('status' => 'error', 'message' => 'You have no right to do this action')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        // Returning recommandations for this period
        if ($request->getMethod() === 'GET') {
            $competitorsData = $competitorsRepo->getCompetitorsForUser($user);
            $competitors = [];
            foreach($competitorsData as $competitor) {
                array_push($competitors, $competitor->toArray());
            }
            $response = new Response(json_encode([
                'competitors' => $competitors,
            ]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        if ($request->getMethod() === 'POST') {
            $csrf_token = $request->request->get('token');
            if(!$this->isCsrfTokenValid('hub_token', $csrf_token)){
                $response = new Response(json_encode(array('status' => 'error', 'message' => 'Invalid CSRF Token. Please reload this page and try it again.')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $is_update = false;
            if ($request->request->get('competitor_id')) {
                $competitor = $competitorsRepo->find($request->request->get('competitor_id'));
                if(!$competitor){
                    $response = new Response(json_encode(array('status' => 'error', 'message' => 'Competitor to update not found')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                if(!$competitor->getUser() || ($competitor->getUser() && $competitor->getUser()->getId() != $user->getId())){
                    $response = new Response(json_encode(array('status' => 'error', 'message' => 'You are not owner of this competitor')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                $is_update = true;
            }else {
                $competitor = new MDCompetitor();
            }
            $files = $request->files->all();
            $image = $files['image'];
            $awsS3Uploader = $this->get('app.s3_uploader');

            if($is_update && $competitor->getPicture() && !$request->request->get('picture_id')) {
                // delete from aws s3
                $picture = $competitor->getPicture();
                $competitor->setPicture(null);
                $url =$picture->getPath();
                $awsS3Uploader->deleteFile($url);
                $em->remove($picture);
            }

            if ($image) {
                if($is_update && $competitor->getPicture()) {
                    // delete from aws s3
                    $picture = $competitor->getPicture();
                    $competitor->setPicture(null);
                    $url =$picture->getPath();
                    $awsS3Uploader->deleteFile($url);
                    $em->remove($picture);
                }
                $explode = explode('.', $image->getClientOriginalName());
                $extension = $explode[count($explode) - 1];
                $postPath = 'competitor/' . $currentPeriod . '/';
                $fileName = $user->getId() . '_' . md5(uniqid()) . '.' . $extension;


                $image->move(
                    $this->getParameter('upload_dir') . $postPath,
                    $fileName
                );

                $picture = new Picture();
                $picture->setFilename($postPath . $fileName);
                $em->persist($picture);


                $competitor->setPicture($picture);

                # upload to aws s3
                $path = $this->getParameter('upload_dir') . $postPath . $fileName;
                $awsS3Uploader->uploadFile('uploads/' . $postPath . $fileName, $path);

                $liip = $this->container->get('liip_imagine.service.filter');
                $picture->resizeImage($liip, "thumbnail_picture");
            }
            
            $competitor->setProductName(Settings::getXssCleanString($request->request->get('name')));
            $competitor->setCategory(Settings::getXssCleanString($request->request->get('category')));
            $competitor->setBrand(Settings::getXssCleanString($request->request->get('brand')));

            $competitor->removeAllTags();
            if ($request->request->get('tags')) {
                foreach ($request->request->get('tags') as $tagName) {
                    $tag = $tagRepo->getOrCreateTag($tagName);
                    $competitor->addTag($tag);
                }
            }

            $competitor->setPeriode($currentPeriod);
            $competitor->setUser($user);
            $em->persist($competitor);
            $em->flush();

            $response = new Response(json_encode($competitor->toArray()));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $response = new Response('', 405);
        return $response;
    }
}
