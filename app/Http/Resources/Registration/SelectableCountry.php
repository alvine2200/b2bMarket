<?php

namespace App\Http\Resources\Registration;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectableCountry extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->name;
    }
}
