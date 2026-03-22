<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAdmissionApplication extends Model
{
    use HasFactory;

    public $table = 'student_admission_applications';

    protected $fillable = [
        'admission_date',
        'admission_id_no',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'contact_number',
        'email',
        'fathers_name',
        'mothers_name',
        'guardian_name',
        'guardian_relation',
        'guardian_contact_number',
        'guardian_email',
        'student_birth_no',
        'student_blood_group',
        'address',
        'village',
        'post_office',
        'school_name',
        'class_name',
        'class_roll',
        'batch_name',
        'subjects',
        'photo_path',
        'status',
        'student_id',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'subjects' => 'array',
        'admission_date' => 'date',
        'dob' => 'date',
        'approved_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
