<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOtherDue extends Model
{
    use HasFactory;

    public $table = 'student_other_dues';

    protected $fillable = [
        'student_id',
        'earning_category_id',
        'batch_id',
        'subject_id',
        'title',
        'amount',
        'paid_amount',
        'academic_background',
        'exam_year',
        'details',
        'due_date',
        'payment_method',
        'payment_proof_details',
        'paid_by',
        'recieved_by',
        'status',
        'earning_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function earningCategory()
    {
        return $this->belongsTo(EarningCategory::class, 'earning_category_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function earning()
    {
        return $this->belongsTo(Earning::class, 'earning_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
