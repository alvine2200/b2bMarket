<?php

namespace App\Http\Resources\ProductsServices;

use Illuminate\Http\Resources\Json\JsonResource;

class Cart extends JsonResource
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
            'products'=>$this->products != null ? CartProduct::collection($this->products) : [],
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
