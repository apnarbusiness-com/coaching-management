<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBookTransaction extends Model
{
    use HasFactory;

    protected $table = 'cash_book_transactions';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'cash_book_id',
        'old_amount',
        'new_amount',
        'action_type',
        'note',
        'created_by_id',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function cashBook()
    {
        return $this->belongsTo(CashBook::class, 'cash_book_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}