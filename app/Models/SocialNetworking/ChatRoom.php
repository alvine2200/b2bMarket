<?php

namespace App\Models\SocialNetworking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ChatRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "conversation_id",
        "name",
        "created_by"
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }
}
