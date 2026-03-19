<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('expense_edit');
    }

    public function rules()
    {
        return [
            'expense_category_id' => [
                'required',
                'integer',
                'exists:expense_categories,id',
            ],
            'title' => [
                'string',
                'nullable',
            ],
            'amount' => [
                'required',
                'numeric',
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'teacher_id' => [
                'nullable',
                'integer',
                'exists:teachers,id',
            ],
            'expense_month' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'expense_year' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'expense_reference' => [
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
            'paid_by' => [
                'string',
                'nullable',
            ],
        ];
    }
}
