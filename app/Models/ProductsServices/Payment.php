<?php

namespace App\Models\ProductsServices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "purchase_order_id",
        "amount",
        "payment_number"
    ];

    public function order(){
        return $this->belongsTo(PurchaseOrder::class, "purchase_order_id");
    }
}
