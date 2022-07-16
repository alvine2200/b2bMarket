<?php

namespace App\Listeners;

use App\Events\BusinessLikedEvent;
use App\Notifications\BusinessLikedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBusinessLikedNotificationListener
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
     * @param  \App\Providers\BusinessLikedEvent  $event
     * @return void
     */
    public function handle(BusinessLikedEvent $event)
    {
        $associatedBusinesses = $event->associatedBusinesses;
        $businessToNotify = $associatedBusinesses['initiator'];
        // notify like business user
        $userToNotify = $businessToNotify->user;
        $userToNotify->notify(new BusinessLikedNotification($associatedBusinesses));
    }
}
