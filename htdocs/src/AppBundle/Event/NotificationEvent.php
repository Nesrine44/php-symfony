<?php
namespace AppBundle\Event;

use AppBundle\Entity\Innovation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Event;
use AppBundle\Worker\PernodWorker;

class NotificationEvent extends Event
{
    const NAME = 'event.notification';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * On view innovation.
     *
     * @param Innovation $innovation
     */
    public function onViewInnovation(Innovation $innovation)
    {
        $this->em->getRepository('AppBundle:Notification')->createOrUpdatePromoteInnovation($innovation, 1, 0, 0);
    }

    /**
     * On export innovation.
     *
     * @param Innovation $innovation
     */
    public function onExportInnovation(Innovation $innovation)
    {
        $this->em->getRepository('AppBundle:Notification')->createOrUpdatePromoteInnovation($innovation, 0, 1, 0);
    }

    /**
     * On share innovation.
     *
     * @param Innovation $innovation
     */
    public function onShareInnovation(Innovation $innovation)
    {
        $this->em->getRepository('AppBundle:Notification')->createOrUpdatePromoteInnovation($innovation, 0, 0, 1);
    }




    
}