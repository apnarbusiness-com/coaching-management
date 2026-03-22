@extends('layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-6xl mx-auto flex flex-col gap-6 pb-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Admission Applications</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Public submissions waiting for review.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-700">
                    <span class="text-sm text-slate-500">Total: {{ $applications->total() }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Student</th>
                                <th class="px-4 py-3 text-left">Contact</th>
                                <th class="px-4 py-3 text-left">Class / Batch</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Submitted</th>
                                <th class="px-4 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse ($applications as $application)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3 font-semibold">#{{ $application->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $application->first_name }} {{ $application->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $application->gender }} · {{ $application->dob?->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $application->contact_number }}</div>
                                        <div class="text-xs text-slate-500">Guardian: {{ $application->guardian_contact_number }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $application->class_name ?? '—' }}</div>
                                        <div class="text-xs text-slate-500">{{ $application->batch_name ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $application->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $application->created_at?->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.admission-applications.show', $application->id) }}"
                                            class="text-primary font-semibold hover:underline">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">No applications yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 md:p-6">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
