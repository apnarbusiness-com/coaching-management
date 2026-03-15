@extends('layouts.admin')
@section('content')

    <div class="flex-1 overflow-y-auto bg-[#f8fafc] dark:bg-[#0f172a] transition-colors duration-300">
        <div class="max-w-4xl mx-auto p-4 md:p-8 lg:p-12">
            <!-- Breadcrumbs & Header -->
            <div class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
                <nav
                    class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">
                    <a href="{{ route('admin.home') }}" class="hover:text-primary transition-colors">Dashboard</a>
                    <span class="material-symbols-outlined !text-[14px]">chevron_right</span>
                    <a href="{{ route('admin.earning-categories.index') }}"
                        class="hover:text-primary transition-colors">Earning Categories</a>
                    <span class="material-symbols-outlined !text-[14px]">chevron_right</span>
                    <span class="text-slate-900 dark:text-white">Edit</span>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Edit <span class="text-primary">Earning Category</span>
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-slate-400 text-lg">
                            Update category details for revenue organization.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.earning-categories.update', [$earningCategory->id]) }}" method="POST"
                enctype="multipart/form-data"
                class="space-y-8 animate-in fade-in slide-in-from-bottom-6 duration-1000 delay-200">
                @method('PUT')
                @csrf

                <!-- Section: Category Details -->
                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-primary/20 to-purple-500/20 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000">
                    </div>
                    <div
                        class="relative bg-white dark:bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">category</span>
                                </div>
                                Category Information
                            </h2>
                        </div>

                        <div class="p-6 md:p-8 space-y-6">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label
                                    class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
                                    for="name">
                                    {{ trans('cruds.earningCategory.fields.name') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group/input">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 !text-[20px] group-focus-within/input:text-primary transition-colors">label</span>
                                    <input
                                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-xl py-3 pl-12 pr-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all {{ $errors->has('name') ? 'ring-2 ring-red-500 border-transparent' : '' }}"
                                        type="text" name="name" id="name" value="{{ old('name', $earningCategory->name) }}"
                                        placeholder="e.g. Tuition Fee, Exam Fee, Admission Fee" required>
                                </div>
                                @if($errors->has('name'))
                                    <p class="text-xs font-medium text-red-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-[14px]">error</span>
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                                @if(trans('cruds.earningCategory.fields.name_helper'))
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ trans('cruds.earningCategory.fields.name_helper') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Type -->
                            <div class="space-y-2">
                                <label
                                    class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
                                    for="type">
                                    {{ trans('cruds.earningCategory.fields.type') }}
                                </label>
                                <div class="relative group/input">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 !text-[20px] group-focus-within/input:text-primary transition-colors">style</span>
                                    <input
                                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-xl py-3 pl-12 pr-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all {{ $errors->has('type') ? 'ring-2 ring-red-500 border-transparent' : '' }}"
                                        type="text" name="type" id="type" value="{{ old('type', $earningCategory->type) }}"
                                        placeholder="e.g. Monthly, One-time, Annual">
                                </div>
                                @if($errors->has('type'))
                                    <p class="text-xs font-medium text-red-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-[14px]">error</span>
                                        {{ $errors->first('type') }}
                                    </p>
                                @endif
                                @if(trans('cruds.earningCategory.fields.type_helper'))
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ trans('cruds.earningCategory.fields.type_helper') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Student Connected -->
                            <div class="space-y-2">
                                <label
                                    class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
                                    for="is_student_connected">
                                    Student Connected?
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="hidden" name="is_student_connected" value="0">
                                    <input
                                        class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary"
                                        type="checkbox" name="is_student_connected" id="is_student_connected" value="1"
                                        {{ old('is_student_connected', $earningCategory->is_student_connected) ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">
                                        Requires student info when recording earnings for this category.
                                    </span>
                                </div>
                                @if($errors->has('is_student_connected'))
                                    <p class="text-xs font-medium text-red-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-[14px]">error</span>
                                        {{ $errors->first('is_student_connected') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Info Box -->
                            <div
                                class="p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                                <p class="text-xs text-amber-700 dark:text-amber-400 font-medium leading-relaxed">
                                    <span class="material-symbols-outlined !text-[14px] align-middle mr-1">info</span>
                                    Changes to this category will affect all associated earnings records.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pb-12">
                    <a href="{{ route('admin.earning-categories.index') }}"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center">
                        Cancel
                    </a>
                    <button
                        class="w-full sm:w-auto px-10 py-3.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 transition-all transform active:scale-95 flex items-center justify-center gap-3"
                        type="submit">
                        <span class="material-symbols-outlined">save</span>
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
