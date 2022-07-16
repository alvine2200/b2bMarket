<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserNewsResource extends JsonResource
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
            "id"=> $this->id,
            "title"=> $this->title,
            "subtitle"=> $this->subtitle,
            "body"=> $this->body,
            "image"=> !empty($this->image) ? url(Storage::url($this->image)) : '',
            "author"=> $this->author,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
        ];
        return parent::toArray($request);
    }
}
