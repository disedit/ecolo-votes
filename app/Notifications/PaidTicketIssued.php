<?php

namespace App\Notifications;

use App\Models\Edition;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaidTicketIssued extends Notification implements ShouldQueue
{
    use Queueable;

    // The associated payment
    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
        $url = url('/auto/' . $notifiable->currentLoginToken()->token);

        $edition = Edition::current()->first();

        $data = [
            'user' => $notifiable,
            'attendee' => $notifiable->attendee(),
            'payment' => $this->payment,
            'edition' => $edition
        ];
        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf = $pdf->output();

        return (new MailMessage)
            ->subject('[EGP Congress] Your ticket is ready')
            ->markdown('mail.paid_ticket_issued', [
                'user' => $notifiable,
                'url' => $url
            ])
            ->attachData($pdf, 'Invoice-' . $this->payment->id . '.pdf', [
                'mime' => 'application/pdf',
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
