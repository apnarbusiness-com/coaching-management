<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherPaymentTransaction extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'teacher_payment_transactions';

    protected $dates = [
        'payment_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PAYMENT_METHOD_SELECT = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'mobile_banking' => 'Mobile Banking',
    ];

    protected $fillable = [
        'teachers_payment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'received_by',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function teachersPayment()
    {
        return $this->belongsTo(TeachersPayment::class, 'teachers_payment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
