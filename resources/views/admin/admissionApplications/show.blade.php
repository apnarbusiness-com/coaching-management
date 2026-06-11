@extends('layouts.admin')
@section('title', "Application #{$student->id} — Details")
@section('content')
@php
$ref = $student->studentDetails?->reference ? json_decode($student->studentDetails->reference, true) : [];
$bgClass = $student->status === 'pending' ? 'from-amber-500 to-orange-600' : 'from-teal-500 to-emerald-600';
$statusBadge = $student->status === 'pending'
    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
    : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
$statusLabel = $student->status === 'pending' ? 'Pending' : 'Approved';
@endphp
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-6xl mx-auto flex flex-col gap-6 pb-12">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Application #{{ $student->id }}</h1>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Submitted {{ $student->created_at?->format('d M Y, h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($student->status === 'pending')
                    <form method="POST" action="{{ route('admin.admission-applications.approve', $student->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-sm transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Approve
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.admission-applications.destroy', $student->id) }}"
                        onsubmit="return confirm('Reject and remove this application permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-800/40 transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.student-basic-infos.show', $student->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        View Student Profile
                    </a>
                @endif
            </div>
        </div>

        {{-- Photo + Student Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-gradient-to-br {{ $bgClass }} p-6 text-center">
                        @if ($student->image)
                            <img src="{{ $student->image->url }}" alt="Photo"
                                class="w-32 h-32 rounded-xl object-cover mx-auto ring-4 ring-white/30 shadow-lg">
                        @else
                            <div class="w-32 h-32 rounded-xl bg-white/20 mx-auto ring-4 ring-white/30 flex items-center justify-center">
                                <svg class="h-12 w-12 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-5 text-center">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $student->first_name }} {{ $student->last_name }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ ucfirst($student->gender) }}@if($student->dob) · {{ $student->dob }}@endif</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                {{-- Contact --}}
                <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                        <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Contact Information
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Mobile</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->contact_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Email</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Birth Registration</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->student_birth_no ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Blood Group</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->student_blood_group ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Admission Details --}}
                <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                        <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Admission Details
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Admission Date</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->joining_date ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Admission ID</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->id_no ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Class</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $ref['class_name'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Class Roll</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $ref['class_roll'] ?? $student->roll ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Batch</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $ref['batch_name'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Previous School</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $ref['school_name'] ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Family & Guardian --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                    <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Family & Guardian
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Father's Name</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->fathers_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Mother's Name</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->mothers_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Guardian Name</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->guardian_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Guardian Relation</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->guardian_relation ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Guardian Mobile</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->guardian_contact_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Guardian Email</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->guardian_email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Address & School --}}
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                    <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Address & School
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Present Address</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $student->studentDetails?->address ?? '—' }}</p>
                        @if(!empty($ref['village']) || !empty($ref['post_office']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Village: {{ $ref['village'] ?? '—' }} · P.O: {{ $ref['post_office'] ?? '—' }}
                        </p>
                        @endif
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-700/60 pt-5">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Subjects</p>
                        @if(!empty($ref['subjects']))
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @foreach($ref['subjects'] as $sub)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">
                                {{ $sub }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">—</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Referral Info --}}
        @if($student->referral_code)
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Referral Information
                </h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-lg font-bold shrink-0">
                            {{ $student->referredBy ? substr($student->referredBy->name, 0, 1) : '?' }}
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">Referred By</p>
                            <p class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $student->referredBy->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Referral Code: <span class="font-mono font-semibold text-amber-600 dark:text-amber-400">{{ $student->referral_code }}</span>
                            </p>
                        </div>
                    </div>
                    @if($student->referredBy)
                    <div class="md:ml-auto flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Wallet Balance</p>
                            <p class="text-lg font-bold text-teal-600 dark:text-teal-400">{{ number_format($student->referredBy->wallet?->balance ?? 0, 2) }} TK</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Earned</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($student->referredBy->wallet?->total_earned ?? 0, 2) }} TK</p>
                        </div>
                        @if($student->referredBy->email)
                        <div class="text-center">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Email</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $student->referredBy->email }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Status Timeline --}}
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Timeline
                </h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="flex items-start gap-3">
                        <div class="flex flex-col items-center">
                            <div class="h-8 w-8 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                <svg class="h-4 w-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            @if($student->status !== 'pending')
                            <div class="w-0.5 h-full min-h-[2rem] bg-teal-200 dark:bg-teal-800"></div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Submitted</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->created_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    @if($student->status !== 'pending')
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Approved</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->updated_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection