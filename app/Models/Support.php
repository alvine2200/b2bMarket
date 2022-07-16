<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $table="supports";

    protected $fillable = [

        'subject',
        'description',
        'photo',
        'status',
        'ticket_number',
        'sending_date',
        'user_id',
        'last_reply',
        'reply_message',
        'image',
    ];


    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
