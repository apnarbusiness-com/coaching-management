@extends('layouts.admin')
@section('title', 'Admission Applications — List')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-7xl mx-auto flex flex-col gap-6 pb-12">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Pending Admissions</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Students awaiting approval.</p>
            </div>
        </div>

        {{-- Search + Filter --}}
        <div class="flex flex-col md:flex-row gap-4">
            <form method="GET" action="{{ route('admin.admission-applications.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by ID, name, or mobile..."
                        class="pl-9 pr-4 py-2 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1a2632] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                </div>
                <div class="flex gap-1 bg-slate-100 dark:bg-slate-800/60 rounded-lg p-1">
                    <a href="{{ route('admin.admission-applications.index', ['filter' => 'all', 'search' => $search]) }}"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $filter === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                        All
                    </a>
                    <a href="{{ route('admin.admission-applications.index', ['filter' => 'referred', 'search' => $search]) }}"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $filter === 'referred' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                        Referred
                    </a>
                    <a href="{{ route('admin.admission-applications.index', ['filter' => 'no-ref', 'search' => $search]) }}"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $filter === 'no-ref' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                        No Referral
                    </a>
                </div>
                @if($search)
                <a href="{{ route('admin.admission-applications.index', ['filter' => $filter]) }}"
                    class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400">
                    Clear
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">Total: <strong class="text-slate-700 dark:text-slate-200">{{ $students->total() }}</strong></span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Referral</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Submitted</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse ($students as $student)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-900 dark:text-white">#{{ $student->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name ?? '', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ ucfirst($student->gender) }}
                                            @if($student->dob) · {{ $student->dob }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-700 dark:text-slate-300">{{ $student->contact_number }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Guardian: {{ $student->studentDetails?->guardian_contact_number ?? '—' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($student->referredBy)
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-[10px] font-bold">
                                        {{ substr($student->referredBy->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $student->referredBy->name }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $student->referral_code }}</div>
                                    </div>
                                </div>
                                @else
                                <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                                {{ $student->created_at?->format('d M Y') }}
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $student->created_at?->format('h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.admission-applications.show', $student->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 dark:bg-teal-900/20 dark:text-teal-400 dark:hover:bg-teal-900/30 transition-all">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">No pending admissions</p>
                                <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">
                                    @if($search)
                                    Try a different search term.
                                    @elseif($filter === 'referred')
                                    No referred applications pending.
                                    @elseif($filter === 'no-ref')
                                    All applications have referral codes.
                                    @else
                                    New applications will appear here.
                                    @endif
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection