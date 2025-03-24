<?php

namespace App\Notifications;

use App\Models\Edition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BadgeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $edition;

    /**
     * Create a new notification instance.
     */
    public function __construct($sms = false)
    {
        $this->edition = Edition::current()->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/badge/' . $notifiable->token);
        $subject = str_replace('[firstName]', $notifiable->first_name, $this->edition->mail_notification_subject);
        $subject = str_replace('[lastName]', $notifiable->last_name, $subject);
        $message = str_replace('[firstName]', $notifiable->first_name, $this->edition->mail_notification_body);
        $message = str_replace('[lastName]', $notifiable->last_name, $message);

        return (new MailMessage)
            ->subject($subject)
            ->markdown('mail.badge', [
                'user' => $notifiable,
                'message' => $message,
                'url' => $url
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}