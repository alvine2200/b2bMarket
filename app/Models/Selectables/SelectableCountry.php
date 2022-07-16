<?php

namespace App\Models\Selectables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectableCountry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'continent',
        'capital',
        'currency',
        'currency_to_usd_factor',
    ];

   
}
