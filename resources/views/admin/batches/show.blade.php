@extends('layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $batch->batch_name }}</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">Batch Overview</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.batches.edit', $batch->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit Batch
                    </a>
                    <a href="{{ route('admin.batches.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">info</span>
                            <h3 class="text-lg font-bold">Details</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-5">
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Subject</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @php
                                        $subjectNames = $batch->subjects->pluck('name')->filter()->unique();
                                        if ($subjectNames->isEmpty() && $batch->subject) {
                                            $subjectNames = collect([$batch->subject->name]);
                                        }
                                    @endphp
                                    @forelse ($subjectNames as $subjectName)
                                        <span class="badge badge-info">{{ $subjectName }}</span>
                                    @empty
                                        <p class="text-lg font-semibold">-</p>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Class</label>
                                <p class="mt-1 text-lg font-semibold">{{ $batch->class->class_name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Fee Type</label>
                                <p class="mt-1 text-lg font-semibold">{{ \App\Models\Batch::FEE_TYPE_SELECT[$batch->fee_type] ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Fee Amount</label>
                                <p class="mt-1 text-lg font-semibold">{{ number_format((float) $batch->fee_amount, 2) }}</p>
                            </div>
                            @if ($batch->fee_type === 'course')
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Duration</label>
                                    <p class="mt-1 text-lg font-semibold">{{ $batch->duration_in_months }} months</p>
                                </div>
                            @endif
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Class Schedule</label>
                                <div class="mt-2 flex flex-col gap-2">
                                    @php
                                        $schedule = $batch->class_schedule ?? [];
                                        $dayOrder = \App\Models\Batch::DAY_ORDER;
                                        $hasSchedule = false;
                                    @endphp
                                    @foreach($dayOrder as $day)
                                        @if(isset($schedule[$day]))
                                            @php $hasSchedule = true; @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="badge badge-info">{{ \App\Models\Batch::CLASS_DAY_SELECT[$day] ?? $day }}</span>
                                                <span class="text-slate-600 dark:text-slate-400">-</span>
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($schedule[$day])->format('h:i A') }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(!$hasSchedule)
                                        <p class="text-slate-500">No schedule set</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">group</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Assigned Students</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @includeIf('admin.batches.relationships.batchStudentBasicInfos', ['studentBasicInfos' => $batch->students])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
