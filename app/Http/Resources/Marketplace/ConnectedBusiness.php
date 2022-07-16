<?php

namespace App\Http\Resources\Marketplace;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Selectables\Selectable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConnectedBusiness extends JsonResource
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
            "logo" => !empty($this->logo) ? url(Storage::url($this->logo)) : '',
            "banner" => !empty($this->banner) ? url(Storage::url($this->banner)) : '',
            // todo: add random product
        ];
        // return parent::toArray($request);
    }
}
