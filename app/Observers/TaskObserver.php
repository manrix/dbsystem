<?php

namespace DBSystem\Observers;

use DBSystem\Task;

class TaskObserver
{
    /**
     * Generate token
     *
     * @param Task $task
     */
    public function creating(Task $task)
    {
        $task->token = generate_token(32);
    }

    /**
     * Delete relations
     *
     * @param Task $task
     */
    public function deleted(Task $task)
    {
        $task->databases()->delete();
        $task->file()->delete();
        $task->destinations()->delete();
        $task->statistics()->delete();

        // remove all logs
        $logs = $task->logs()->get();
        foreach ($logs as $log) {
            $log->logs()->delete();
        }
        $task->logs()->delete();
    }
}