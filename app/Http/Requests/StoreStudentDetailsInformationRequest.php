<?php

namespace App\Http\Requests;

use App\Models\StudentDetailsInformation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStudentDetailsInformationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('student_details_information_create');
    }

    public function rules()
    {
        return [
            'fathers_name' => [
                'string',
                'required',
            ],
            'mothers_name' => [
                'string',
                'nullable',
            ],
            'fathers_nid' => [
                'string',
                'nullable',
            ],
            'mothers_nid' => [
                'string',
                'nullable',
            ],
            'guardian_name' => [
                'string',
                'required',
            ],
            'guardian_relation' => [
                'string',
                'nullable',
            ],
            'guardian_contact_number' => [
                'string',
                'min:11',
                'max:15',
                'required',
            ],
            'student_birth_no' => [
                'string',
                'nullable',
            ],
            'student_blood_group' => [
                'string',
                'nullable',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'student_id' => [
                'required',
                'integer',
            ],
            'id_card_delivery_status' => [
                'string',
                'nullable',
            ],
        ];
    }
}
