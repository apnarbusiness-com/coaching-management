<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StudentBasicInfo extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    protected $appends = [
        'image',
    ];

    public $table = 'student_basic_infos';

    public const STATUS_SELECT = [
        '1' => 'Active',
        '0' => 'Postpone',
    ];

    public const GENDER_RADIO = [
        'male' => 'Male',
        'female' => 'Female',
        'others' => 'Others',
    ];

    protected $dates = [
        'dob',
        'joining_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'roll',
        'id_no',
        'first_name',
        'last_name',
        'gender',
        'contact_number',
        'email',
        'dob',
        'status',
        'monthly_discount',
        'joining_date',
        'class_id',
        'section_id',
        'shift_id',
        'academic_background_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function studentEarnings()
    {
        return $this->hasMany(Earning::class, 'student_id', 'id');
    }

    public function getDobAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? (Carbon::parse($value)->format('Y-m-d')) : null;
    }

    public function getJoiningDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setJoiningDateAttribute($value)
    {
        $this->attributes['joining_date'] = $value ? (Carbon::parse($value)->format('Y-m-d H:i:s')) : null;
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function class()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function academicBackground()
    {
        return $this->belongsTo(AcademicBackground::class, 'academic_background_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function studentDetails()
    {
        return $this->hasOne(StudentDetailsInformation::class, 'student_id', 'id');
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class)
            ->withPivot(['enrolled_at', 'per_student_discount', 'one_time_discount', 'custom_monthly_fee']);
    }

    public function flags()
    {
        return $this->belongsToMany(StudentFlag::class, 'student_flag_assignments')
            ->withPivot('comment', 'created_by_id')
            ->withTimestamps();
    }

}
