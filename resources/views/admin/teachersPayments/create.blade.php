@extends('layouts.admin')
@section('title', 'Teachers Payments — Create')
@section('content')

    <!-- Page Scroll Container -->
    <div
        class="flex-1 overflow-y-auto p-6 lg:px-10 lg:py-8 bg-background-light dark:bg-background-dark transition-colors duration-200">
        <div class="max-w-[1024px] mx-auto flex flex-col gap-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-sm">
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.home') }}">Dashboard</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.teachers-payments.index') }}">Teachers Payments</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <span class="text-text-main dark:text-white font-medium">Record Payment</span>
            </nav>

            <!-- Page Heading -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold text-text-main dark:text-white tracking-tight">
                    {{ trans('global.create') }} {{ trans('cruds.teachersPayment.title_singular') }}
                </h1>
                <p class="text-text-secondary dark:text-gray-400 max-w-2xl">
                    Add a new payment record for faculty members. Ensure the month and year are correctly specified.
                </p>
            </div>

            <!-- Main Form Card -->
            <form action="{{ route('admin.teachers-payments.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                @csrf

                <!-- Section: Payment Details -->
                <div class="p-6 lg:p-8">
                    <h2 class="text-xl font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">payments</span>
                        Payment Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Teacher Selection -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5"
                                for="teacher_id">
                                {{ trans('cruds.teachersPayment.fields.teacher') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                class="form-control select2 w-full {{ $errors->has('teacher') ? 'ring-2 ring-red-500' : '' }}"
                                name="teacher_id" id="teacher_id" required>
                                @foreach($teachers as $id => $entry)
                                    <option value="{{ $id }}" {{ old('teacher_id') == $id ? 'selected' : '' }}>{{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('teacher'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('teacher') }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-text-secondary dark:text-gray-400">
                                {{ trans('cruds.teachersPayment.fields.teacher_helper') }}</p>
                        </div>

                        <!-- Month -->
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5" for="month">
                                {{ trans('cruds.teachersPayment.fields.month') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary material-symbols-outlined !text-[20px]">calendar_month</span>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 pl-10 pr-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('month') ? 'ring-2 ring-red-500' : '' }}"
                                    type="number" name="month" id="month" value="{{ old('month', date('n')) }}" step="1"
                                    min="1" max="12" required>
                            </div>
                            @if($errors->has('month'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('month') }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-text-secondary dark:text-gray-400">
                                {{ trans('cruds.teachersPayment.fields.month_helper') }}</p>
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5" for="year">
                                {{ trans('cruds.teachersPayment.fields.year') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary material-symbols-outlined !text-[20px]">event</span>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 pl-10 pr-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('year') ? 'ring-2 ring-red-500' : '' }}"
                                    type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" step="1"
                                    required>
                            </div>
                            @if($errors->has('year'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('year') }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-text-secondary dark:text-gray-400">
                                {{ trans('cruds.teachersPayment.fields.year_helper') }}</p>
                        </div>

                        <!-- Payment Status -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">
                                {{ trans('cruds.teachersPayment.fields.payment_status') }} <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach(App\Models\TeachersPayment::PAYMENT_STATUS_SELECT as $key => $label)
                                    <label
                                        class="relative flex items-center justify-center p-3 rounded-lg border border-border-light dark:border-border-dark bg-background-light/50 dark:bg-black/20 cursor-pointer hover:border-primary transition-all group has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                        <input type="radio" name="payment_status" value="{{ $key }}" class="sr-only" {{ old('payment_status', 'due') === (string) $key ? 'checked' : '' }}>
                                        <div class="flex flex-col items-center gap-1 text-center">
                                            <span
                                                class="text-sm font-medium text-text-main dark:text-white group-has-[:checked]:text-primary">{{ $label }}</span>
                                            @if($key === 'paid')
                                                <span
                                                    class="material-symbols-outlined text-[18px] text-green-500">check_circle</span>
                                            @elseif($key === 'due')
                                                <span class="material-symbols-outlined text-[18px] text-yellow-500">pending</span>
                                            @else
                                                <span class="material-symbols-outlined text-[18px] text-red-500">cancel</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @if($errors->has('payment_status'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('payment_status') }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-text-secondary dark:text-gray-400">
                                {{ trans('cruds.teachersPayment.fields.payment_status_helper') }}</p>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5"
                                for="payment_details">
                                {{ trans('cruds.teachersPayment.fields.payment_details') }}
                            </label>
                            <textarea
                                class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary resize-none {{ $errors->has('payment_details') ? 'ring-2 ring-red-500' : '' }}"
                                name="payment_details" id="payment_details"
                                placeholder="Enter transaction ID, notes, or payment method..."
                                rows="4">{{ old('payment_details') }}</textarea>
                            @if($errors->has('payment_details'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('payment_details') }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-text-secondary dark:text-gray-400">
                                {{ trans('cruds.teachersPayment.fields.payment_details_helper') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div
                    class="p-6 lg:p-8 bg-background-light/50 dark:bg-black/20 border-t border-border-light dark:border-border-dark flex items-center justify-end gap-4">
                    <a href="{{ route('admin.teachers-payments.index') }}"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium text-text-secondary dark:text-gray-300 hover:text-text-main hover:bg-white dark:hover:bg-white/5 transition-colors">
                        Cancel
                    </a>
                    <button
                        class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all transform active:scale-95 flex items-center gap-2"
                        type="submit">
                        <span class="material-symbols-outlined !text-[20px]">save</span>
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#teacher_id').select2({
                placeholder: 'Select a teacher',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection