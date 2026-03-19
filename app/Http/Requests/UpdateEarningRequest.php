<?php

namespace App\Http\Requests;

use App\Models\Earning;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEarningRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('earning_edit');
    }

    public function rules()
    {
        return [
            'earning_category_id' => [
                'required',
                'integer',
                'exists:earning_categories,id',
            ],
            'student_id' => [
                'nullable',
                'integer',
                'exists:student_basic_infos,id',
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id',
            ],
            'title' => [
                'string',
                'required',
            ],
            'details' => [
                'string',
                'nullable',
            ],
            'academic_background' => [
                'string',
                'nullable',
            ],
            'exam_year' => [
                'string',
                'nullable',
            ],
            'amount' => [
                'required',
                'numeric',
            ],
            'earning_date' => [
                'required',
                'date',
            ],
            'earning_month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
            'earning_year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2100',
            ],
            'earning_reference' => [
                'string',
                'nullable',
            ],
            'payment_method' => [
                'string',
                'nullable',
            ],
            'payment_proof' => [
                'array',
            ],
            'payment_proof_details' => [
                'string',
                'nullable',
            ],
            'paid_by' => [
                'string',
                'nullable',
            ],
            'recieved_by' => [
                'string',
                'nullable',
            ],
        ];
    }
}
