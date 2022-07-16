<?php

namespace App\Events\SocialNetworking;

use App\Models\SocialNetworking\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * The chatMessage instance.
     *
     * @var \App\Models\SocialNetworking\ChatMessage
     */
    public $chatMessage;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\SocialNetworking\ChatMessage  $chatMessage
     * @return void
     */
    public function __construct(ChatMessage $chatMessage)
    {
        //
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
