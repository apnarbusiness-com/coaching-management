<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicBackgroundRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_background_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'max:255',
                Rule::unique('academic_backgrounds', 'name')
                    ->ignore($this->route('academic_background')?->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
