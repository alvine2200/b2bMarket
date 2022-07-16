<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNews extends Model
{
    use HasFactory;

    protected $table='user_news';

    protected $fillable=[
        'user_id',
        'title',
        'subtitle',
        'body',
        'author',
        'image',
        'status',
    ];
}
