<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    public $table = 'wallet_transactions';

    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
