<?php

namespace App\Http\Requests;

use App\Models\AcademicClass;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAcademicClassRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_class_edit');
    }

    public function rules()
    {
        return [
            'class_name' => [
                'string',
                'required',
            ],
            'academic_year' => [
                'date',
                'nullable',
            ],
            'class_code' => [
                'string',
                'nullable',
            ],
            'class_sections.*' => [
                'integer',
            ],
            'class_sections' => [
                'array',
            ],
            'class_shifts.*' => [
                'integer',
            ],
            'class_shifts' => [
                'array',
            ],
        ];
    }
}
