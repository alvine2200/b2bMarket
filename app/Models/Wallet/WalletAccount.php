<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "wallet_id",
        "name",
        "account_number",
        "description",
        "balance"
    ];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
