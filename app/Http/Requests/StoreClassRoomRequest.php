<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('class_room_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'max:255',
                Rule::unique('class_rooms', 'name')->whereNull('deleted_at'),
            ],
        ];
    }
}
