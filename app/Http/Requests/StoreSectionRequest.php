<?php

namespace App\Http\Requests;

use App\Models\Section;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSectionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('section_create');
    }

    public function rules()
    {
        return [
            'section_name' => [
                'string',
                'required',
            ],
            'section_code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
