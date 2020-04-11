<?php

namespace DBSystem\Notifications;

use DBSystem\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskCompleted extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->success()
            ->subject('Task Completed')
            ->line("Task #{$this->task->id} ({$this->task->name}) was successfully completed.")
            ->action("Login to " . config('app.name'), route('login'));
    }
}
