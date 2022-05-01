<?php

namespace AppBundle\EventSubscriber;


use AppBundle\Controller\ActionController;
use AppBundle\Service\WebsiteGlobalDatas;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActionSubscriber implements EventSubscriberInterface
{

    private $websiteGlobalDataService;
    private $tokenStorage;

    public function __construct(WebsiteGlobalDatas $websiteGlobalDataService, TokenStorageInterface $tokenStorage)
    {
        $this->websiteGlobalDataService = $websiteGlobalDataService;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if (!$token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!$token->isAuthenticated()) {
            return;
        }

        if (!$user = $token->getUser()) {
            return;
        }

        if ($controller[0] instanceof ActionController) {
            if(!$this->websiteGlobalDataService->actionIsPossible($user)){
                $event->setController(function() {
                    return new Response("force_reload", 503);
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}