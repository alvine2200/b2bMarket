<?php

namespace App\Models\ProductsServices;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'delivery_address_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, "purchase_order_items")->withPivot("quantity")->withTimestamps();
    }

    public function deliveryAddress(){
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function invoice(){
        return $this->hasOne(Invoice::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function getSubTotalAttribute(){ 
        $products = $this->products()->withPivot("quantity")->get();
        $sub_total = 0;
        foreach($products as $product){
            $sub_total+= $product->pivot->quantity * $product->unit_price;
        }
        return $sub_total;
    }

    public function getTaxAttribute(){ 
        $products = $this->products()->withPivot("quantity")->get();
        $tax = 0;
        foreach($products as $product){
            $tax += $product->pivot->quantity * $product->tax;
        }
        return $tax;
    }
    public function getTotalAttribute(){ 
        return $this->subTotal+$this->tax;
    }

    public function getPaidAttribute(){
        return $this->payments()->sum("amount");
    }
    
    public function getUnpaidAttribute(){
        return $this->total - $this->paid;
    }

    public function getPaymentStatusAttribute(){
        if ($this->unpaid == 0){
            return "Paid";
        }

        if ($this->paid == $this->unpaid){
            return "Unpaid";
        }

        return "Partial";
    }
}
