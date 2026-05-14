@extends('layouts.admin')
@section('title', 'Subjects — Details')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="flex">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white"
                            href="{{ route('admin.home') }}">
                            <span class="material-symbols-outlined text-[18px] mr-2">home</span>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <a class="ml-1 text-sm font-medium text-slate-500 hover:text-primary md:ml-2 dark:text-slate-400 dark:hover:text-white"
                                href="{{ route('admin.subjects.index') }}">Subjects</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Subject
                                Details</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $subject->name }}
                    </h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">
                        Subject Information & Related Records
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit Subject
                    </a>
                    <a href="{{ route('admin.subjects.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">info</span>
                            <h3 class="text-lg font-bold">Basic Information</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-6">
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Subject
                                    Name</label>
                                <p class="mt-1 text-lg font-semibold">{{ $subject->name }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Subject
                                    Code</label>
                                <p class="mt-1 text-lg font-mono font-bold text-primary">{{ $subject->code ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Monthly
                                    Fee</label>
                                <p class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">
                                    ${{ number_format($subject->monthly_fee, 2) }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Internal
                                    ID</label>
                                <p class="mt-1 text-lg font-semibold">#{{ $subject->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div
                        class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-xl shadow-lg p-6 text-white relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                            <span class="material-symbols-outlined text-[100px]">groups</span>
                        </div>
                        <h4 class="text-indigo-100 text-xs font-bold uppercase tracking-wider mb-4 leading-none">Total
                            Students</h4>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold">{{ $subject->subjectStudentBasicInfos->count() }}</span>
                            <span class="text-indigo-200 text-sm">Enrolled</span>
                        </div>
                    </div>
                </div>

                <!-- Related Data Card -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="border-b border-slate-200 dark:border-slate-700">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="relationship-tabs"
                                role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg border-primary text-primary"
                                        id="students-tab" data-tabs-target="#subject_student_basic_infos" type="button"
                                        role="tab" aria-controls="subject_student_basic_infos" aria-selected="true">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[20px]">group</span>
                                            {{ trans('cruds.studentBasicInfo.title') }}
                                        </div>
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button
                                        class="inline-block p-4 border-b-2 rounded-t-lg border-transparent hover:text-slate-600 hover:border-slate-300 dark:hover:text-slate-300"
                                        id="teachers-tab" data-tabs-target="#subject_teachers" type="button" role="tab"
                                        aria-controls="subject_teachers" aria-selected="false">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[20px]">person_4</span>
                                            {{ trans('cruds.teacher.title') }}
                                        </div>
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button
                                        class="inline-block p-4 border-b-2 rounded-t-lg border-transparent hover:text-slate-600 hover:border-slate-300 dark:hover:text-slate-300"
                                        id="earnings-tab" data-tabs-target="#subject_earnings" type="button" role="tab"
                                        aria-controls="subject_earnings" aria-selected="false">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[20px]">payments</span>
                                            {{ trans('cruds.earning.title') }}
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div id="tab-content">
                            <div class="p-4 md:p-6" id="subject_student_basic_infos" role="tabpanel"
                                aria-labelledby="students-tab">
                                @includeIf('admin.subjects.relationships.subjectStudentBasicInfos', ['studentBasicInfos' => $subject->subjectStudentBasicInfos])
                            </div>
                            <div class="p-4 md:p-6 hidden" id="subject_teachers" role="tabpanel"
                                aria-labelledby="teachers-tab">
                                @includeIf('admin.subjects.relationships.subjectTeachers', ['teachers' => $subject->subjectTeachers])
                            </div>
                            <div class="p-4 md:p-6 hidden" id="subject_earnings" role="tabpanel"
                                aria-labelledby="earnings-tab">
                                @includeIf('admin.subjects.relationships.subjectEarnings', ['earnings' => $subject->subjectEarnings])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function () {
                $('[data-tabs-target]').on('click', function () {
                    const target = $(this).data('tabs-target');

                    // Hide all tab panes
                    $('#tab-content > div').addClass('hidden');
                    // Show target pane
                    $(target).removeClass('hidden');

                    // Reset all tab styles
                    $('[data-tabs-target]').removeClass('border-primary text-primary').addClass('border-transparent text-slate-500');
                    // Set active tab style
                    $(this).removeClass('border-transparent text-slate-500').addClass('border-primary text-primary');
                });
            });
        </script>
    @endsection
@endsection