<?php

namespace App\Http\Requests;

use App\Models\EarningCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEarningCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('earning_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:earning_categories,id',
        ];
    }
}
