<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('class_room_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'max:255',
                Rule::unique('class_rooms', 'name')
                    ->ignore($this->route('class_room')?->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
