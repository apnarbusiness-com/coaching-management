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

    public const DAY_ORDER = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'class_schedule' => 'array',
    ];

    protected $fillable = [
        'batch_name',
        'subject_id',
        'class_id',
        'fee_type',
        'fee_amount',
        'duration_in_months',
        'class_schedule',
        'capacity',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getClassDaysAttribute()
    {
        if (isset($this->attributes['class_schedule'])) {
            $schedule = is_array($this->attributes['class_schedule']) 
                ? $this->attributes['class_schedule'] 
                : json_decode($this->attributes['class_schedule'], true);
            return array_keys($schedule ?? []);
        }
        return [];
    }

    public function getFormattedScheduleAttribute()
    {
        $schedule = $this->class_schedule ?? [];
        $formatted = [];
        foreach (self::DAY_ORDER as $day) {
            if (isset($schedule[$day])) {
                $entry = $schedule[$day];
                $timeValue = is_array($entry) ? ($entry['time'] ?? null) : $entry;
                $roomId = is_array($entry) ? ($entry['class_room_id'] ?? null) : null;
                if (!$timeValue) {
                    continue;
                }
                $formatted[$day] = [
                    'day' => self::CLASS_DAY_SELECT[$day],
                    'time' => \Carbon\Carbon::parse($timeValue)->format('h:i A'),
                    'class_room_id' => $roomId,
                ];
            }
        }
        return $formatted;
    }

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
        return $this->belongsToMany(StudentBasicInfo::class)
            ->withPivot(['enrolled_at', 'per_student_discount', 'one_time_discount', 'custom_monthly_fee']);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)
            ->withPivot(['salary_amount', 'role', 'salary_amount_type'])
            ->withTimestamps();
    }
}
