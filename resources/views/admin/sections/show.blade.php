@extends('layouts.admin')
@section('title', 'Sections — Details')
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
                                href="{{ route('admin.sections.index') }}">Sections</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Section
                                Details</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $section->section_name }}
                    </h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">
                        Section Overview & Associated Classes
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.sections.edit', $section->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit Section
                    </a>
                    <a href="{{ route('admin.sections.index') }}"
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
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Section
                                    Name</label>
                                <p class="mt-1 text-lg font-semibold">{{ $section->section_name }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Section
                                    Code</label>
                                <p class="mt-1 text-lg font-mono font-bold text-primary">
                                    {{ $section->section_code ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Internal
                                    ID</label>
                                <p class="mt-1 text-lg font-semibold">#{{ $section->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Data Card -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">school</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Associated Academic Classes</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @includeIf('admin.sections.relationships.classSectionAcademicClasses', ['academicClasses' => $section->classSectionAcademicClasses])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection