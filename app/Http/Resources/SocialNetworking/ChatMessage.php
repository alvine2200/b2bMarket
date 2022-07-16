<?php

namespace App\Http\Resources\SocialNetworking;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $conversation = $this->conversation;
        $attachments = $this->attachments;

        // if(count($attachments)>0){
        //     dd($attachments);
        // }

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "conversation" => [
                "id" => $conversation->id,
                "is_direct" => $conversation->is_direct,
                "created_at" => $conversation->created_at,
                "updated_at" => $conversation->updated_at,
            ],
            "attachments" => ChatMessageAttachment::collection($attachments),
        ];
    }
}
