<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    public $table = 'wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_withdrawn',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'wallet_id');
    }
}
