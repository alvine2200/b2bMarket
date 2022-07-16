<?php

namespace App\Models\AdvancedFilter;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancedFilterRecentSearch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'country',
        'region',
        'size_start_range',
        'size_end_range',
        'age_start_range',
        'age_end_range',
        'updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
