<?php

namespace App\Http\Requests;

use App\Models\Batch;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreBatchRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('batch_create');
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
        if ($this->has('class_schedule') && is_array($this->class_schedule)) {
            $schedule = [];
            foreach ($this->class_schedule as $key => $row) {
                if (isset($row['day']) && isset($row['time']) && $row['day'] && $row['time']) {
                    $schedule[$row['day']] = $row['time'];
                }
            }
            $this->merge(['class_schedule' => $schedule]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->has('class_schedule') || !is_array($this->class_schedule)) {
                return;
            }

            $validDays = array_keys(Batch::CLASS_DAY_SELECT);
            $schedule = $this->class_schedule;

            foreach ($schedule as $day => $time) {
                if (!in_array($day, $validDays)) {
                    $validator->errors()->add("class_schedule.{$day}", "The selected day {$day} is invalid.");
                    continue;
                }
                if (!$time || !preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time)) {
                    $validator->errors()->add("class_schedule.{$day}", "The time for {$day} must be in HH:MM format.");
                }
            }

            if (empty($schedule)) {
                $validator->errors()->add('class_schedule', 'At least one class schedule is required.');
            }
        });
    }
}
