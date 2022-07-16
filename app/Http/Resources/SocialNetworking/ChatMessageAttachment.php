<?php

namespace App\Http\Resources\SocialNetworking;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ChatMessageAttachment extends JsonResource
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
            'id' => $this->id,
            "attachment" => !empty($this->attachment) ? url(Storage::url($this->attachment)) : '',
        ];
    }
}
