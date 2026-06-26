<?php

namespace App\Http\Requests;

use App\Models\TeachersPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

class UpdateTeachersPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teachers_payment_edit');
    }

    public function rules()
    {
        $teachersPayment = $this->route('teachersPayment');

        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
            'year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2100',
            ],
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],
            'batch_id' => [
                'nullable',
                'integer',
                'exists:batches,id',
            ],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $teachersPayment = $this->route('teachersPayment');
            $newAmount = (float) $this->input('amount');
            $paidAmount = $teachersPayment ? (float) $teachersPayment->paid_amount : 0;

            if ($paidAmount > 0 && $newAmount < $paidAmount) {
                $validator->errors()->add('amount', "The amount cannot be less than the already paid amount (৳" . number_format($paidAmount, 2) . ").");
            }
        });
    }
}
