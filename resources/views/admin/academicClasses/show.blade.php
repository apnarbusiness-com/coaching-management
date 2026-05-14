@extends('layouts.admin')
@section('title', 'Academic Classes — Details')
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
                                href="{{ route('admin.academic-classes.index') }}">Academic Classes</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Class
                                Details</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $academicClass->class_name }}
                    </h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">
                        Academic Class Overview & Related Information
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.academic-classes.edit', $academicClass->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit Class
                    </a>
                    <a href="{{ route('admin.academic-classes.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">info</span>
                            <h3 class="text-lg font-bold">Basic Information</h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Class
                                        Name</label>
                                    <p class="mt-1 text-lg font-semibold">{{ $academicClass->class_name }}</p>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Academic
                                        Year</label>
                                    <p class="mt-1 text-lg font-semibold">{{ $academicClass->academic_year ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Class
                                        Code</label>
                                    <p class="mt-1 text-lg font-mono font-bold text-primary">
                                        {{ $academicClass->class_code ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Internal
                                        ID</label>
                                    <p class="mt-1 text-lg font-semibold">#{{ $academicClass->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Data Tabs -->
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="border-b border-slate-200 dark:border-slate-700">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="relationship-tabs"
                                role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg border-primary text-primary"
                                        id="students-tab" data-tabs-target="#class_student_basic_infos" type="button"
                                        role="tab" aria-controls="class_student_basic_infos" aria-selected="true">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[20px]">group</span>
                                            {{ trans('cruds.studentBasicInfo.title') }}
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div id="tab-content">
                            <div class="p-4 md:p-6" id="class_student_basic_infos" role="tabpanel"
                                aria-labelledby="students-tab">
                                @includeIf('admin.academicClasses.relationships.classStudentBasicInfos', ['studentBasicInfos' => $academicClass->classStudentBasicInfos])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Sections Card -->
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">grid_view</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Sections</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @forelse($academicClass->class_sections as $class_section)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 transition-colors hover:bg-blue-100 dark:hover:bg-blue-900/50">
                                        {{ $class_section->section_name }}
                                    </span>
                                @empty
                                    <p class="text-sm text-slate-500 italic">No sections assigned</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Shifts Card -->
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">schedule</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Shifts</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @forelse($academicClass->class_shifts as $class_shift)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10 transition-colors hover:bg-purple-100 dark:hover:bg-purple-900/50">
                                        {{ $class_shift->shift_name }}
                                    </span>
                                @empty
                                    <p class="text-sm text-slate-500 italic">No shifts assigned</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Statistics/Meta Info -->
                    <div
                        class="bg-blue-600 dark:bg-blue-700 rounded-xl shadow-lg p-6 text-white overflow-hidden relative group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                            <span class="material-symbols-outlined text-[120px]">trending_up</span>
                        </div>
                        <h4 class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-4 leading-none">Total
                            Students</h4>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold">{{ $academicClass->classStudentBasicInfos->count() }}</span>
                            <span class="text-blue-200 text-sm">Enrolled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection