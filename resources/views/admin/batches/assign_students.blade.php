@extends('layouts.admin')
@section('content')
    <div class="max-w-6xl mx-auto p-6 flex flex-col gap-6">
        @php
            $assignedCount = count($assignedStudentIds ?? []);
            $totalStudents = $students->count();
            $estimatedRevenue = $assignedCount * (float) $batch->fee_amount;
            $classOptions = $students->map(fn ($student) => $student->class->class_name ?? null)
                ->filter()
                ->unique()
                ->sort()
                ->values();
        @endphp

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Assign Students to Batch</h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Enroll students into the <strong>{{ $batch->batch_name }}</strong> batch.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-primary/10 dark:bg-primary/20 rounded-lg text-primary text-sm font-medium">
                    <span class="material-symbols-outlined text-sm">group</span>
                    <span>Assigned: {{ $assignedCount }}</span>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.batches.assignStudents.store', $batch->id) }}" class="flex flex-col gap-6">
            @csrf
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="relative flex-1 w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input
                        id="studentSearch"
                        class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500"
                        placeholder="Search students by name, ID or class..." type="text" />
                </div>
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    <select id="classFilter"
                        class="px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200">
                        <option value="">All Classes</option>
                        @foreach ($classOptions as $className)
                            <option value="{{ $className }}">{{ $className }}</option>
                        @endforeach
                    </select>
                    <select id="feeFilter"
                        class="px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200">
                        <option value="">All Fee Status</option>
                        <option value="paid">Paid</option>
                        <option value="not_paid">Not Paid</option>
                    </select>
                    <a href="{{ route('admin.batches.manage', $batch->id) }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-bottom border-slate-200 dark:border-slate-700">
                                <th class="px-6 py-4 w-12 text-center">
                                    <input class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary" type="checkbox" disabled />
                                </th>
                                <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Student Details</th>
                                <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Student ID</th>
                                <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Current Class</th>
                                <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Fee Status</th>
                                <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($students as $student)
                                @php
                                    $isAssigned = in_array($student->id, $assignedStudentIds ?? [], true);
                                    $latestEarning = $student->studentEarnings->sortByDesc('earning_date')->first();
                                    $feeStatus = $latestEarning ? 'Paid' : 'Not Paid';
                                    $feeStatusKey = $latestEarning ? 'paid' : 'not_paid';
                                    $statusClass = $latestEarning
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-900/40 dark:text-slate-400';
                                    $rowSearch = strtolower(
                                        trim($student->first_name . ' ' . $student->last_name) . ' ' .
                                        ($student->id_no ?? '') . ' ' .
                                        ($student->class->class_name ?? '')
                                    );
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors"
                                    data-search="{{ $rowSearch }}"
                                    data-class="{{ $student->class->class_name ?? '' }}"
                                    data-fee="{{ $feeStatusKey }}">
                                    <td class="px-6 py-4 text-center">
                                        <input name="students[]" value="{{ $student->id }}" {{ $isAssigned ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                            type="checkbox" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs uppercase">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-slate-100">
                                                    {{ trim($student->first_name . ' ' . $student->last_name) }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $student->id_no ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        {{ $student->class->class_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[11px] font-bold uppercase {{ $statusClass }}">{{ $feeStatus }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.student-basic-infos.show', $student->id) }}" class="text-slate-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-6 text-center text-slate-500" colspan="6">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-auto flex flex-col md:flex-row items-center justify-between gap-4 p-4 md:p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg sticky bottom-6">
                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8">
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ $assignedCount }} Students Selected</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Out of {{ $totalStudents }} total available</p>
                        </div>
                    </div>
                    <div class="h-10 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>
                    <div class="flex flex-col">
                        <p class="text-xs uppercase font-bold text-slate-400 tracking-wider">Estimated Revenue</p>
                        <p class="text-lg font-bold text-primary">{{ number_format($estimatedRevenue, 2) }} <span class="text-sm font-normal text-slate-500">/mo</span></p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.batches.manage', $batch->id) }}"
                        class="flex-1 md:flex-none px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancel
                    </a>
                    <button
                        class="flex-1 md:flex-none px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all">
                        Confirm Enrollment
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (function() {
            const searchInput = document.getElementById('studentSearch');
            const classFilter = document.getElementById('classFilter');
            const feeFilter = document.getElementById('feeFilter');
            const rows = Array.from(document.querySelectorAll('tbody tr[data-search]'));

            function applyFilters() {
                const search = (searchInput.value || '').toLowerCase().trim();
                const classValue = classFilter.value;
                const feeValue = feeFilter.value;

                rows.forEach((row) => {
                    const rowSearch = row.getAttribute('data-search') || '';
                    const rowClass = row.getAttribute('data-class') || '';
                    const rowFee = row.getAttribute('data-fee') || '';

                    const matchesSearch = !search || rowSearch.includes(search);
                    const matchesClass = !classValue || rowClass === classValue;
                    const matchesFee = !feeValue || rowFee === feeValue;

                    row.classList.toggle('hidden', !(matchesSearch && matchesClass && matchesFee));
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }
            if (classFilter) {
                classFilter.addEventListener('change', applyFilters);
            }
            if (feeFilter) {
                feeFilter.addEventListener('change', applyFilters);
            }
        })();
    </script>
@endsection
