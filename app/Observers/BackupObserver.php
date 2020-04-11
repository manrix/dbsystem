<?php

namespace DBSystem\Observers;

use DBSystem\Backup;

class BackupObserver
{
    /**
     * Delete relations
     *
     * @param Backup $backup
     */
    public function deleted(Backup $backup)
    {
        $backup->destinations()->delete();
    }
}