<?php

namespace App\Models\ProductsServices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order_id',
        'invoice_number'
    ];

    public function order(){
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
