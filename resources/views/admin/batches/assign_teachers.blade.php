@extends('layouts.admin')
@section('content')
    <div class="max-w-4xl mx-auto p-6 flex flex-col gap-6">
        @php
            $subjectNames = $batch->subjects->pluck('name')->filter()->unique();
            if ($subjectNames->isEmpty() && $batch->subject) {
                $subjectNames = collect([$batch->subject->name]);
            }
            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            $currentYear = now()->year;
            $years = range($currentYear - 1, $currentYear + 1);
        @endphp

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">Assign Teacher</h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Add a teacher to <span class="text-primary font-medium">{{ $batch->batch_name }}</span>
                    ({{ $batch->class->class_name ?? 'N/A' }} - {{ $subjectNames->implode(', ') ?: 'N/A' }}).
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year]) }}"
                    class="px-4 py-2 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors">
                    Back to Manage
                </a>
            </div>
        </div>

        <!-- Month/Year Selector -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('admin.batches.assignTeachers', $batch->id) }}" class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Month</label>
                    <select name="month" class="px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Year</label>
                    <select name="year" class="px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary">
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                    Change Month
                </button>
            </form>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                Currently viewing: <span class="font-medium text-primary">{{ $months[$month] }} {{ $year }}</span>
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Assignment Form</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Assign or update a teacher for <span class="font-medium text-primary">{{ $months[$month] }} {{ $year }}</span>
                </p>
            </div>
            <form method="POST" action="{{ route('admin.batches.assignTeachers.store', $batch->id) }}" class="px-6 py-6 space-y-6">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">person_search</span>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Teacher Selection</h3>
                    </div>
                    <div class="relative">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 ml-1">Select Teacher</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <select name="teacher_id"
                                class="custom-select-arrow w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none"
                                required>
                                <option disabled selected value="">Search by name</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->emloyee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-200 dark:bg-slate-700"></div>

                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">assignment_ind</span>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Assignment Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Salary Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">$</span>
                                <input name="salary_amount" required
                                    class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="0.00" type="number" step="0.01" min="0" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Salary Amount Type</label>
                            <select name="salary_amount_type"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Role</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input checked class="peer hidden" name="role" type="radio" value="primary" />
                                    <div class="flex items-center justify-center py-3 px-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all">
                                        Primary
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input class="peer hidden" name="role" type="radio" value="assistant" />
                                    <div class="flex items-center justify-center py-3 px-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all">
                                        Assistant
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year]) }}"
                        class="px-6 py-2.5 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Cancel
                    </a>
                    <button
                        class="px-8 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        Save Assignment
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    Assigned Teachers for {{ $months[$month] }} {{ $year }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="px-6 py-3">Teacher</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Salary</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($assignedTeachers as $assignment)
                            @php
                                $teacher = $teachers->find($assignment->teacher_id);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                                    {{ $teacher->name ?? 'Unknown' }}
                                    <div class="text-xs text-slate-500">{{ $teacher->emloyee_code ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200">{{ ucfirst($assignment->role ?? 'primary') }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200">
                                    {{ number_format((float) $assignment->salary_amount, 2) }}
                                    @if($assignment->salary_amount_type === 'percentage')
                                        <span class="text-xs text-slate-500">({{ $assignment->salary_amount }}%)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200">
                                    {{ $assignment->salary_amount_type === 'percentage' ? 'Percentage' : 'Fixed' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.batches.assignTeachers.remove', [$batch->id, $assignment->teacher_id]) }}" onsubmit="return confirm('Remove this teacher from the batch?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-500" colspan="5">
                                    No teachers assigned for {{ $months[$month] }} {{ $year }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script></script>
@endsection
