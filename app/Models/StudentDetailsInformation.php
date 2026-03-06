<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDetailsInformation extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'student_details_informations';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'fathers_name',
        'mothers_name',
        'fathers_nid',
        'mothers_nid',
        'guardian_name',
        'guardian_relation',
        'guardian_contact_number',
        'guardian_email',
        'student_birth_no',
        'student_blood_group',
        'address',
        'student_id',
        'id_card_delivery_status',
        'reference',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(StudentBasicInfo::class, 'student_id');
    }
}
