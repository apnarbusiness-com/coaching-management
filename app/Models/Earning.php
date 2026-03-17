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

class Earning extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'earnings';

    protected $appends = [
        'payment_proof',
    ];

    protected $dates = [
        'earning_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'earning_category_id',
        'student_id',
        'batch_id',
        'subject_id',
        'title',
        'academic_background',
        'exam_year',
        'details',
        'amount',
        'earning_date',
        'earning_month',
        'earning_year',
        'earning_reference',
        'payment_method',
        'payment_proof_details',
        'paid_by',
        'recieved_by',
        'created_by_id',
        'updated_by_id',
        'student_monthly_due_id',
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

    public function earning_category()
    {
        return $this->belongsTo(EarningCategory::class, 'earning_category_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function getEarningDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEarningDateAttribute($value)
    {
        $this->attributes['earning_date'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    public function getPaymentProofAttribute()
    {
        $files = $this->getMedia('payment_proof');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview = $item->getUrl('preview');
        });

        return $files;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
