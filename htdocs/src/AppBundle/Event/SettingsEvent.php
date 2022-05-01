<?php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Worker\PernodWorker;

class SettingsEvent extends Event
{
    const NAME = 'event.settings';

    /**
     * On update action.
     * Generate other_datas array.
     *
     * @param PernodWorker $worker
     * @param Event|null $event
     */
    public function onUpdateAction(PernodWorker $worker, Event $event = null)
    {
        $worker->later()->generateOtherDatas();
    }
}