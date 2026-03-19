<?php

namespace App\Http\Requests;

use App\Models\StudentDetailsInformation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStudentDetailsInformationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('student_details_information_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:student_details_informations,id',
        ];
    }
}
