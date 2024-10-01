<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailOffre extends Notification
{
    use Queueable;

    public $contenu;
    
    public $email;
  
    
    /**
     * Create a new notification instance.
     */
    public function __construct($contenu,$email)
    {
        $this->email = $email;
        $this->contenu = $contenu;
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
        return (new MailMessage)
                    ->subject('Offre Assigné')
                    ->line('Bonjour.')
                    ->view('emails.offer_assigned', ['contenu' => $this->contenu]);
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