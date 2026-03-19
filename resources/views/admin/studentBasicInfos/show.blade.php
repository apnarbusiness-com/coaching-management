@extends('layouts.admin')
@section('content')
    <!-- Profile View Content -->
    <div class="p-8 max-w-6xl mx-auto w-full">
        <!-- Action Toolbar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.student-basic-infos.index') }}"
                    class="p-2 bg-white dark:bg-slate-800 border border-[#e7edf3] dark:border-slate-700 rounded-lg text-slate-600 hover:text-primary transition-colors flex items-center">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-2xl font-bold tracking-tight">Student Profile Preview</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.student-basic-infos.printIdCard', $studentBasicInfo->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-[#e7edf3] dark:border-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    Print ID
                </a>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-[#e7edf3] dark:border-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                    Report Card
                </button>
                <a href="{{ route('admin.student-basic-infos.edit', $studentBasicInfo->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    Edit Profile
                </a>
            </div>
        </div>
        <!-- Profile Header Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 p-6 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative">
                    <div class="size-32 rounded-full  border-primary/10 overflow-hidden bg-slate-100"
                        data-alt="Student profile portrait"
                        style='background-image: url("{{ $studentBasicInfo->image ? $studentBasicInfo->image->getUrl('preview') : 'https://ui-avatars.com/api/?name=' . urlencode($studentBasicInfo->first_name . ' ' . $studentBasicInfo->last_name) . '&background=random' }}"); background-position: center; background-size: cover;border: 2px solid gray;'>
                    </div>
                    <div
                        class="absolute bottom-1 right-1 bg-green-500 size-6 rounded-full border-4 border-white dark:border-slate-800">
                    </div>
                </div>
                <div class="flex flex-col text-left md:text-left">
                    <h1 class="text-3xl font-bold text-[#0d141b] dark:text-white mb-1">
                        {{ $studentBasicInfo->first_name }} {{ $studentBasicInfo->last_name }}
                    </h1>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-x-4 gap-y-2">
                        <span class="text-[#4c739a] font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">badge</span>
                            Academic ID: # {{ $studentBasicInfo->id_no }}
                        </span>
                        @php
                            $StatusColor =
                                isset($studentBasicInfo->status) && $studentBasicInfo->status ? 'green' : 'red';
                        @endphp
                        <span
                            class="px-3 py-1 bg-{{ $StatusColor }}-100 text-{{ $StatusColor }}-700 dark:bg-{{ $StatusColor }}-900/30 dark:text-{{ $StatusColor }}-400 text-xs font-bold rounded-full uppercase tracking-wider">
                            {{ $studentBasicInfo->status == 1 ? 'Active' : 'Inactive' }} Student
                        </span>
                    </div>
                    <span class="text-[#4c739a] font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">badge</span>
                        Roll No: {{ $studentBasicInfo->roll }}
                    </span>
                    <span class="text-[#4c739a] font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                        {{-- Address: --}}
                        {{ $studentBasicInfo->studentDetails->address ?? 'N/A' }}
                    </span>
                </div>
                <div class="md:ml-auto grid grid-cols-2 gap-4">
                    <div class="bg-background-light dark:bg-slate-700/50 p-4 rounded-lg text-center min-w-[120px]">
                        <p class="text-[#4c739a] text-xs font-medium uppercase mb-1">Attendance</p>
                        <p class="text-xl font-bold text-primary">{{ $attendancePercent }}%</p>
                    </div>
                    <div class="bg-background-light dark:bg-slate-700/50 p-4 rounded-lg text-center min-w-[120px]">
                        <p class="text-[#4c739a] text-xs font-medium uppercase mb-1">GPA Score</p>
                        <p class="text-xl font-bold text-primary">{{ $score }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Details -->
            <div class="lg:col-span-2 flex flex-col gap-8">
                <!-- Academic Information -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">school</span>
                        <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">Academic Information</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">
                                Current Class
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->class->class_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">
                                {{ trans('cruds.studentBasicInfo.fields.academic_background') }}
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->academicBackground->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">
                                Student ID No.
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">#  {{ $studentBasicInfo->id_no }}</p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">
                                Admission Date</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->joining_date ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <!-- Personal Details -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">Personal Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">Date of
                                Birth</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">{{ $studentBasicInfo->dob ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">Gender
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ App\Models\StudentBasicInfo::GENDER_RADIO[$studentBasicInfo->gender] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">Blood
                                Group</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->studentDetails->student_blood_group ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">Phone
                                Number</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->contact_number ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">Email
                                Address</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">{{ $studentBasicInfo->email ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-[#4c739a] text-sm font-medium mb-1 uppercase tracking-tight">
                                Residential Address</p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->studentDetails->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <!-- Payment History Table -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div
                        class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">payments</span>
                            <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">Recent Payment History</h3>
                        </div>
                        <a class="text-primary text-sm font-bold hover:underline" href="#">View All History</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[#0d141b] dark:text-white">
                            <thead>
                                <tr class="bg-background-light dark:bg-slate-700/50">
                                    <th class="px-6 py-3 text-[#4c739a] text-xs font-bold uppercase">Date</th>
                                    <th class="px-6 py-3 text-[#4c739a] text-xs font-bold uppercase">Description
                                    </th>
                                    <th class="px-6 py-3 text-[#4c739a] text-xs font-bold uppercase">Amount</th>
                                    <th class="px-6 py-3 text-[#4c739a] text-xs font-bold uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e7edf3] dark:divide-slate-700">
                                @forelse ($studentBasicInfo->studentEarnings->take(5) as $earning)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium">{{ $earning->date ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $earning->description ?? 'Tuition Fee' }}</td>
                                        <td class="px-6 py-4 text-sm font-bold">{{ number_format($earning->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-[10px] font-bold rounded uppercase">Paid</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-slate-500">No recent payments
                                            found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Right Column: Secondary Details -->
            <div class="flex flex-col gap-8">
                <!-- Guardian Information -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">family_restroom</span>
                        <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">Guardian Details</h3>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <div>
                            <p class="text-[#4c739a] text-xs font-medium mb-1 uppercase tracking-tight">
                                Father's Name
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->studentDetails->fathers_name ?? 'N/A' }}</p>
                            <p class="text-xs text-[#4c739a]">NID:
                                {{ $studentBasicInfo->studentDetails->fathers_nid ?? 'N/A' }}</p>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                            <p class="text-[#4c739a] text-xs font-medium mb-1 uppercase tracking-tight">
                                Mother's Name
                            </p>
                            <p class="font-bold text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->studentDetails->mothers_name ?? 'N/A' }}</p>
                            <p class="text-xs text-[#4c739a]">NID:
                                {{ $studentBasicInfo->studentDetails->mothers_nid ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-primary/5 dark:bg-primary/10 p-4 rounded-lg">
                            <p class="text-primary text-xs font-bold mb-2 uppercase tracking-wide flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">emergency_share</span>
                                Emergency Contact
                            </p>
                            <p class="font-bold text-sm text-[#0d141b] dark:text-white">
                                {{ $studentBasicInfo->studentDetails->guardian_name ?? 'N/A' }}
                                ({{ $studentBasicInfo->studentDetails->guardian_relation ?? 'Guardian' }})</p>
                            <p class="text-primary text-sm font-bold">
                                {{ $studentBasicInfo->studentDetails->guardian_contact_number ?? 'N/A' }}</p>
                            <p class="text-primary text-sm font-bold">
                                {{ $studentBasicInfo->studentDetails->guardian_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <!-- Assigned Batches -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">event_note</span>
                        <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">Assigned Batches & Class Days</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4 text-[#0d141b] dark:text-white">
                            @forelse ($studentBasicInfo->batches as $batch)
                                <li class="p-4 bg-background-light dark:bg-slate-700/30 rounded-lg">
                                    <p class="text-sm font-bold">{{ $batch->batch_name }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $batch->subject->name ?? 'N/A' }} | {{ $batch->class->class_name ?? 'N/A' }} |
                                        {{ \App\Models\Batch::FEE_TYPE_SELECT[$batch->fee_type] ?? $batch->fee_type }}
                                    </p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($batch->class_schedule ?? [] as $day => $time)
                                            <span class="px-2 py-1 bg-primary/10 text-primary text-[11px] font-semibold rounded">
                                                {{ \App\Models\Batch::CLASS_DAY_SELECT[$day] ?? $day }}:
                                                {{ \Carbon\Carbon::parse($time)->format('h:i A') }}
                                            </span>
                                        @endforeach
                                    </div>
                                </li>
                            @empty
                                <p class="text-sm text-slate-500">No batch assigned</p>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Enrolled Subjects -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-[#e7edf3] dark:border-slate-700 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-[#e7edf3] dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">auto_stories</span>
                        <h3 class="font-bold text-lg text-[#0d141b] dark:text-white">
                            Enrolled {{ trans('cruds.subject.title') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3 text-[#0d141b] dark:text-white">
                            @forelse ($studentBasicInfo->subjects as $subject)
                                <li
                                    class="flex items-center justify-between p-3 bg-background-light dark:bg-slate-700/30 rounded-lg group hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="material-symbols-outlined text-slate-400 group-hover:text-primary">auto_stories</span>
                                        <span class="text-sm font-bold">{{ $subject->name }}</span>
                                    </div>
                                    <span class="text-xs font-medium text-slate-500">{{ $subject->code ?? '' }}</span>
                                </li>
                            @empty
                                <p class="text-sm text-slate-500">No subjects assigned</p>
                            @endforelse
                        </ul>
                        <button onclick="openSubjectModal()"
                            class="w-full mt-6 py-2 border-2 border-dashed border-[#e7edf3] dark:border-slate-700 rounded-lg text-sm text-[#4c739a] font-bold hover:border-primary/50 hover:text-primary transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Manage {{ trans('cruds.subject.title') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{-- <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.studentBasicInfo.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.student-basic-infos.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.id') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.roll') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->roll }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.id_no') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->id_no }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.first_name') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->first_name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.last_name') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->last_name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.gender') }}
                            </th>
                            <td>
                                {{ App\Models\StudentBasicInfo::GENDER_RADIO[$studentBasicInfo->gender] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.contact_number') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->contact_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.email') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->email }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.dob') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->dob }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\StudentBasicInfo::STATUS_SELECT[$studentBasicInfo->status] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.joining_date') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->joining_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.image') }}
                            </th>
                            <td>
                                @if ($studentBasicInfo->image)
                                <a href="{{ $studentBasicInfo->image->getUrl() }}" target="_blank"
                                    style="display: inline-block">
                                    <img src="{{ $studentBasicInfo->image->getUrl('thumb') }}">
                                </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.class') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->class->class_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.section') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->section->section_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.shift') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->shift->shift_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.user') }}
                            </th>
                            <td>
                                {{ $studentBasicInfo->user->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.studentBasicInfo.fields.subject') }}
                            </th>
                            <td>
                                @foreach ($studentBasicInfo->subjects as $key => $subject)
                                <span class="label label-info">{{ $subject->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.student-basic-infos.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div> --}}



    {{-- Related Data --}}
    <div class="p-8 max-w-6xl mx-auto w-full">


        <div class="card ">
            <div class="card-header">
                {{ trans('global.relatedData') }}
            </div>
            <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="#student_earnings" role="tab" data-toggle="tab">
                        {{ trans('cruds.earning.title') }}
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" role="tabpanel" id="student_earnings">
                    @includeIf('admin.studentBasicInfos.relationships.studentEarnings', [
                        'earnings' => $studentBasicInfo->studentEarnings,
                    ])
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- Subject Management Modal -->
    <div id="subjectModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                onclick="closeSubjectModal()"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
                <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-8 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-primary">auto_stories</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">
                                Manage Enrolled {{ trans('cruds.subject.title') }}
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                    Select the subjects this student should be enrolled in.
                                </p>
                                <form id="syncSubjectsForm">
                                    @csrf
                                    <div class="space-y-4">
                                        <div class="form-group">
                                            <label
                                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Available
                                                Subjects</label>
                                            <select name="subjects[]" id="subjects-select" class="w-full select2"
                                                multiple="multiple">
                                                @foreach ($subjects as $id => $name)
                                                    <option value="{{ $id }}"
                                                        {{ $studentBasicInfo->subjects->contains($id) ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" onclick="submitSubjects()" id="saveSubjectsBtn"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-primary/20 px-6 py-2.5 bg-primary text-base font-bold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Save Changes
                    </button>
                    <button type="button" onclick="closeSubjectModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2.5 bg-white dark:bg-slate-800 text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:w-auto sm:text-sm transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSubjectModal() {
            $('#subjectModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            // Re-initialize select2 if needed when modal opens
            $('#subjects-select').select2({
                width: '100%',
                placeholder: "Select subjects...",
                dropdownParent: $('#subjectModal')
            });
        }

        function closeSubjectModal() {
            $('#subjectModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        function submitSubjects() {
            const btn = $('#saveSubjectsBtn');
            const originalText = btn.html();

            btn.prop('disabled', true).html('<span class="material-symbols-outlined animate-spin">sync</span> Saving...');

            const formData = $('#syncSubjectsForm').serialize();

            $.ajax({
                url: '{{ route('admin.student-basic-infos.syncSubjects', $studentBasicInfo->id) }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                    btn.prop('disabled', false).html(originalText);
                }
            });
        }
    </script>
@endsection
