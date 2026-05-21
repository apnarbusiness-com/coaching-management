<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_books';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'amount',
        'image',
        'icon',
        'note',
        'status',
        'is_financial_account',
        'is_default',
        'order',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_financial_account' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function transactions()
    {
        return $this->hasMany(CashBookTransaction::class, 'cash_book_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}