<?php

namespace App\Http\Resources\ProductsServices;

use App\Http\Resources\Selectables\SelectableName;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Product extends JsonResource
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
            'id'=>$this->id,
            'business_id'=>$this->business_id,
            'business_slug' => $this->business->slug,
            'created_by'=>$this->creator->id,
            'created_by_slug'=>$this->creator->slug,
            'name'=>$this->name,
            'gallery_image'=>!empty($this->gallery_image) ? url(Storage::url($this->gallery_image)) : '',
            'thumbnail_image'=>!empty($this->thumbnail_image) ? url(Storage::url($this->thumbnail_image)) : '',
            'description'=>$this->description,
            'quantity'=>$this->quantity,
            'unit_price'=>$this->unit_price,
            'category'=>$this->category,
            'units'=>$this->units,
            'sku'=>$this->sku,
            'minimum_purchase_quantity'=>$this->minimum_purchase_quantity,
            'tags'=>$this->tags != null ? SelectableName::collection($this->tags) : [],
            'pdf_specs'=>!empty($this->pdf_specs) ? url(Storage::url($this->pdf_specs)) : '',
            "is_saved" => User::find(Auth::user()->id)->getIsProductSavedAttribute($this->id),
            "in_stock" => $this->inStock
        ];
    }
}
