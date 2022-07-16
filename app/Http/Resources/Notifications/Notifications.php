<?php

namespace App\Http\Resources\Notifications;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class Notifications extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $data = $this->data;
        // $data["notification_id"] = $this->id;
        $repr = [
            "notification_id" => $this->id,
            "image" => !empty($this->data["image"]) ? url(Storage::url($this->data["image"])) : '',
            "title" => $this->data["title"],
            "body" => $this->data["body"],
            "created_at" => $this->created_at,
        ];
        
        isset($this->data["conversation_id"]) ? $repr["conversation_id"] = $this->data["conversation_id"] : "";

        return $repr;
    }
}
