<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessLikedNotification extends Notification
{
    use Queueable;

    public $associatedBusinesses;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($associatedBusinesses)
    {
        //
        $this->associatedBusinesses = $associatedBusinesses;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $likedBusiness = $this->associatedBusinesses["liked"];
        $likedBusinessUser = $likedBusiness->user;

        $initiatorBusiness = $this->associatedBusinesses["initiator"];

        return (new MailMessage)
                    ->subject("Your business liked")
                    ->greeting("Hi, $likedBusinessUser->full_name\n")
                    ->line("$likedBusiness->name business has been liked by: $initiatorBusiness->name")
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $likedBusiness = $this->associatedBusinesses["liked"];

        $initiatorBusiness = $this->associatedBusinesses["initiator"];

        return [
            //
            "image"=>$this->initiatorBusiness->logo,
            "title"=>"Your business liked",
            "body"=>"$likedBusiness->name business has been liked by: $initiatorBusiness->name",
        ];
    }
}
