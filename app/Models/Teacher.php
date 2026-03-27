<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Teacher extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'teachers';

    protected $appends = [
        'profile_img',
    ];

    protected $dates = [
        'joining_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const SALARY_TYPE_SELECT = [
        'fixed' => 'fixed',
        'variable' => 'variable',
    ];

    public const SALARY_AMOUNT_TYPE_SELECT = [
        'fixed' => 'Fixed Amount',
        'percentage' => 'Percentage',
    ];

    public const GENDER_SELECT = [
        'male' => 'Male',
        'female' => 'Female',
        'others' => 'Others',
    ];

    protected $fillable = [
        'emloyee_code',
        'name',
        'phone',
        'email',
        'address',
        'user_id',
        'gender',
        'joining_date',
        'status',
        'salary_type',
        'salary_amount',
        'salary_amount_type',
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

    public function teacherExpenses()
    {
        return $this->hasMany(Expense::class, 'teacher_id', 'id');
    }

    public function teacherTeachersPayments()
    {
        return $this->hasMany(TeachersPayment::class, 'teacher_id', 'id');
    }

    public function getProfileImgAttribute()
    {
        $file = $this->getMedia('profile_img')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getJoiningDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setJoiningDateAttribute($value)
    {
        $this->attributes['joining_date'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class)
            ->withPivot(['salary_amount', 'salary_amount_type', 'role'])
            ->withTimestamps();
    }
}
