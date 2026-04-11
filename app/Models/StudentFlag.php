<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentFlag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(StudentBasicInfo::class, 'student_flag_assignments')
            ->withPivot('comment', 'created_by_id')
            ->withTimestamps();
    }
}
