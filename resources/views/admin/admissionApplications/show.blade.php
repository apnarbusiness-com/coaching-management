@extends('layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Application #{{ $application->id }}</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Review the submitted information below.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($application->status !== 'approved')
                        <form method="POST" action="{{ route('admin.admission-applications.approve', $application->id) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover">
                                Approve & Create Student
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.admission-applications.destroy', $application->id) }}"
                        onsubmit="return confirm('Remove this application?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
                            @if ($application->photo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($application->photo_path) }}" alt="Photo"
                                    class="rounded-lg w-full object-cover" style="max-height: 260px;">
                            @else
                                <div class="text-slate-400">No photo</div>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs uppercase text-slate-500">Student Name</p>
                                <p class="font-semibold">{{ $application->first_name }} {{ $application->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Gender / DOB</p>
                                <p class="font-semibold">{{ ucfirst($application->gender) }} · {{ $application->dob?->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Contact</p>
                                <p class="font-semibold">{{ $application->contact_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Guardian Contact</p>
                                <p class="font-semibold">{{ $application->guardian_contact_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Father / Mother</p>
                                <p class="font-semibold">{{ $application->fathers_name }} / {{ $application->mothers_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Blood Group</p>
                                <p class="font-semibold">{{ $application->student_blood_group ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Admission Date</p>
                                <p class="font-semibold">{{ $application->admission_date?->format('d M Y') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Admission ID</p>
                                <p class="font-semibold">{{ $application->admission_id_no ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Class / Batch</p>
                                <p class="font-semibold">{{ $application->class_name ?? '—' }} · {{ $application->batch_name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Class Roll</p>
                                <p class="font-semibold">{{ $application->class_roll ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs uppercase text-slate-500">Address</p>
                        <p class="font-semibold">{{ $application->address ?? '—' }}</p>
                        <p class="text-sm text-slate-500">Village: {{ $application->village ?? '—' }}, P.O: {{ $application->post_office ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-500">School</p>
                        <p class="font-semibold">{{ $application->school_name ?? '—' }}</p>
                        <p class="text-sm text-slate-500">Subjects: {{ $application->subjects ? implode(', ', $application->subjects) : '—' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 p-6">
                    <p class="text-xs uppercase text-slate-500">Status</p>
                    <p class="font-semibold">
                        {{ ucfirst($application->status) }}
                        @if ($application->approved_at)
                            · Approved on {{ $application->approved_at->format('d M Y') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
