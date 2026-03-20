<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchAttendance extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'batch_attendances';

    public const STATUS_SELECT = [
        'present' => 'Present',
        'absent'  => 'Absent',
        'late'    => 'Late',
    ];

    protected $dates = [
        'attendance_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'batch_id',
        'student_id',
        'attendance_date',
        'status',
        'remarks',
        'recorded_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }
}
