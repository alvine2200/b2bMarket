<?php

namespace App\Models\Profile;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "business_id"
    ];

    public function viewer(){
        return $this->belongsTo(User::class);
    }
    
    public function viewed(){
        return $this->belongsTo(Business::class);
    }
}
