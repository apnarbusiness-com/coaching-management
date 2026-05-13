<?php

namespace App\Http\Requests;

use App\Models\Teacher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTeacherRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teacher_create');
    }

    public function rules()
    {
        return [
            'emloyee_code' => [
                'string',
                'required',
            ],
            'name' => [
                'string',
                'required',
            ],
            'father_name' => [
                'string',
                'nullable',
            ],
            'mother_name' => [
                'string',
                'nullable',
            ],
            'dob' => [
                'date',
                'nullable',
            ],
            'phone' => [
                'string',
                'required',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'joining_date' => [
                'date',
                'nullable',
            ],
            'qualifications' => [
                'nullable',
                'array',
            ],
            'qualifications.*.university' => [
                'string',
                'required',
            ],
            'qualifications.*.department' => [
                'string',
                'required',
            ],
            'qualifications.*.session' => [
                'string',
                'required',
            ],
            'salary_type' => [
                'required',
                'string',
                'in:monthly_fixed,batch_wise',
            ],
            'salary_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:salary_type,monthly_fixed',
            ],
            'subjects.*' => [
                'integer',
            ],
            'subjects' => [
                'array',
            ],
        ];
    }
}
