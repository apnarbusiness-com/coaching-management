@extends('layouts.admin')
@section('title', 'Academic Backgrounds — Details')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $academicBackground->name }}
                    </h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">
                        {{ trans('cruds.academicBackground.title_singular') }} Details
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.academic-backgrounds.edit', $academicBackground->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit
                    </a>
                    <a href="{{ route('admin.academic-backgrounds.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                        Back to List
                    </a>
                </div>
            </div>

            <div
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined font-bold">info</span>
                    <h3 class="text-lg font-bold">Basic Information</h3>
                </div>
                <div class="p-6 md:p-8 space-y-5">
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            {{ trans('cruds.academicBackground.fields.name') }}
                        </label>
                        <p class="mt-1 text-lg font-semibold">{{ $academicBackground->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            {{ trans('cruds.academicBackground.fields.id') }}
                        </label>
                        <p class="mt-1 text-lg font-semibold">#{{ $academicBackground->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
