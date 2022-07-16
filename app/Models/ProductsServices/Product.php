<?php

namespace App\Models\ProductsServices;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $cast = ['shifts' => 'array'];

    protected $fillable = [
        'business_id',
        'created_by',
        'name',
        'gallery_image',
        'thumbnail_image',
        'description',
        'quantity',
        'unit_price',
        'category',
        'units',
        'sku',
        'minimum_purchase_quantity',
        'tags',
        'pdf_specs',
    ];

    public function tags()
    {
        return $this->belongsToMany(SelectableProductTag::class, "product_tag", "product_id", "tag_id")->withTimestamps();
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function savedBy(){
        return $this->belongsToMany(User::class, "saved_product_user")->withTimestamps();
    }

    public function orders(){ 
        return $this->belongsToMany(PurchaseOrder::class, "purchase_order_items", "product_id");
    }

    public function getPurchasedAttribute(){
        $orders = $this->orders()->withPivot("quantity")->get();
        return $orders->sum("pivot.quantity");
    }
    
    public function getInStockAttribute(){
        $purchased = $this->purchased;
        return $this->quantity - $purchased;
    }
    // public function cart()
    // {
    //     return $this->belongsTo(Cart::class,'id','product_id');
    // }

    // public function orders()
    // {
    //     return $this->belongsTo(Order::class,'id');
    // }
}
