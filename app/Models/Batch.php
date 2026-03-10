<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'batches';

    public const FEE_TYPE_SELECT = [
        'course'  => 'Course (Fixed)',
        'monthly' => 'Monthly Fee',
    ];

    public const CLASS_DAY_SELECT = [
        'saturday'  => 'Saturday',
        'sunday'    => 'Sunday',
        'monday'    => 'Monday',
        'tuesday'   => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday'  => 'Thursday',
        'friday'    => 'Friday',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'class_days' => 'array',
    ];

    protected $fillable = [
        'batch_name',
        'subject_id',
        'class_id',
        'fee_type',
        'fee_amount',
        'duration_in_months',
        'class_days',
        'class_time',
        'capacity',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(StudentBasicInfo::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)
            ->withPivot(['salary_amount', 'role'])
            ->withTimestamps();
    }
}
