<?php

namespace DBSystem\Observers;

use DBSystem\TaskLog;

class TaskLogObserver
{
    /**
     * Delete relations
     *
     * @param TaskLog $taskLog
     */
    public function deleted(TaskLog $taskLog)
    {
        $taskLog->logs()->delete();
    }
}