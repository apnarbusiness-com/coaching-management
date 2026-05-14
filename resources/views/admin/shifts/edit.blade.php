@extends('layouts.admin')
@section('title', 'Shifts — Edit')
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
                                href="{{ route('admin.shifts.index') }}">Shifts</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Edit Shift</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Shift</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Update shift details like name, code, and timing.</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.shifts.update', [$shift->id]) }}" enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @method('PUT')
                @csrf
                
                <div class="p-6 md:p-8 space-y-8">
                    <!-- Shift Basic Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-primary border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <span class="material-symbols-outlined">schedule</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Shift Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- shift_name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="shift_name">{{ trans('cruds.shift.fields.shift_name') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('shift_name') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="shift_name" name="shift_name" placeholder="e.g. Morning Shift" type="text"
                                    value="{{ old('shift_name', $shift->shift_name) }}" required />
                                @if ($errors->has('shift_name'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('shift_name') }}</p>
                                @endif
                                <span class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.shift.fields.shift_name_helper') }}</span>
                            </div>

                            {{-- shift_code --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="shift_code">{{ trans('cruds.shift.fields.shift_code') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('shift_code') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="shift_code" name="shift_code" placeholder="e.g. MORN" type="text"
                                    value="{{ old('shift_code', $shift->shift_code) }}" />
                                @if ($errors->has('shift_code'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('shift_code') }}</p>
                                @endif
                                <span class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.shift.fields.shift_code_helper') }}</span>
                            </div>

                            {{-- shift_time --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="shift_time">{{ trans('cruds.shift.fields.shift_time') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('shift_time') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="shift_time" name="shift_time" placeholder="e.g. 8:00 AM - 1:00 PM" type="text"
                                    value="{{ old('shift_time', $shift->shift_time) }}" />
                                @if ($errors->has('shift_time'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('shift_time') }}</p>
                                @endif
                                <span class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.shift.fields.shift_time_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700"
                        type="button" onclick="window.history.back()">
                        Cancel
                    </button>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Update Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection