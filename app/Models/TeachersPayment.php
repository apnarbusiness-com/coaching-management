<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachersPayment extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'teachers_payments';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PAYMENT_STATUS_SELECT = [
        'due' => 'Due',
        'partial' => 'Partial',
        'pending' => 'Pending',
        'processing' => 'Processing',
        'paid' => 'Paid',
    ];

    protected $fillable = [
        'teacher_id',
        'batch_id',
        'payment_details',
        'amount',
        'month',
        'year',
        'payment_status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function transactions()
    {
        return $this->hasMany(TeacherPaymentTransaction::class, 'teachers_payment_id');
    }

    public function getCalculatedAmountAttribute()
    {
        if ($this->amount !== null) {
            return (float) $this->amount;
        }

        $details = is_string($this->payment_details)
            ? json_decode($this->payment_details, true)
            : $this->payment_details;

        return $details['calculated_amount'] ?? 0;
    }

    public function getPaidAmountAttribute()
    {
        return (float) $this->transactions()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->calculated_amount - $this->paid_amount;
    }

    public function getPaymentStatusAttribute()
    {
        $totalAmount = $this->calculated_amount;
        $paidAmount = $this->paid_amount;

        if ($paidAmount >= $totalAmount && $totalAmount > 0) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        }

        return $this->attributes['payment_status'] ?? 'due';
    }

    public function getMonthYearNameAttribute()
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        return ($months[$this->month] ?? '').' '.$this->year;
    }

    public function scopePaid($query)
    {
        return $query;
    }

    public function updatePaymentStatus()
    {
        $totalAmount = $this->calculated_amount;
        $paidAmount = $this->paid_amount;

        if ($paidAmount >= $totalAmount && $totalAmount > 0) {
            $this->update(['payment_status' => 'paid']);
        } elseif ($paidAmount > 0) {
            $this->update(['payment_status' => 'partial']);
        } else {
            $this->update(['payment_status' => 'due']);
        }
    }
}
