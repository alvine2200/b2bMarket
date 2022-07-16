<?php

namespace App\Listeners\SocialNetworking;

use App\Events\SocialNetworking\ChatMessageCreatedEvent;
use App\Notifications\ReceivedMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChatMessageNotificationListener
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
     * @param  \App\Events\SocialNetworking\ChatMessageCreatedEvent  $event
     * @return void
     */
    public function handle(ChatMessageCreatedEvent $event)
    {
        //
        $chatMessage = $event->chatMessage;
        $recipients = $chatMessage->recipients;

        foreach($recipients as $recipient){
            $recipient->notify(new ReceivedMessage($chatMessage));
        }
    }
}
