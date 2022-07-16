<?php

namespace App\Models\SocialNetworking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Conversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "is_direct"
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }
    
    public function chatMessages(){
        return $this->hasMany(ChatMessage::class);
    }
    
    public function archivedChatMessages(){
        $archivedAt = $this->archivedAt;
        return $this->hasMany(ChatMessage::class)->where("created_at","<", $archivedAt);
    }

    public function chatRoom(){
        return $this->hasOne(ChatRoom::class);
    }

    public function getTitleAttribute(){ 
        if($this->chatRoom()->exists()){
            return $this->chatRoom->name;
        }
        else{
            $authUserId = Auth::user()->id;
            $otherUser = $this->users()->where("user_id", "!=", $authUserId)->first();
            return $otherUser->fullName;
        }
    }

    public function getUnreadAttribute(){
        $authUserId = Auth::user()->id;
        if($this->is_direct){
            $user = $this->users()->where("user_id", $authUserId)->withPivot('last_read')->first();
        }
        else{
            $user = $this->chatRoom->users()->where("user_id", $authUserId)->withPivot('last_read')->first();
        }
        
        $lastRead = $user->pivot->last_read;
        if($lastRead == null){
            return $this->chatMessages()->where("sender_id","!=", $authUserId)->count();
        }

        // return $this->chatMessages()->where("created_at", ">", $lastRead)->count();
        return $this->chatMessages()->where("sender_id","!=", $authUserId)->where("created_at", ">", $lastRead)->count();
    }

    public function getClearedAtAttribute(){
        $user = User::find(Auth::user()->id);
        // dd($this->users()->firstWhere("user_id", $user->id)->pivot->cleared_at);
        if($this->is_direct){
            $cleared_at = $this->users()
                                ->wherePivot("user_id", $user->id)
                                ->withPivot("cleared_at")
                                ->first()->pivot->cleared_at;
        }
        else{
            $cleared_at = $this->chatRoom->users()
                                            ->wherePivot("user_id", $user->id)
                                            ->withPivot("cleared_at")
                                            ->first()->pivot->cleared_at;
        }
        
        return $cleared_at;
    }
    
    public function getArchivedAtAttribute(){
        $user = User::find(Auth::user()->id);
        // dd($this->users()->firstWhere("user_id", $user->id)->pivot->cleared_at);
        if($this->is_direct){
            $archived_at = $this->users()
                                ->wherePivot("user_id", $user->id)
                                ->withPivot("archived_at")
                                ->first()->pivot->archived_at;
        }
        else{
            $archived_at = $this->chatRoom->users()
                                            ->wherePivot("user_id", $user->id)
                                            ->withPivot("archived_at")
                                            ->first()->pivot->archived_at;
        }
        
        return $archived_at;
    }

    public function getBlockedByAttribute(){
        $user = User::find(Auth::user()->id);
        // dd($this->users()->firstWhere("user_id", $user->id)->pivot->cleared_at);
        if($this->is_direct){
            $blocked_by = $this->users()
                                ->wherePivot("user_id", $user->id)
                                ->withPivot("blocked_by")
                                ->first()->pivot->blocked_by;
        }
        else{
            $blocked_by = $this->chatRoom->users()
                                            ->wherePivot("user_id", $user->id)
                                            ->withPivot("blocked_by")
                                            ->first()->pivot->blocked_by;
        }
        
        return $blocked_by;
    }
    
    public function getDidBlockAttribute(){
        $user = User::find(Auth::user()->id);
        // dd($this->users()->firstWhere("user_id", $user->id)->pivot->cleared_at);
        if($this->is_direct){
            return $this->users()->where("conversation_user.blocked_by", $user->id)->exists();
        }
        
        return false;
    }

    public function getLastMessageBodyAttribute(){
        $lastMessage = $this->chatMessages()->latest()->first();
        if($lastMessage == null){return "";}

        // yield $this->id;
        $clearedAt = $this->clearedAt;
        if($clearedAt == null){
            return $lastMessage->body;
        }
        // if($this->id == 3){
        //     dd($cleared_at);
        // }
        if($lastMessage->created_at < $clearedAt){
            return "";
        }

        return $lastMessage->body;
    }
    
    public function getArchivedLastMessageBodyAttribute(){
        $lastMessage = $this->archivedChatMessages()->latest()->first();
        if($lastMessage == null){return "";}

        return $lastMessage->body;
    }

    public function getLastActivityAttribute(){
        $lastMessage = $this->chatMessages()->latest()->first();
        if($lastMessage == null){return $this->updated_at;}

        return $lastMessage->updated_at;
    }

    public function getIsDeletedAttribute(){
        $authUserId = Auth::user()->id;
        if(!$this->is_direct){
            $users = $this->chatRoom->users();
        }
        else{
            $users = $this->users();
        }

        $deleted_at = $users->withPivot("deleted_at")->firstWhere("user_id", $authUserId)->pivot->deleted_at;

        if($deleted_at == null){
            return false;
        }

        return $this->lastActivity < $deleted_at;
    }
    
    public function getIsArchivedAttribute(){
        $archived_at = $this->archived_at;

        if($archived_at == null){
            return false;
        }

        return $this->lastActivity < $archived_at;
    }
    
    public function getIsBlockedAttribute(){
        $blocked_by = $this->blocked_by;

        if($blocked_by == null){
            return false;
        }

        return true;
    }

    protected function getRecipientAttribute(){
        if(!$this->is_direct){
            return null;
        }
        $authUserId = Auth::user()->id;
        $recipient = $this->users()->firstWhere("user_id", "!=", $authUserId);
        return $recipient;
    }

    protected function getRecipientIdAttribute(){
        $recipient = $this->recipient;
        if($recipient==null){
            return null;
        }
        return $recipient->id;
    }

    protected function getBusinessAttribute()
    {
        $recipient = $this->recipient;
        if($recipient==null){
            return null;
        }
        return $recipient->business;
    }
    
    protected function getBusinessNameAttribute()
    {
        $business = $this->business;
        if($business == null){
            return null;
        }
        return $business->name;
    }
    
    protected function getBusinessSlugAttribute()
    {
        $business = $this->business;
        if($business == null){
            return null;
        }
        return $business->slug;
    }
    
    protected function getBusinessLogoAttribute()
    {
        $business = $this->business;
        if($business == null){
            return null;
        }
        return !empty($business->logo) ? url(Storage::url($business->logo)) : null;
    }
}
