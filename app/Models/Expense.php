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

class Expense extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'expenses';

    protected $appends = [
        'payment_proof',
    ];

    protected $dates = [
        'expense_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'expense_category_id',
        'title',
        'details',
        'amount',
        'expense_date',
        'expense_month',
        'expense_year',
        'expense_reference',
        'payment_method',
        'payment_proof_details',
        'paid_by',
        'created_by_id',
        'updated_by_id',
        'teacher_id',
        'batch_id',
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

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getExpenseDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setExpenseDateAttribute($value)
    {
        $this->attributes['expense_date'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
