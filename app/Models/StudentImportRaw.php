<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentImportRaw extends Model
{
    use HasFactory;

    public $table = 'student_import_raws';

    protected $fillable = [
        'source_file',
        'sheet_name',
        'row_index',
        'row_data',
        'is_processed',
        'processed_at',
        'processed_status',
        'processed_note',
    ];

    protected $casts = [
        'row_data' => 'array',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];
}
