<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable=[

        'full_name',
        'business_name',
        'country',
        'phone',
        'email',
        'user_id',
        'invoice_id',
        'order_id',
        'order_status',
        'order_date',
        'shipping_method',
        'payment_method',
        'total',
        'referrence_number',

    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class,'invoice_id','id');
    }

    public function products()
    {
        return $this->hasMany(Product::class,'id');
    }
}
