<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestStatusNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $description;
    protected string $state;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $description
     * @param string $state
     */
    public function __construct(string $title, string $description, string $state)
    {
        $this->title = $title;
        $this->description = $description;
        $this->state = $state;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => $this->title,
            "description" => $this->description,
            "state" => $this->state,
            'type' => 'system',
        ];
    }
}
