<?php

namespace App\Models\SocialNetworking;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "conversation_id",
        "sender_id",
        "body"
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function attachments(){
        return $this->hasMany(ChatMessageAttachment::class, 'chat_message_id', 'id');
    }

    public function sender(){
        return $this->belongsTo(User::class, "sender_id");
    }

    public function getRecipientsAttribute(){
        $is_direct = $this->conversation->is_direct;
        if($is_direct){
            $allUsers = $this->conversation->users();
        }
        else{
            $allUsers = $this->chatRoom->users();
        }
        $recipients = $allUsers->where("user_id", "!=",$this->sender_id)->get();

        return $recipients;
    }
}
