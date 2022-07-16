<?php

namespace App\Http\Resources\ProductsServices;

use Illuminate\Http\Resources\Json\JsonResource;

class SummarizedPurchaseOrder extends JsonResource
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
            "order_number" => $this->order_number,
            "status"=>$this->status,
            "payment_mode"=>$this->payment_mode,
            "delivery_mode"=>$this->delivery_mode,
            "sub_total"=>$this->subTotal,
            "tax"=>$this->tax,
            "total"=>$this->total,
            "paid"=>$this->paid,
            "unpaid"=>$this->unpaid,
            "payment_status"=>$this->paymentStatus,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at
        ];
    }
}
