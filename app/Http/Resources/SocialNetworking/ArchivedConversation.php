<?php

namespace App\Http\Resources\SocialNetworking;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchivedConversation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "business_slug"=> $this->businessSlug,
            "business_logo"=> $this->businessLogo,
            "business_name"=> $this->businessName,
            "chat_room_id"=> $this->chatRoom?->id,
            "chat_room_name"=> $this->chatRoom?->name,
            "is_direct"=>$this->is_direct,
            "last_message_body"=> $this->archivedLastMessageBody,
            "last_activity"=> $this->archivedAt,
            "recipient_id"=> $this->recipient?->id,
            // "title"=> $this->title,
            "unread"=> 0,
        ];
    }
}
