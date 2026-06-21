<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EarningTransaction extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'earning_transactions';

    protected $dates = [
        'payment_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'receipt_no',
        'student_id',
        'total_amount',
        'payment_method',
        'cash_book_id',
        'payment_date',
        'total_items',
        'notes',
        'created_by_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function cashBook()
    {
        return $this->belongsTo(CashBook::class, 'cash_book_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class, 'earning_transaction_id');
    }
}
