<?php

namespace App\Models\ProductsServices;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // 'service_id',
        //'service_qty',

    ];

   /* public function service()
    {
        return $this->hasMany(Service::class,'service_id','id');
    }
    */

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, "cart_product")->withPivot("quantity")->withTimestamps();
    }
}
