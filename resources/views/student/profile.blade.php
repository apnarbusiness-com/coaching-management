@extends('layouts.admin')
@section('content')
    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
        $avatarUrl = $student->image ? ($student->image->preview ?? $student->image->url) : null;
        $initials = strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1));
    @endphp

    <main class="flex-1 overflow-y-auto">
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-sky-50 to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950"></div>
            <div class="absolute -right-20 -top-20 size-72 rounded-full bg-sky-200/40 blur-3xl dark:bg-sky-500/10"></div>
            <div class="absolute -left-20 bottom-0 size-64 rounded-full bg-indigo-200/40 blur-3xl dark:bg-indigo-500/10"></div>

            <div class="relative mx-auto max-w-6xl px-6 py-10">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Student Profile</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                            Profile Overview
                        </h1>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                            View your personal and academic details in one place.
                        </p>
                    </div>
                    <a href="{{ route('admin.home') }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        Back to Dashboard
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $studentName }}"
                                            class="size-28 rounded-2xl object-cover shadow-md">
                                    @else
                                        <div class="flex size-28 items-center justify-center rounded-2xl bg-slate-100 text-2xl font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <span
                                        class="absolute -bottom-2 -right-2 rounded-full bg-emerald-500 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                                        Active
                                    </span>
                                </div>
                                <h2 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{{ $studentName }}</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Admission ID: {{ $student->id_no ?? 'N/A' }}</p>
                                <div class="mt-4 w-full rounded-xl bg-slate-50 px-4 py-3 text-left text-sm text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs uppercase tracking-wider text-slate-400">Class</span>
                                        <span class="font-semibold">{{ $student->class->class_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-xs uppercase tracking-wider text-slate-400">Section</span>
                                        <span class="font-semibold">{{ $student->section->section_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-xs uppercase tracking-wider text-slate-400">Shift</span>
                                        <span class="font-semibold">{{ $student->shift->shift_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Personal Details</h3>
                                <div class="mt-4 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Full Name</span>
                                        <span class="font-semibold">{{ $studentName }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Gender</span>
                                        <span class="font-semibold">{{ \App\Models\StudentBasicInfo::GENDER_RADIO[$student->gender] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Date of Birth</span>
                                        <span class="font-semibold">{{ $student->dob ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Status</span>
                                        <span class="font-semibold">{{ \App\Models\StudentBasicInfo::STATUS_SELECT[$student->status] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Academic Details</h3>
                                <div class="mt-4 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Academic Background</span>
                                        <span class="font-semibold">{{ $student->academicBackground->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Roll</span>
                                        <span class="font-semibold">{{ $student->roll ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Joining Date</span>
                                        <span class="font-semibold">{{ $student->joining_date ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Email</span>
                                        <span class="font-semibold">{{ $student->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Contact</h3>
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 text-sm">
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Phone</p>
                                    <p class="mt-1 font-semibold">{{ $student->contact_number ?? 'N/A' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Email</p>
                                    <p class="mt-1 font-semibold">{{ $student->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Guardians</h3>
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 text-sm">
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Father</p>
                                    <p class="mt-1 font-semibold">{{ $student->studentDetails->fathers_name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">NID: {{ $student->studentDetails->fathers_nid ?? 'N/A' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Mother</p>
                                    <p class="mt-1 font-semibold">{{ $student->studentDetails->mothers_name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">NID: {{ $student->studentDetails->mothers_nid ?? 'N/A' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Guardian</p>
                                    <p class="mt-1 font-semibold">{{ $student->studentDetails->guardian_name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Relation: {{ $student->studentDetails->guardian_relation ?? 'N/A' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Guardian Contact</p>
                                    <p class="mt-1 font-semibold">{{ $student->studentDetails->guardian_contact_number ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Email: {{ $student->studentDetails->guardian_email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
                                <p class="text-xs uppercase tracking-wider text-slate-400">Address</p>
                                <p class="mt-1 font-semibold">{{ $student->studentDetails->address ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Batches</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($student->batches as $batch)
                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">
                                        {{ $batch->batch_name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500">No batches assigned yet.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Subjects</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($student->subjects as $subject)
                                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/20 dark:text-sky-200">
                                        {{ $subject->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500">No subjects assigned yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
