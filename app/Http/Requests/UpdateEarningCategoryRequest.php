<?php

namespace App\Http\Requests;

use App\Models\EarningCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEarningCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('earning_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
            'type' => [
                'string',
                'nullable',
            ],
            'is_student_connected' => [
                'boolean',
            ],
        ];
    }
}
