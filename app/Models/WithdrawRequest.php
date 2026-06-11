<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    public $table = 'withdraw_requests';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'phone',
        'payment_method',
        'account_number',
        'admin_notes',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
