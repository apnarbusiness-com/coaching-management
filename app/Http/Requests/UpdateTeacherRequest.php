<?php

namespace App\Http\Requests;

use App\Models\Teacher;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teacher_edit');
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
            'phone' => [
                'string',
                'required',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'joining_date' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'status' => [
                'required',
            ],
            'salary_amount' => [
                'nullable',
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
