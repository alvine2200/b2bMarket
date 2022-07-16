<?php

namespace App\Providers;

use App\Events\BusinessMatchedEvent;
use App\Events\SocialNetworking\ChatMessageCreatedEvent;
use App\Listeners\SendBusinessMatchedNotificationListener;
use App\Listeners\SendBusinessLikedNotificationListener;
use App\Listeners\SocialNetworking\SendChatMessageNotificationListener;
use Illuminate\Auth\Events\BusinessLikedEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Business;
use App\Models\SocialNetworking\ChatRoom;
use App\Observers\Profiling\BusinessObserver;
use App\Observers\SocialNetworking\ChatRoomObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ChatMessageCreatedEvent::class => [
            SendChatMessageNotificationListener::class
        ],
        BusinessMatchedEvent::class => [
            SendBusinessMatchedNotificationListener::class
        ],
        BusinessLikedEvent::class => [
            SendBusinessLikedNotificationListener::class
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        Business::observe(BusinessObserver::class);
        ChatRoom::observe(ChatRoomObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
