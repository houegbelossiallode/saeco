<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     public $rendezvous;
    public function __construct($rendezvous)
    {
        $this->rendezvous = $rendezvous;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */


    public function toDatabase(object $notifiable): array
    {

        return [
              'message'=>  'Vôtre rendez-vous avec Mr/Mme' . $this->rendezvous->client->user->nom . ' ' . $this->rendezvous->client->user->prenom . ' est pour aujoud\'hui',
              'date'=> $this->rendezvous->date_du_rdv,
        ];
    }



    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Rappel de rendez-vous')
                   // ->action('Notification Action', url('/'))
                    ->line('Vôtre rendez-vous avec Mr/Mme  ' . $this->rendezvous->client->user->nom . ' ' . $this->rendezvous->client->user->prenom. ' est pour aujoud\'hui  ' . $this->rendezvous->date_du_rdv);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

}