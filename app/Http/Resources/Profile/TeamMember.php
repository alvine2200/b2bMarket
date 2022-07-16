<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamMember extends JsonResource
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
            "full_name"=>$this->full_name,
            "quote"=>$this->quote,
            "is_team_member"=>$this->is_team_member,
            "profile_image"=>url(Storage::url($this->profile_image)),
            "business_role"=>$this->businessRole->name,
            "slug"=>$this->slug,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
        return parent::toArray($request);
    }
}
