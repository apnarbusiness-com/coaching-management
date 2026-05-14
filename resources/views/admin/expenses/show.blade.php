@extends('layouts.admin')
@section('title', 'Expenses — Details')
@section('content')
    <div class="mx-auto max-w-6xl px-6 py-8">
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <a class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 transition-colors"
                    href="{{ route('admin.expenses.index') }}">
                    <span class="material-icons-round text-lg">arrow_back</span>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ trans('global.show') }}
                    {{ trans('cruds.expense.title') }}</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Reference: <span
                    class="font-mono text-slate-700 dark:text-slate-300">{{ $expense->expense_reference ?? 'N/A' }}</span>
                • Created by: <span
                    class="text-slate-700 dark:text-slate-300">{{ $expense->created_by->name ?? 'System' }}</span>
                • Date: {{ $expense->created_at->format('M d, Y') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7 space-y-6">
                <!-- Expense Summary Card -->
                <div
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800/50">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Expense Summary</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.expense_category') }}</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        <span class="material-icons-round text-lg">category</span>
                                    </span>
                                    <span
                                        class="text-sm font-medium text-slate-900 dark:text-white">{{ $expense->expense_category->name ?? 'Uncategorized' }}</span>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.amount') }}</label>
                                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                                    {{ number_format($expense->amount, 2) }} <span
                                        class="text-xs font-normal text-slate-400">BDT</span>
                                </p>
                            </div>
                            @if($expense->teacher)
                                <div>
                                    <label
                                        class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.teacher') }}</label>
                                    <div class="mt-1 flex items-center gap-3">
                                        @if($expense->teacher->profile_img)
                                            <img src="{{ $expense->teacher->profile_img->getUrl('thumb') }}"
                                                class="h-8 w-8 rounded-full object-cover border border-slate-200 dark:border-slate-700"
                                                alt="{{ $expense->teacher->name }}">
                                        @else
                                            <div
                                                class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200 dark:border-slate-700">
                                                {{ substr($expense->teacher->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm font-medium text-slate-900 dark:text-white">{{ $expense->teacher->name }}</span>
                                    </div>
                                </div>
                            @endif
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.expense_date') }}</label>
                                <div class="mt-1 flex items-center gap-2 text-slate-700 dark:text-slate-300">
                                    <span class="material-icons-round text-lg text-slate-400">calendar_today</span>
                                    <span class="text-sm">{{ $expense->expense_date }}</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.title') }}</label>
                                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $expense->title }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ trans('cruds.expense.fields.details') }}</label>
                                <div
                                    class="mt-1 text-sm text-slate-600 dark:text-slate-400 leading-relaxed prose prose-slate dark:prose-invert max-w-none">
                                    {!! $expense->details !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info Card -->
                <div
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800/50">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Payment Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3">
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-2">{{ trans('cruds.expense.fields.payment_method') }}</label>
                                <div
                                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">
                                    <span class="material-icons-round text-lg text-primary">payments</span>
                                    <span
                                        class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $expense->payment_method ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-2">{{ trans('cruds.expense.fields.paid_by') }}</label>
                                <span class="text-sm text-slate-900 dark:text-white">{{ $expense->paid_by ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-2">Period</label>
                                <span class="text-sm text-slate-900 dark:text-white">{{ $expense->expense_month }},
                                    {{ $expense->expense_year }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Proofs & Metadata -->
            <div class="lg:col-span-5 space-y-6">
                <div class="sticky top-24 space-y-6">
                    <div
                        class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Proof of Payment</h2>
                                <span
                                    class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary dark:bg-primary/20">
                                    {{ count($expense->payment_proof) }}
                                    {{ count($expense->payment_proof) === 1 ? 'File' : 'Files' }}
                                </span>
                            </div>
                        </div>

                        @if(count($expense->payment_proof) > 0)
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($expense->payment_proof as $key => $media)
                                    <div
                                        class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-3 hover:border-primary/30 hover:bg-white dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-primary/50 dark:hover:bg-slate-800 transition-all">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm overflow-hidden dark:bg-slate-700">
                                            @php
                                                $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp

                                            @if($isImage)
                                                <img alt="{{ $media->name }}" class="h-full w-full object-cover"
                                                    src="{{ $media->getUrl('thumb') }}" />
                                            @else
                                                <span class="material-icons-round text-2xl text-slate-400">insert_drive_file</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-900 dark:text-white"
                                                title="{{ $media->file_name }}">{{ $media->file_name }}</p>
                                            <p class="text-[11px] text-slate-500">{{ round($media->size / 1024, 2) }} KB</p>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-md"
                                                title="View Full">
                                                <span class="material-icons-round text-xl">visibility</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center py-8 text-slate-400 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-xl">
                                <span class="material-icons-round text-4xl mb-2">file_off</span>
                                <p class="text-xs">No payment proof uploaded</p>
                            </div>
                        @endif

                        @if($expense->payment_proof_details)
                            <div class="mt-6">
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-2">{{ trans('cruds.expense.fields.payment_proof_details') }}</label>
                                <div
                                    class="text-sm text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                    {!! $expense->payment_proof_details !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Meta Info -->
                    <div
                        class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Metadata</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 flex items-center justify-center">
                                    <span class="material-icons-round text-lg">add_circle_outline</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-900 dark:text-white">Created by
                                        {{ $expense->created_by->name ?? 'System' }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase mt-0.5">
                                        {{ $expense->created_at->format('M d, Y • h:i A') }}</p>
                                </div>
                            </div>
                            @if($expense->updated_by)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 flex items-center justify-center">
                                        <span class="material-icons-round text-lg">edit_note</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-slate-900 dark:text-white">Last updated by
                                            {{ $expense->updated_by->name }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase mt-0.5">
                                            {{ $expense->updated_at->format('M d, Y • h:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection