<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EarningCategory extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'earning_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'is_student_connected',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_student_connected' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function earningCategoryEarnings()
    {
        return $this->hasMany(Earning::class, 'earning_category_id', 'id');
    }
}
