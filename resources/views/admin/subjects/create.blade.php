@extends('layouts.admin')
@section('title', 'Subjects — Create')
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
                                href="{{ route('admin.subjects.index') }}">Subjects</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">
                                Create Subject</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Create Subject</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Add a new Subject to the curriculum.</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.subjects.store') }}" enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @csrf

                <div class="p-6 md:p-8 space-y-8">
                    <!-- Subject Basic Info -->
                    <div class="space-y-6">
                        <div
                            class="flex items-center gap-2 text-primary border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <span class="material-symbols-outlined font-bold">book</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Subject Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="name">{{ trans('cruds.subject.fields.name') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('name') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="name" name="name" placeholder="e.g. Mathematics" type="text"
                                    value="{{ old('name', '') }}" required />
                                @if ($errors->has('name'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.subject.fields.name_helper') }}</span>
                            </div>

                            {{-- code --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="code">{{ trans('cruds.subject.fields.code') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('code') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="code" name="code" placeholder="e.g. MATH-101" type="text"
                                    value="{{ old('code', '') }}" />
                                @if ($errors->has('code'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('code') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.subject.fields.code_helper') }}</span>
                            </div>

                            {{-- monthly_fee --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="monthly_fee">{{ trans('cruds.subject.fields.monthly_fee') }}</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm symbol-of-tk">৳</span>
                                    </div>
                                    <input
                                        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 pl-7 pr-3 {{ $errors->has('monthly_fee') ? 'border-red-500 ring-red-500' : '' }}"
                                        id="monthly_fee" name="monthly_fee" placeholder="0.00" type="number" step="0.01"
                                        value="{{ old('monthly_fee', '') }}" />
                                </div>
                                @if ($errors->has('monthly_fee'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('monthly_fee') }}
                                    </p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.subject.fields.monthly_fee_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700"
                        type="reset">
                        Reset Form
                    </button>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection