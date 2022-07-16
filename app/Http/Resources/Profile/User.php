<?php

namespace App\Http\Resources\Profile;

use App\Http\Resources\Marketplace\Business as MarketplaceBusinessResource;
use App\Http\Resources\Profile\Business as ProfileBusinessResource;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = [
            "id" => $this->id,
            "slug" => $this->slug,
            "full_name" => $this->full_name,
        ];

        // $bs_resource = new MarketplaceBusinessResource($this->business);
        $result["business"] = new ProfileBusinessResource($this->business);
        return $result;
    }
}
