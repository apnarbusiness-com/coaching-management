<?php

namespace App\Http\Requests;

use App\Models\Shift;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreShiftRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shift_create');
    }

    public function rules()
    {
        return [
            'shift_name' => [
                'string',
                'required',
            ],
            'shift_code' => [
                'string',
                'nullable',
            ],
            'shift_time' => [
                'string',
                'nullable',
            ],
        ];
    }
}
