<?php

namespace App\Http\Requests;

use App\Models\Batch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBatchRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('batch_edit');
    }

    public function rules()
    {
        return [
            'batch_name' => [
                'string',
                'required',
            ],
            'subjects' => [
                'required',
                'array',
                'min:1',
            ],
            'subjects.*' => [
                'integer',
                'exists:subjects,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:academic_classes,id',
            ],
            'fee_type' => [
                'required',
                Rule::in(array_keys(Batch::FEE_TYPE_SELECT)),
            ],
            'fee_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'duration_in_months' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:fee_type,course',
            ],
            'class_schedule' => [
                'required',
                'array',
                'min:1',
            ],
            'class_schedule.day' => [
                'required',
                'array',
                'min:1',
            ],
            'class_schedule.day.*' => [
                'required',
                Rule::in(array_keys(Batch::CLASS_DAY_SELECT)),
            ],
            'class_schedule.time' => [
                'required',
                'array',
            ],
            'class_schedule.time.*' => [
                'required',
                'date_format:H:i',
            ],
            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'students.*' => [
                'integer',
                'exists:student_basic_infos,id',
            ],
            'students' => [
                'array',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('class_schedule') && isset($this->class_schedule['day'])) {
            $schedule = [];
            foreach ($this->class_schedule['day'] as $index => $day) {
                if ($day && isset($this->class_schedule['time'][$index])) {
                    $schedule[$day] = $this->class_schedule['time'][$index];
                }
            }
            $this->merge(['class_schedule' => $schedule]);
        }
    }
}
