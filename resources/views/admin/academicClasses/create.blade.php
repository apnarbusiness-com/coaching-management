@extends('layouts.admin')
@section('title', 'Academic Classes — Create')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-4xl mx-auto flex flex-col gap-6 pb-12">
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
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Create Class</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Create Academic Class</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Set up a new academic class with sections and shifts.</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.academic-classes.store') }}" enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @csrf
                
                <div class="p-6 md:p-8 space-y-8">
                    <!-- Class Basic Info Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-primary border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <span class="material-symbols-outlined">school</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Class Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- class_name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="class_name">{{ trans('cruds.academicClass.fields.class_name') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('class_name') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="class_name" name="class_name" placeholder="e.g. Class 10" type="text"
                                    value="{{ old('class_name', '') }}" required />
                                @if ($errors->has('class_name'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_name') }}</p>
                                @endif
                            </div>

                            {{-- academic_year --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="academic_year">{{ trans('cruds.academicClass.fields.academic_year') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('academic_year') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="academic_year" name="academic_year" type="date"
                                    value="{{ old('academic_year', '') }}" />
                                @if ($errors->has('academic_year'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('academic_year') }}</p>
                                @endif
                            </div>

                            {{-- class_code --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="class_code">{{ trans('cruds.academicClass.fields.class_code') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('class_code') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="class_code" name="class_code" placeholder="e.g. C10-2024" type="text"
                                    value="{{ old('class_code', '') }}" />
                                @if ($errors->has('class_code'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_code') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sections and Shifts Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-primary border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <span class="material-symbols-outlined">account_tree</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Sections & Shifts</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Class Sections --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="class_sections">
                                        {{ trans('cruds.academicClass.fields.class_section') }}
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button" class="select-all text-[10px] font-bold uppercase tracking-wider text-primary hover:text-blue-600">Select All</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button" class="deselect-all text-[10px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600">Deselect All</button>
                                    </div>
                                </div>
                                <select class="form-control select2 block w-full {{ $errors->has('class_sections') ? 'is-invalid' : '' }}" name="class_sections[]" id="class_sections" multiple>
                                    @foreach($class_sections as $id => $class_section)
                                        <option value="{{ $id }}" {{ in_array($id, old('class_sections', [])) ? 'selected' : '' }}>{{ $class_section }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('class_sections'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_sections') }}</p>
                                @endif
                            </div>

                            {{-- Class Shifts --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="class_shifts">
                                        {{ trans('cruds.academicClass.fields.class_shift') }}
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button" class="select-all text-[10px] font-bold uppercase tracking-wider text-primary hover:text-blue-600">Select All</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button" class="deselect-all text-[10px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600">Deselect All</button>
                                    </div>
                                </div>
                                <select class="form-control select2 block w-full {{ $errors->has('class_shifts') ? 'is-invalid' : '' }}" name="class_shifts[]" id="class_shifts" multiple>
                                    @foreach($class_shifts as $id => $class_shift)
                                        <option value="{{ $id }}" {{ in_array($id, old('class_shifts', [])) ? 'selected' : '' }}>{{ $class_shift }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('class_shifts'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('class_shifts') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700"
                        type="reset">
                        Reset Form
                    </button>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection