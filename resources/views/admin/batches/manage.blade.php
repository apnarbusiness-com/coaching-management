@extends('layouts.admin')
@section('content')
    <div class="max-w-6xl mx-auto p-6 md:p-10">
        @php
            $subjectNames = $batch->subjects->pluck('name')->filter()->unique();
            if ($subjectNames->isEmpty() && $batch->subject) {
                $subjectNames = collect([$batch->subject->name]);
            }
            $schedule = $batch->formatted_schedule;
            $capacity = $batch->capacity;
            $capacityText = $capacity ? $studentCount . '/' . $capacity : $studentCount . '/∞';
            $capacityPercent = $capacity ? min(100, round(($studentCount / max($capacity, 1)) * 100)) : null;
            $className = $batch->class->class_name ?? 'N/A';
            $subjectLabel = $subjectNames->implode(', ') ?: 'N/A';
            $totalExpense = (float) $batch->teachers->sum(function ($teacher) {
                return (float) ($teacher->pivot->salary_amount ?? 0);
            });

            $dayColors = [
                'saturday' => [
                    'bg' => 'bg-rose-100 dark:bg-rose-900/30',
                    'text' => 'text-rose-700 dark:text-rose-300',
                    'icon' => 'saturday',
                ],
                'sunday' => [
                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                    'text' => 'text-orange-700 dark:text-orange-300',
                ],
                'monday' => [
                    'bg' => 'bg-amber-100 dark:bg-amber-900/30',
                    'text' => 'text-amber-700 dark:text-amber-300',
                ],
                'tuesday' => ['bg' => 'bg-lime-100 dark:bg-lime-900/30', 'text' => 'text-lime-700 dark:text-lime-300'],
                'wednesday' => [
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-700 dark:text-green-300',
                ],
                'thursday' => [
                    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                    'text' => 'text-emerald-700 dark:text-emerald-300',
                ],
                'friday' => ['bg' => 'bg-teal-100 dark:bg-teal-900/30', 'text' => 'text-teal-700 dark:text-teal-300'],
            ];
        @endphp

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Batch Details: {{ $batch->batch_name }}
                </h1>
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                        <span class="material-symbols-outlined text-[16px]">school</span>
                        <span>{{ $className }}</span>
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                        <span class="material-symbols-outlined text-[16px]">menu_book</span>
                        <span>{{ $subjectLabel }}</span>
                    </span>
                    @if (!empty($schedule))
                        @foreach ($schedule as $dayKey => $info)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md {{ $dayColors[$dayKey]['bg'] ?? 'bg-slate-100 dark:bg-slate-800' }} {{ $dayColors[$dayKey]['text'] ?? 'text-slate-700 dark:text-slate-300' }}">
                                <span class="text-xs font-medium">{{ substr($info['day'], 0, 3) }}</span>
                                <span class="text-xs font-bold">{{ $info['time'] }}</span>
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.batches.edit', $batch->id) }}"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-sm transition-colors hover:bg-slate-300 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined text-lg">edit</span>
                    <span>Edit Batch</span>
                </a>
                <a href="{{ route('admin.batches.show', $batch->id) }}"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-lg">visibility</span>
                    <span>View Details</span>
                </a>
            </div>
        </div>

        {{-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10"> --}}
            <div class="flex flex-col gap-2 mb-10">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <p class="text-sm font-medium">Class Schedule</p>
                </div>
                @if (!empty($schedule))
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach ($schedule as $dayKey => $info)
                            <div
                                class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $dayColors[$dayKey]['bg'] ?? 'bg-slate-100 dark:bg-slate-800' }}">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-semibold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $info['day'] }}
                                    </span>
                                    <span
                                        class="text-sm font-bold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-900 dark:text-white' }}">
                                        {{ $info['time'] }}
                                    </span>
                                </div>
                                <span
                                    class="material-symbols-outlined text-[20px] ml-auto {{ $dayColors[$dayKey]['text'] ?? 'text-slate-500' }}">
                                    schedule
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">No schedule set</p>
                @endif
            </div>
        {{-- </div> --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">payments</span>
                    <p class="text-sm font-medium">Fee Amount</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">{{ number_format((float) $batch->fee_amount, 2) }}</p>
                <p class="text-xs text-slate-500">{{ \App\Models\Batch::FEE_TYPE_SELECT[$batch->fee_type] ?? 'N/A' }}</p>
            </div>
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">meeting_room</span>
                    <p class="text-sm font-medium">Class</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">{{ $batch->class->class_name ?? 'N/A' }}</p>
            </div>

            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">group</span>
                    <p class="text-sm font-medium">Students</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">{{ $capacityText }}</p>
                <p class="text-xs text-slate-500">Current / Capacity</p>
                @if ($capacityPercent !== null)
                    <div class="mt-2 h-2 w-full rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-primary" style="width: {{ $capacityPercent }}%"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-6 p-4 bg-white dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
            <form method="GET" action="{{ route('admin.batches.manage', $batch->id) }}"
                class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Month</label>
                    <select name="month"
                        class="form-select w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 min-w-[100px]">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Year</label>
                    <select name="year"
                        class="form-select w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md font-medium hover:bg-primary/90">
                    Filter
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-blue-50 via-white to-white dark:from-blue-900/20 dark:via-slate-800/60 dark:to-slate-900/60 p-6">
                <div class="flex items-center gap-2 text-blue-700 dark:text-blue-300">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    <p class="text-sm font-semibold uppercase tracking-wider">Expected Income</p>
                </div>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($expectedIncome, 2) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">{{ $studentCount }} ×
                    {{ number_format((float) $batch->fee_amount, 2) }}
                    ({{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }})</p>
            </div>
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-emerald-50 via-white to-white dark:from-emerald-900/20 dark:via-slate-800/60 dark:to-slate-900/60 p-6">
                <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300">
                    <span class="material-symbols-outlined">trending_up</span>
                    <p class="text-sm font-semibold uppercase tracking-wider">Income This Month</p>
                </div>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($incomeUntilNow, 2) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</p>
            </div>
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-teal-50 via-white to-white dark:from-teal-900/20 dark:via-slate-800/60 dark:to-slate-900/60 p-6">
                <div class="flex items-center gap-2 text-teal-700 dark:text-teal-300">
                    <span class="material-symbols-outlined">savings</span>
                    <p class="text-sm font-semibold uppercase tracking-wider">Total Income</p>
                </div>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalIncome, 2) }}</p>
                <p class="text-xs text-slate-500 mt-1">All time collection</p>
            </div>
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-rose-50 via-white to-white dark:from-rose-900/20 dark:via-slate-800/60 dark:to-slate-900/60 p-6">
                <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300">
                    <span class="material-symbols-outlined">trending_down</span>
                    <p class="text-sm font-semibold uppercase tracking-wider">Total Expense</p>
                </div>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalExpense, 2) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">Teachers salary</p>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-xl font-bold tracking-tight">Management Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    class="flex flex-col gap-5 p-6 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">school</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Assigned Teachers</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $teacherCount }} teacher(s)
                                    assigned</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-wider">Active</span>
                    </div>
                    <div class="flex flex-wrap gap-2 py-2">
                        @forelse ($batch->teachers->take(5) as $teacher)
                            @php
                                $initials = collect(explode(' ', $teacher->name))
                                    ->filter()
                                    ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary">{{ $initials ?: 'T' }}</span>
                                <span>{{ $teacher->name }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No teachers assigned yet.</p>
                        @endforelse
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('admin.batches.assignTeachers', $batch->id) }}"
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-lg bg-primary text-white font-bold text-sm shadow-md hover:opacity-90 transition-opacity">
                            <span class="material-symbols-outlined text-lg">person_add</span>
                            <span>Assign Teacher</span>
                        </a>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-5 p-6 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">group</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Enrolled Students</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $capacityText }} students</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year]) }}"
                            class="text-sm font-semibold text-primary hover:underline">Manage</a>
                    </div>
                    <div class="space-y-3">
                        @if ($capacityPercent !== null)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                    <div class="h-full bg-primary" style="width: {{ $capacityPercent }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 font-semibold">{{ $capacityPercent }}%</span>
                            </div>
                        @endif
                        <div
                            class="rounded-lg border border-slate-100 dark:border-slate-700 max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($enrolledStudents as $student)
                                <div class="flex items-center justify-between gap-2 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                            {{ trim($student->first_name . ' ' . $student->last_name) }}
                                        </p>
                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $student->class->class_name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs text-slate-400">{{ $student->id_no ?? 'N/A' }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 px-4 py-6 text-center">No students enrolled yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year]) }}"
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-lg bg-primary text-white font-bold text-sm shadow-md hover:opacity-90 transition-opacity">
                            <span class="material-symbols-outlined text-lg">person_add_alt</span>
                            <span>Enroll Students</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="mt-10 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold">Recently Enrolled Students</h3>
                <a class="text-primary text-sm font-bold hover:underline"
                    href="{{ route('admin.batches.show', $batch->id) }}">View Details</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Student ID</th>
                            <th class="px-6 py-3">Class</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($enrolledStudents->take(5) as $student)
                            <tr>
                                <td class="px-6 py-4 font-medium">
                                    {{ trim($student->first_name . ' ' . $student->last_name) }}
                                </td>
                                <td class="px-6 py-4">{{ $student->id_no ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $student->class->class_name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-500" colspan="3">No students enrolled yet.
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
