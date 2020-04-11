<?php

namespace DBSystem\Observers;

use DBSystem\BackupDestination;
use DBSystem\Destination;
use DBSystem\TaskDestination;

class DestinationObserver
{
    /**
     * Delete relations
     *
     * @param Destination $destination
     */
    public function deleted(Destination $destination)
    {
        TaskDestination::where('destination_id', $destination->id)->delete();
        BackupDestination::where('destination_id', $destination->id)->delete();
    }
}