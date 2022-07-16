<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paypal_Payment extends Model
{
    use HasFactory;

    protected $table='paypal_payments';

    protected $fillable = [
        'invoice_id', 
        'order_id',
        'order_date',
        'referrence_number',
        'order_status',
        'business_name',
        'paid_by',
        'paid_to',
    ];
}
