<?php

namespace App\Listeners;

use App\Events\BusinessMatchedEvent;
use App\Notifications\BusinessMatchedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBusinessMatchedNotificationListener
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
     * @param  \App\Events\BusinessMatchedEvent  $event
     * @return void
     */
    public function handle(BusinessMatchedEvent $event)
    {
        //
        $connectedBusinesses = $event->connectedBusinesses;
        if(count($connectedBusinesses) != 2){
            return;
        }

        for ($i=0; $i < count($connectedBusinesses); $i++) { 
            if($i == 0){ 
                $connectingTo = $connectedBusinesses[1];
            }
            else{
                $connectingTo = $connectedBusinesses[0];
            }

            $userToNotify = $connectedBusinesses[$i]->user;
            $userToNotify->notify(new BusinessMatchedNotification($connectingTo));
        }

        foreach ($connectedBusinesses as $connectedBusiness){
            $userToNotify = $connectedBusiness->user;
        }
    }
}
