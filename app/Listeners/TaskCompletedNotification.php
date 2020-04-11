<?php

namespace DBSystem\Listeners;

use DBSystem\Events\TaskCompleted;

class TaskCompletedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TaskCompleted  $event
     * @return void
     */
    public function handle(TaskCompleted $event)
    {
        if (settings('task_notification')) {
            $user = $event->task->user()->first();
            if ($user) {
                $user->notify(new \DBSystem\Notifications\TaskCompleted($event->task));
            }
        }
    }
}
