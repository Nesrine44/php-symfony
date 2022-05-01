<?php

namespace Dtc\QueueBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Dtc\GridBundle\Annotation as Grid;

/**
 * @Grid\Grid(actions={@Grid\ShowAction(), @Grid\DeleteAction(label="Archive")},sort=@Grid\Sort(column="whenAt"))
 * @ODM\Document(db="dtc_queue", collection="job")
 */
class Job extends BaseJob
{
    const STATUS_ARCHIVE = 'archive';
}
