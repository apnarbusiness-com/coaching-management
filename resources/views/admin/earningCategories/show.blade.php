@extends('layouts.admin')
@section('title', 'Earning Categories — Details')
@section('content')

    <div class="flex-1 overflow-y-auto bg-[#f8fafc] dark:bg-[#0f172a] transition-colors duration-300">
        <div class="max-w-6xl mx-auto p-4 md:p-8 lg:p-12">
            <!-- Breadcrumbs & Header -->
            <div class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
                <nav
                    class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">
                    <a href="{{ route('admin.home') }}" class="hover:text-primary transition-colors">Dashboard</a>
                    <span class="material-symbols-outlined !text-[14px]">chevron_right</span>
                    <a href="{{ route('admin.earning-categories.index') }}"
                        class="hover:text-primary transition-colors">Earning Categories</a>
                    <span class="material-symbols-outlined !text-[14px]">chevron_right</span>
                    <span class="text-slate-900 dark:text-white">Details</span>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Category <span class="text-primary">Details</span>
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-slate-400 text-lg">
                            View earning category information and related records.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.earning-categories.index') }}"
                            class="px-6 py-3 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-all shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[18px]">arrow_back</span>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-8 animate-in fade-in slide-in-from-bottom-6 duration-1000 delay-200">
                <!-- Category Information Card -->
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

                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- ID -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 !text-[18px]">tag</span>
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            {{ trans('cruds.earningCategory.fields.id') }}
                                        </label>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900 dark:text-white pl-7">
                                        #{{ $earningCategory->id }}
                                    </div>
                                </div>

                                <!-- Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 !text-[18px]">label</span>
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            {{ trans('cruds.earningCategory.fields.name') }}
                                        </label>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900 dark:text-white pl-7">
                                        {{ $earningCategory->name }}
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 !text-[18px]">style</span>
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            {{ trans('cruds.earningCategory.fields.type') }}
                                        </label>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900 dark:text-white pl-7">
                                        @if($earningCategory->type)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-primary/10 text-primary">
                                                {{ $earningCategory->type }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-sm">Not specified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Earnings Section -->
                <div
                    class="bg-white dark:bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-emerald-500">account_balance_wallet</span>
                            </div>
                            {{ trans('global.relatedData') }}
                        </h2>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-slate-200 dark:border-slate-700">
                        <nav class="flex px-6 md:px-8 gap-2" role="tablist" id="relationship-tabs">
                            <button
                                class="relative px-6 py-4 text-sm font-bold text-primary border-b-2 border-primary transition-colors"
                                data-bs-toggle="tab" data-bs-target="#earning_category_earnings" role="tab">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined !text-[18px]">payments</span>
                                    {{ trans('cruds.earning.title') }}
                                </span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content p-6 md:p-8">
                        <div class="tab-pane fade show active" role="tabpanel" id="earning_category_earnings">
                            @includeIf('admin.earningCategories.relationships.earningCategoryEarnings', ['earnings' => $earningCategory->earningCategoryEarnings])
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-12">
                    <a href="{{ route('admin.earning-categories.index') }}"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center border border-slate-200 dark:border-slate-700">
                        <span class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined !text-[18px]">arrow_back</span>
                            Back to List
                        </span>
                    </a>
                    <a href="{{ route('admin.earning-categories.edit', $earningCategory->id) }}"
                        class="w-full sm:w-auto px-10 py-3.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">edit</span>
                        Edit Category
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Bootstrap 5 tab functionality
        document.addEventListener('DOMContentLoaded', function () {
            const triggerTabList = document.querySelectorAll('[data-bs-toggle="tab"]');
            triggerTabList.forEach(triggerEl => {
                const tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', event => {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });
        });
    </script>
@endsection