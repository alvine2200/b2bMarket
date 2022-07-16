<?php

namespace App\Observers\SocialNetworking;

use Illuminate\Support\Str;
use App\Models\SocialNetworking\ChatRoom;

class ChatRoomObserver
{
    /**
     * Handle the ChatRoom "created" event.
     *
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return void
     */
    public function created(ChatRoom $chatRoom)
    {
        //
        $chatRoom->slug = Str::slug($chatRoom->name);

        $chatRoomLink = Str::random(10);
        while(ChatRoom::where('link', $chatRoomLink)->exists()){
            $chatRoomLink = Str::random(10);
        }

        $chatRoom->link = $chatRoomLink;
        $chatRoom->saveQuietly();
    }

    /**
     * Handle the ChatRoom "updated" event.
     *
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return void
     */
    public function updated(ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Handle the ChatRoom "deleted" event.
     *
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return void
     */
    public function deleted(ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Handle the ChatRoom "restored" event.
     *
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return void
     */
    public function restored(ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Handle the ChatRoom "force deleted" event.
     *
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return void
     */
    public function forceDeleted(ChatRoom $chatRoom)
    {
        //
    }
}
