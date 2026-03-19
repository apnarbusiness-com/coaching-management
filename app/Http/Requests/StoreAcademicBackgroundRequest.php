<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicBackgroundRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_background_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'max:255',
                Rule::unique('academic_backgrounds', 'name')->whereNull('deleted_at'),
            ],
        ];
    }
}
