<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMonthlyDue extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'student_monthly_dues';

    protected $dates = [
        'due_date',
        'paid_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'student_id',
        'batch_id',
        'academic_class_id',
        'section_id',
        'shift_id',
        'month',
        'year',
        'due_amount',
        'paid_amount',
        'discount_amount',
        'due_remaining',
        'status',
        'due_date',
        'paid_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function academicClass()
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class, 'student_monthly_due_id');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'free']);
    }

    public function scopeFree($query)
    {
        return $query->where('status', 'free');
    }

    public function scopeForMonth($query, $month, $year)
    {
        $query->where('year', $year);
        if ($month !== 'all') {
            $query->where('month', $month);
        }
        return $query;
    }

    public function getDueStatusAttribute()
    {
        $displayStatus = ($this->due_amount == 0 && $this->paid_amount == 0) ? 'free' : $this->status;

        return match ($displayStatus) {
            'paid' => '<span class="badge bg-success">Paid</span>',
            'free' => '<span class="badge bg-warning">Free</span>',
            'partial' => '<span class="badge bg-warning">Partial</span>',
            default => '<span class="badge bg-danger">Unpaid</span>',
        };
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::createFromDate(null, $this->month, 1)->format('F');
    }
}
