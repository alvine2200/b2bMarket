<?php

namespace App\Models\SocialNetworking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageAttachment extends Model
{
    use HasFactory;

    protected $table = 'chat_message_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "attachment",
    ];
}
