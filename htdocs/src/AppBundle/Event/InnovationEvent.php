<?php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Worker\PernodWorker;

class InnovationEvent extends Event
{
    const NAME = 'event.innovation';

    /**
     * On update action.
     * Generate all_innovations array and all_consolidation array.
     *
     * @param PernodWorker $worker
     * @param Event|null $event
     */
    public function onUpdateAction(PernodWorker $worker, Event $event = null)
    {
        $worker->later()->generateAllInnovationsAndConsolidation();
    }


    /**
     * On create Innovation action.
     * Generate all_innovations array and all_consolidation array.
     *
     * @param PernodWorker $worker
     * @param array $innovation_array
     * @param Event|null $event
     */
    public function onCreateInnovationAction(PernodWorker $worker, $innovation_array, Event $event = null)
    {
        /**
         * This is a synchronous worker (it's really fast)
         */
        $worker->updateAllInnovationsAndConsolidationByAddingInnovation($innovation_array);
    }
    
    /**
     * On update Innovation action.
     * Generate all_innovations array and all_consolidation array.
     *
     * @param PernodWorker $worker
     * @param array $innovation_array
     * @param Event|null $event
     */
    public function onUpdateInnovationAction(PernodWorker $worker, $innovation_array, Event $event = null)
    {
        /**
         * This is a synchronous worker (it's really fast)
         */
        $worker->updateAllInnovationsAndConsolidationByInnovation($innovation_array);
    }

    /**
     * On create Innovation action.
     * Generate all_innovations array and all_consolidation array.
     *
     * @param PernodWorker $worker
     * @param int $innovation_id
     * @param Event|null $event
     */
    public function onDeleteInnovationAction(PernodWorker $worker, $innovation_id, Event $event = null)
    {
        /**
         * This is a synchronous worker (it's really fast)
         */
        $worker->updateAllInnovationsAndConsolidationByRemovingInnovation($innovation_id);
    }

    
}