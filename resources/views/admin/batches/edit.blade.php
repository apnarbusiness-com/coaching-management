@extends('layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-4xl mx-auto flex flex-col gap-6 pb-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Batch</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Update batch configuration and assigned students.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.batches.update', [$batch->id]) }}" enctype="multipart/form-data" novalidate
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @method('PUT')
                @csrf

                <div class="p-6 md:p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="batch_name">{{ trans('cruds.batch.fields.batch_name') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('batch_name') ? 'border-red-500 ring-red-500' : '' }}"
                                id="batch_name" name="batch_name" type="text"
                                value="{{ old('batch_name', $batch->batch_name) }}" required />
                            @if ($errors->has('batch_name'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('batch_name') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="subjects">{{ trans('cruds.batch.fields.subject') }}</label>
                            <select
                                class="form-control select2 mt-1 block w-full {{ $errors->has('subjects') ? 'is-invalid' : '' }}"
                                name="subjects[]" id="subjects" multiple required>
                                @php
                                    $selectedSubjects = old('subjects', $batch->subjects->pluck('id')->toArray());
                                    if (empty($selectedSubjects) && $batch->subject_id) {
                                        $selectedSubjects = [$batch->subject_id];
                                    }
                                @endphp
                                @foreach ($subjects as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, $selectedSubjects, false) ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('subjects'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('subjects') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="class_id">{{ trans('cruds.batch.fields.class') }}</label>
                            <select
                                class="form-control select2 mt-1 block w-full {{ $errors->has('class_id') ? 'is-invalid' : '' }}"
                                name="class_id" id="class_id" required>
                                @foreach ($classes as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (string) old('class_id', $batch->class_id) === (string) $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('class_id'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_id') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="fee_type">{{ trans('cruds.batch.fields.fee_type') }}</label>
                            <select
                                class="form-control select2 mt-1 block w-full {{ $errors->has('fee_type') ? 'is-invalid' : '' }}"
                                name="fee_type" id="fee_type" required>
                                <option value="" disabled {{ old('fee_type', $batch->fee_type) ? '' : 'selected' }}>
                                    {{ trans('global.pleaseSelect') }}</option>
                                @foreach (\App\Models\Batch::FEE_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('fee_type', $batch->fee_type) === $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('fee_type'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('fee_type') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="fee_amount">{{ trans('cruds.batch.fields.fee_amount') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('fee_amount') ? 'border-red-500 ring-red-500' : '' }}"
                                id="fee_amount" name="fee_amount" type="number" min="0" step="0.01"
                                value="{{ old('fee_amount', $batch->fee_amount) }}" required />
                            @if ($errors->has('fee_amount'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('fee_amount') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1" id="duration-wrapper">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="duration_in_months">{{ trans('cruds.batch.fields.duration_in_months') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('duration_in_months') ? 'border-red-500 ring-red-500' : '' }}"
                                id="duration_in_months" name="duration_in_months" type="number" min="1"
                                value="{{ old('duration_in_months', $batch->duration_in_months) }}" />
                            @if ($errors->has('duration_in_months'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('duration_in_months') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="class_time">{{ trans('cruds.batch.fields.class_time') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('class_time') ? 'border-red-500 ring-red-500' : '' }}"
                                id="class_time" name="class_time" type="time"
                                value="{{ old('class_time', $batch->class_time ? \Carbon\Carbon::parse($batch->class_time)->format('H:i') : '') }}"
                                required />
                            @if ($errors->has('class_time'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_time') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="capacity">Capacity</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('capacity') ? 'border-red-500 ring-red-500' : '' }}"
                                id="capacity" name="capacity" type="number" min="1"
                                value="{{ old('capacity', $batch->capacity) }}" placeholder="Leave empty for no limit" />
                            @if ($errors->has('capacity'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('capacity') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="class_days">{{ trans('cruds.batch.fields.class_days') }}</label>
                            <select
                                class="form-control select2 mt-1 block w-full {{ $errors->has('class_days') ? 'is-invalid' : '' }}"
                                name="class_days[]" id="class_days" multiple required>
                                @foreach (\App\Models\Batch::CLASS_DAY_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ in_array($key, old('class_days', $batch->class_days ?? []), true) ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('class_days'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_days') }}</p>
                            @endif
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="students">{{ trans('cruds.batch.fields.students') }}</label>
                            <select
                                class="form-control select2 mt-1 block w-full {{ $errors->has('students') ? 'is-invalid' : '' }}"
                                name="students[]" id="students" multiple>
                                @foreach ($students as $id => $label)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('students', $batch->students->pluck('id')->toArray()), false) ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('students'))
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('students') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Update Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            const $feeType = $('#fee_type');
            const $durationWrapper = $('#duration-wrapper');
            const $durationInput = $('#duration_in_months');

            function toggleDuration() {
                if ($feeType.val() === 'course') {
                    $durationWrapper.show();
                    $durationInput.prop('required', true);
                } else {
                    $durationWrapper.hide();
                    $durationInput.prop('required', false).val('');
                }
            }

            toggleDuration();
            $feeType.on('change', toggleDuration);
        });
    </script>
@endsection
