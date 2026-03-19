<?php

namespace App\Http\Requests;

use App\Models\TeachersPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTeachersPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teachers_payment_edit');
    }

    public function rules()
    {
        return [
            'month' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'year' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
