<?php

namespace App\Http\Requests;

use App\Models\StudentBasicInfo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStudentBasicInfoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('student_basic_info_edit');
    }

    public function rules()
    {
        return [
            'roll' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'id_no' => [
                'string',
                'nullable',
            ],
            'first_name' => [
                'string',
                'required',
            ],
            'last_name' => [
                'nullable',
                'string',
            ],
            'gender' => [
                'required',
            ],
            'contact_number' => [
                'string',
                'required',
            ],
            'email' => [
                'string',
                'nullable',
            ],
            'dob' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'joining_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'academic_background_id' => [
                'nullable',
                'integer',
                'exists:academic_backgrounds,id',
            ],
            'guardian_name' => [
                'string',
                'nullable',
                // 'required',
            ],
            'guardian_contact_number' => [
                'string',
                'required',
            ],
            'guardian_relation_type' => [
                'string',
                'required',
            ],
            'guardian_relation_other' => [
                'string',
                'nullable',
            ],
            'guardian_email' => [
                'email',
                'nullable',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'student_blood_group' => [
                'string',
                'nullable',
            ],
            'subjects.*' => [
                'integer',
            ],
            'subjects' => [
                'array',
            ],
            'batches.*' => [
                'integer',
                'exists:batches,id',
            ],
            'batches' => [
                'array',
            ],
            'monthly_discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }
}
