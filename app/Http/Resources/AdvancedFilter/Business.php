<?php

namespace App\Http\Resources\AdvancedFilter;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Selectables\Selectable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Business extends JsonResource
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
            "id" => $this->id,
            "user_id" => $this->user->id,
            "business_type" => $this->business_type,
            "slug" => $this->slug,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "headquarters" => $this->headquarters->name,
            "operating_countries" => Selectable::collection($this->operating_countries),
            "expand_countries" => Selectable::collection($this->expand_countries),
            "main_services" => Selectable::collection($this->mainServices),
            "main_products" => Selectable::collection($this->mainProducts),
            "is_liked" => User::find(Auth::user()->id)->getIsBusinessLikedAttribute($this->id),
            "is_disliked" => User::find(Auth::user()->id)->getIsBusinessDisikedAttribute($this->id),
            "is_saved" => User::find(Auth::user()->id)->getIsBusinessSavedAttribute($this->id),
            "is_followed" => User::find(Auth::user()->id)->getIsBusinessFollowedAttribute($this->id),
            "size" => "$this->size_start_range-$this->size_end_range",
            "age" => "$this->age_start_range-$this->age_end_range"
            // todo: add random product
        ];
    }
}
