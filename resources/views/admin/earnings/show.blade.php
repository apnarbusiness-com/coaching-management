@extends('layouts.admin')
@section('title', 'Earnings — Details')
@section('content')

    <!-- Page Scroll Container -->
    <div
        class="flex-1 overflow-y-auto p-6 lg:px-10 lg:py-8 bg-background-light dark:bg-background-dark transition-colors duration-200">
        <div class="max-w-[800px] mx-auto flex flex-col gap-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-sm">
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.home') }}">Dashboard</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.earnings.index') }}">Earnings</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <span class="text-text-main dark:text-white font-medium">Earning Details</span>
            </nav>

            <!-- Page Heading with Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h1 class="text-3xl font-bold text-text-main dark:text-white tracking-tight">
                        Transaction Profile
                    </h1>
                    <p class="text-text-secondary dark:text-gray-400">
                        Reference: <span class="font-mono text-primary">{{ $earning->earning_reference ?? 'N/A' }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.earnings.edit', $earning->id) }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all font-medium text-sm">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        Edit Record
                    </a>
                </div>
            </div>

            <!-- Transaction Profile Card -->
            <div
                class="bg-card-light dark:bg-card-dark rounded-2xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                <!-- Header Section -->
                <div
                    class="p-8 border-b border-border-light dark:border-border-dark flex flex-col md:flex-row gap-8 items-start md:items-center bg-background-light/30 dark:bg-black/10">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-3xl">payments</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h2 class="text-2xl font-bold text-text-main dark:text-white">{{ $earning->title }}</h2>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-green-500/10 text-green-500 border border-green-500/20">
                                COMPLETED
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                            <div class="flex items-center gap-2 text-text-secondary dark:text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">category</span>
                                {{ $earning->earning_category->name ?? 'Uncategorized' }}
                            </div>
                            <div class="flex items-center gap-2 text-text-secondary dark:text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                {{ $earning->earning_date ?? 'No Date' }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-card-light dark:bg-background-dark/50 p-4 rounded-xl border border-border-light dark:border-border-dark min-w-[150px] text-center">
                        <p class="text-xs font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest mb-1">
                            Total Amount</p>
                        <p class="text-2xl font-black text-primary">${{ number_format($earning->amount, 2) }}</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <!-- Student Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest">
                            Student Information</h3>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center text-sm border-b border-border-light/50 dark:border-border-dark py-2">
                                <span class="text-text-secondary dark:text-gray-400">Student ID</span>
                                <span
                                    class="text-text-main dark:text-white font-medium">{{ $earning->student->id_no ?? 'N/A' }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-sm border-b border-border-light/50 dark:border-border-dark py-2">
                                <span class="text-text-secondary dark:text-gray-400">Course / Subject</span>
                                <span
                                    class="text-text-main dark:text-white font-medium">{{ $earning->subject->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm py-2">
                                <span class="text-text-secondary dark:text-gray-400">Academic Background</span>
                                <span
                                    class="text-text-main dark:text-white font-medium">{{ $earning->academic_background ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest">
                            Payment Meta</h3>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center text-sm border-b border-border-light/50 dark:border-border-dark py-2">
                                <span class="text-text-secondary dark:text-gray-400">Method</span>
                                <span
                                    class="text-text-main dark:text-white font-medium">{{ $earning->payment_method ?? 'N/A' }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-sm border-b border-border-light/50 dark:border-border-dark py-2">
                                <span class="text-text-secondary dark:text-gray-400">Reporting Period</span>
                                <span class="text-text-main dark:text-white font-medium">
                                    {{ $earning->earning_month ? DateTime::createFromFormat('!m', $earning->earning_month)->format('F') : 'N/A' }},
                                    {{ $earning->earning_year ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm py-2">
                                <span class="text-text-secondary dark:text-gray-400">Exam Year</span>
                                <span
                                    class="text-text-main dark:text-white font-medium">{{ $earning->exam_year ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payer & Receiver Info -->
                    <div class="md:col-span-2 space-y-4 pt-4 border-t border-border-light dark:border-border-dark">
                        <h3 class="text-sm font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest">
                            Responsibility</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center gap-3 p-4 bg-background-light dark:bg-black/20 rounded-xl">
                                <span class="material-symbols-outlined text-primary">person</span>
                                <div>
                                    <p class="text-xs text-text-secondary dark:text-gray-500">Paid By</p>
                                    <p class="text-sm font-semibold text-text-main dark:text-white">
                                        {{ $earning->paid_by ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-background-light dark:bg-black/20 rounded-xl">
                                <span class="material-symbols-outlined text-primary">assignment_ind</span>
                                <div>
                                    <p class="text-xs text-text-secondary dark:text-gray-500">Recorded By</p>
                                    <p class="text-sm font-semibold text-text-main dark:text-white">
                                        {{ $earning->created_by->name ?? 'System' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details/Notes -->
                    @if ($earning->details)
                        <div class="md:col-span-2 space-y-4 pt-4 border-t border-border-light dark:border-border-dark">
                            <h3 class="text-sm font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest">
                                Transaction Notes</h3>
                            <div
                                class="p-6 bg-background-light dark:bg-black/20 rounded-xl text-sm text-text-main dark:text-gray-300 leading-relaxed prose dark:prose-invert max-w-none">
                                {!! $earning->details !!}
                            </div>
                        </div>
                    @endif

                    <!-- Payment Proof -->
                    @if ($earning->payment_proof->count() > 0)
                        <div class="md:col-span-2 space-y-4 pt-4 border-t border-border-light dark:border-border-dark">
                            <h3 class="text-sm font-bold text-text-secondary dark:text-gray-500 uppercase tracking-widest">
                                Payment Proof</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach ($earning->payment_proof as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                        class="block group relative aspect-square rounded-xl overflow-hidden border border-border-light dark:border-border-dark">
                                        <img src="{{ $media->getUrl('thumb') }}" alt="Proof"
                                            class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white">open_in_new</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Audit Info -->
                <div
                    class="px-8 py-4 bg-background-light/50 dark:bg-black/30 border-t border-border-light dark:border-border-dark flex flex-wrap justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-text-secondary dark:text-gray-500">
                        <span class="material-symbols-outlined text-sm">history</span>
                        Last updated: {{ $earning->updated_at->format('M d, Y H:i') }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-text-secondary dark:text-gray-500">
                        <span class="material-symbols-outlined text-sm">person_edit</span>
                        Updated by: {{ $earning->updated_by->name ?? 'System' }}
                    </div>
                </div>
            </div>

            <!-- Back Action -->
            <div class="flex justify-center pb-8">
                <a href="{{ route('admin.earnings.index') }}"
                    class="text-text-secondary dark:text-gray-400 hover:text-primary flex items-center gap-2 transition-colors font-medium">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Back to Earning List
                </a>
            </div>
        </div>
    </div>

@endsection
