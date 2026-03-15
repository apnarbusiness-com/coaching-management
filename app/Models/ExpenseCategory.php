<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'expense_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'is_teacher_connected',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_teacher_connected' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function expenseCategoryExpenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id', 'id');
    }
}
