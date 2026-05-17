@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="content">
        {{-- <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        Dashboard
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!
                    </div>
                </div>
            </div>
        </div> --}}




        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-slate-50 dark:bg-background-dark transition-colors duration-300">
            {{-- <div class="mx-auto max-w-7xl flex flex-col gap-8"> --}}
            <div class="mx-auto max-w-none flex flex-col gap-8">
                @php $visibleWidgets = $visibleWidgets ?? []; @endphp

                <!-- Level 1: Hero Cards -->
                @if (in_array('total_profit', $visibleWidgets))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="widget-card" data-widget="total_profit">
                            <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Total Profit</p>
                                        <div class="widget-skeleton"><div class="h-8 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                        <div class="widget-content hidden">
                                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                <strong>৳</strong>
                                                <span data-field="totalProfit">0</span>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-green-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-green-500 text-xl">account_balance_wallet</span>
                                    </div>
                                </div>
                                <div class="widget-content hidden">
                                    <div class="flex flex-col gap-2">
                                        <div class="h-8 w-full flex items-end gap-1" id="profitMiniBars"></div>
                                        <div class="flex items-center gap-1" id="profitTrend"></div>
                                    </div>
                                </div>
                                <div class="widget-skeleton">
                                    <div class="h-8 w-full flex items-end gap-1">
                                        @for ($i = 0; $i < 6; $i++)
                                            <div class="w-1/6 bg-slate-200 dark:bg-slate-700 rounded-t animate-pulse" style="height: {{ rand(30, 80) }}%"></div>
                                        @endfor
                                    </div>
                                    <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- Yearly Transaction -->
                @if (in_array('yearly_earnings', $visibleWidgets) ||
                        in_array('yearly_expenses', $visibleWidgets) ||
                        in_array('yearly_profit', $visibleWidgets))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @if (in_array('yearly_earnings', $visibleWidgets))
                            <div class="widget-card" data-widget="yearly_earnings">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Earning</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisYearEarnings">0</span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-blue-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-blue-500 text-xl">monetization_on</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('yearly_expenses', $visibleWidgets))
                            <div class="widget-card" data-widget="yearly_expenses">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Cost</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisYearExpenses">0</span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-orange-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-orange-500 text-xl">shopping_cart</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('yearly_profit', $visibleWidgets))
                            <div class="widget-card" data-widget="yearly_profit">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Profit</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisYearProfit">0</span>
                                                </h3>
                                                <div class="flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm" data-field="marginIcon">trending_flat</span>
                                                    <span class="text-xs font-semibold" data-field="marginText">0% margin</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-green-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-green-500 text-xl">savings</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Monthly Transaction -->
                @if (in_array('monthly_earnings', $visibleWidgets) ||
                        in_array('monthly_expenses', $visibleWidgets) ||
                        in_array('monthly_profit', $visibleWidgets))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @if (in_array('monthly_earnings', $visibleWidgets))
                            <div class="widget-card" data-widget="monthly_earnings">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Earning</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisMonthEarnings">0</span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-blue-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-blue-500 text-xl">monetization_on</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('monthly_expenses', $visibleWidgets))
                            <div class="widget-card" data-widget="monthly_expenses">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Cost</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisMonthExpenses">0</span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-orange-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-orange-500 text-xl">shopping_cart</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('monthly_profit', $visibleWidgets))
                            <div class="widget-card" data-widget="monthly_profit">
                                <div class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Profit</p>
                                            <div class="widget-skeleton"><div class="h-8 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                                            <div class="widget-content hidden">
                                                <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                                    <strong>৳</strong>
                                                    <span data-field="thisMonthProfit">0</span>
                                                </h3>
                                                <div class="flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm" data-field="monthlyMarginIcon">trending_flat</span>
                                                    <span class="text-xs font-semibold" data-field="monthlyMarginText">0% margin</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-2 bg-green-500/10 rounded-lg">
                                            <span class="material-symbols-outlined text-green-500 text-xl">savings</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif


                <!-- Level 2: Alert Zone -->
                @if (in_array('student_due_alert', $visibleWidgets) || in_array('teacher_payment_alert', $visibleWidgets))
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-slate-900 dark:text-white text-lg font-bold leading-tight px-1">Priority Alerts
                            </h3>
                            <div class="flex items-center gap-2">
                                <select id="alertMonthFilter" class="text-sm border rounded px-2 py-1">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $m)->format('F') }}</option>
                                    @endfor
                                </select>
                                <select id="alertYearFilter" class="text-sm border rounded px-2 py-1">
                                    @for ($y = $currentYear; $y >= $currentYear - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                                <select id="alertBatchFilter" class="text-sm border rounded px-2 py-1">
                                    <option value="">All Batches</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}"
                                            {{ $batch->status == 0 ? 'class="text-red-500"' : '' }}>
                                            {{ $batch->batch_name }} {{ $batch->status == 0 ? '(Inactive)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if (in_array('student_due_alert', $visibleWidgets))
                                <div class="widget-card" data-widget="student_due_alert">
                                    <div class="flex items-center gap-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 relative overflow-hidden group">
                                        <div class="absolute inset-y-0 left-0 w-1 bg-red-500"></div>
                                        <div class="p-3 bg-red-500/20 rounded-full">
                                            <span class="material-symbols-outlined text-red-500">warning</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-slate-900 dark:text-white font-semibold">Student Due Alert</h4>
                                            <div class="widget-skeleton">
                                                <div class="h-4 w-40 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mb-1"></div>
                                                <div class="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                                            </div>
                                            <div class="widget-content hidden">
                                                <p class="text-slate-600 dark:text-[#9da6b9] text-sm">
                                                    <span data-field="overdueCount">0</span> Students have overdue payments.
                                                </p>
                                                <p class="text-xs text-slate-400 mt-1">Total: ৳<span data-field="totalAmount">0</span></p>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">View List</button>
                                    </div>
                                </div>
                            @endif
                            @if (in_array('teacher_payment_alert', $visibleWidgets))
                                <div class="widget-card" data-widget="teacher_payment_alert">
                                    <div class="flex items-center gap-4 p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 relative overflow-hidden group">
                                        <div class="absolute inset-y-0 left-0 w-1 bg-orange-500"></div>
                                        <div class="p-3 bg-orange-500/20 rounded-full">
                                            <span class="material-symbols-outlined text-orange-500">pending_actions</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-slate-900 dark:text-white font-semibold">Teacher Payment Pending</h4>
                                            <div class="widget-skeleton">
                                                <div class="h-4 w-40 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mb-1"></div>
                                                <div class="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                                            </div>
                                            <div class="widget-content hidden">
                                                <p class="text-slate-600 dark:text-[#9da6b9] text-sm">
                                                    <span data-field="pendingCount">0</span> Teachers pending payment.
                                                </p>
                                                <p class="text-xs text-slate-400 mt-1">Total: ৳<span data-field="totalAmount">0</span></p>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">Review</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif







                <!-- Level 3: Charts & Financial Overview -->
                @if (in_array('monthly_revenue_chart', $visibleWidgets) || in_array('financial_overview', $visibleWidgets))
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        @if (in_array('monthly_revenue_chart', $visibleWidgets))
                            <div
                                class="lg:col-span-2 flex flex-col gap-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-slate-900 dark:text-white text-lg font-bold">Monthly Revenue Breakdown
                                    </h3>
                                    <select id="revenueRange"
                                        class="bg-slate-50 dark:bg-[#282e39] border border-slate-200 dark:border-none text-slate-700 dark:text-white text-sm rounded-lg py-1 px-3 focus:ring-0 cursor-pointer">
                                        <option value="6">Last 6 Months</option>
                                        <option value="12">This Year</option>
                                    </select>
                                </div>
                                <div class="relative h-64 w-full">
                                    <canvas id="earningChart"></canvas>
                                </div>
                            </div>
                        @endif

                        {{-- @if (in_array('financial_overview', $visibleWidgets))
                            <div
                                class="flex flex-col gap-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold mb-2">Financial Overview</h3>
                                <div class="flex items-center justify-center py-4 relative">
                                    <div class="size-40 rounded-full"
                                        style="background: conic-gradient(#135bec 0% 65%, #10b981 65% 85%, #f59e0b 85% 100%);">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div
                                            class="size-28 rounded-full bg-white dark:bg-slate-800 flex flex-col items-center justify-center">
                                            <span class="text-slate-500 dark:text-[#9da6b9] text-xs">Total Assets</span>
                                            <span class="text-slate-900 dark:text-white font-bold text-lg">$47k</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3 mt-2">
                                    <div
                                        class="flex items-center justify-between p-3 rounded-lg bg-[#282e39]/50 hover:bg-[#282e39] transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 rounded bg-blue-500/20 text-blue-500"><span
                                                    class="material-symbols-outlined text-sm">account_balance</span></div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-slate-900 dark:text-white text-sm font-medium">Bank</span>
                                                <span class="text-slate-500 dark:text-[#9da6b9] text-xs">City Bank
                                                    Ltd.</span>
                                            </div>
                                        </div>
                                        <span class="text-slate-900 dark:text-white font-bold text-sm">$40,000</span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-3 rounded-lg bg-[#282e39]/50 hover:bg-[#282e39] transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 rounded bg-pink-500/20 text-pink-500"><span
                                                    class="material-symbols-outlined text-sm">qr_code_2</span></div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-slate-900 dark:text-white text-sm font-medium">bKash</span>
                                                <span class="text-slate-500 dark:text-[#9da6b9] text-xs">Merchant
                                                    Acc</span>
                                            </div>
                                        </div>
                                        <span class="text-slate-900 dark:text-white font-bold text-sm">$5,000</span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-[#282e39]/50 hover:bg-slate-100 dark:hover:bg-[#282e39] transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 rounded bg-yellow-500/20 text-yellow-500"><span
                                                    class="material-symbols-outlined text-sm">point_of_sale</span></div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-slate-900 dark:text-white text-sm font-medium">Office</span>
                                                <span class="text-slate-500 dark:text-[#9da6b9] text-xs">Petty Cash</span>
                                            </div>
                                        </div>
                                        <span class="text-slate-900 dark:text-white font-bold text-sm">$2,000</span>
                                    </div>
                                </div>
                            </div>
                         @endif --}}
                    </div>
                @endif

                @if (in_array('total_batches', $visibleWidgets) ||
                        in_array('total_students', $visibleWidgets) ||
                        in_array('total_teachers', $visibleWidgets))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if (in_array('total_batches', $visibleWidgets))
                            <div class="widget-card" data-widget="total_batches">
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <div class="p-3 rounded-full bg-indigo-500/10">
                                        <span class="material-symbols-outlined text-indigo-500 text-2xl">groups</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-xs font-medium uppercase tracking-wide">Total Batches</p>
                                        <div class="widget-skeleton"><div class="h-7 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mt-1"></div></div>
                                        <div class="widget-content hidden">
                                            <h3 class="text-slate-900 dark:text-white text-xl font-bold" data-field="total">0</h3>
                                            <p class="text-xs text-slate-400 mt-0.5"><span data-field="active">0</span> active / <span data-field="inactive">0</span> inactive</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('total_students', $visibleWidgets))
                            <div class="widget-card" data-widget="total_students">
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <div class="p-3 rounded-full bg-blue-500/10">
                                        <span class="material-symbols-outlined text-blue-500 text-2xl">school</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-xs font-medium uppercase tracking-wide">Total Students</p>
                                        <div class="widget-skeleton"><div class="h-7 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mt-1"></div></div>
                                        <div class="widget-content hidden">
                                            <h3 class="text-slate-900 dark:text-white text-xl font-bold" data-field="total">0</h3>
                                            <p class="text-xs text-slate-400 mt-0.5"><span data-field="enrolled_this_month">0</span> enrolled this month</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('total_teachers', $visibleWidgets))
                            <div class="widget-card" data-widget="total_teachers">
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <div class="p-3 rounded-full bg-purple-500/10">
                                        <span class="material-symbols-outlined text-purple-500 text-2xl">badge</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-xs font-medium uppercase tracking-wide">Total Teachers</p>
                                        <div class="widget-skeleton"><div class="h-7 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mt-1"></div></div>
                                        <div class="widget-content hidden">
                                            <h3 class="text-slate-900 dark:text-white text-xl font-bold" data-field="total">0</h3>
                                            <p class="text-xs text-slate-400 mt-0.5"><span data-field="active">0</span> active</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if (in_array('attendance_overview', $visibleWidgets))
                    <div class="widget-card" data-widget="attendance_overview">
                        <div class="flex flex-col gap-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold">Attendance Overview</h3>
                                <span class="text-xs text-slate-400">This Year</span>
                            </div>
                            <div class="widget-skeleton"><div class="h-16 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                            <div class="widget-content hidden">
                                <div class="flex items-center gap-6">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-slate-900 dark:text-white" data-field="percentage">0<span class="text-lg">%</span></div>
                                        <p class="text-xs text-slate-400 mt-1">Attendance</p>
                                    </div>
                                    <div class="flex-1 h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-700" data-field="bar" style="width: 0%; background: #135bec;"></div>
                                    </div>
                                    <div class="text-center text-sm">
                                        <div><span class="font-semibold text-green-600" data-field="present">0</span> <span class="text-slate-400">Present</span></div>
                                        <div><span class="font-semibold text-red-500" data-field="absent">0</span> <span class="text-slate-400">Absent</span></div>
                                        <div><span class="font-semibold text-amber-500" data-field="late">0</span> <span class="text-slate-400">Late</span></div>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-2">Total records: <span data-field="total">0</span></p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array('monthly_earnings_table', $visibleWidgets))
                    <div class="widget-card" data-widget="monthly_earnings_table">
                        <div class="flex flex-col gap-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold">Monthly Earnings Breakdown</h3>
                                <span class="text-xs text-slate-400">{{ date('Y') }}</span>
                            </div>
                            <div class="widget-skeleton"><div class="h-40 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                            <div class="widget-content hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-left text-slate-500 dark:text-[#9da6b9] uppercase text-xs">
                                                <th class="pb-2 font-medium">Month</th>
                                                <th class="pb-2 font-medium text-right">Earnings (Tk)</th>
                                                <th class="pb-2 font-medium text-right">% of Year</th>
                                            </tr>
                                        </thead>
                                        <tbody id="monthlyEarningsBody" class="text-slate-700 dark:text-white"></tbody>
                                        <tfoot>
                                            <tr class="border-t border-slate-200 dark:border-slate-700 font-semibold">
                                                <td class="pt-2 text-slate-900 dark:text-white">Total</td>
                                                <td class="pt-2 text-right text-slate-900 dark:text-white" id="monthlyEarningsGrandTotal">0</td>
                                                <td class="pt-2 text-right text-slate-900 dark:text-white">100%</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array('recent_transactions', $visibleWidgets))
                    <div class="widget-card" data-widget="recent_transactions">
                        <div class="flex flex-col gap-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold">Recent Transactions</h3>
                            </div>
                            <div class="widget-skeleton"><div class="h-32 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div></div>
                            <div class="widget-content hidden">
                                <div class="divide-y divide-slate-100 dark:divide-slate-700" id="recentTransactionsList"></div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </main>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Lazy load widgets
            function loadWidgetData(widgetKey, callback) {
                $.get("{{ route('admin.widget-data', ':widget') }}".replace(':widget', widgetKey), function(data) {
                    callback(data);
                    $('.widget-card[data-widget="' + widgetKey + '"]').each(function() {
                        $(this).find('.widget-skeleton').hide();
                        $(this).find('.widget-content').removeClass('hidden');
                    });
                }).fail(function() {
                    $('.widget-card[data-widget="' + widgetKey + '"]').each(function() {
                        $(this).find('.widget-skeleton').html('<span class="text-red-400 text-sm">Failed to load</span>');
                    });
                });
            }

            function formatTk(n) {
                return parseFloat(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            // Total Batches
            loadWidgetData('total_batches', function(d) {
                var card = $('.widget-card[data-widget="total_batches"]');
                card.find('[data-field="total"]').text(d.total);
                card.find('[data-field="active"]').text(d.active);
                card.find('[data-field="inactive"]').text(d.inactive);
            });

            // Total Students
            loadWidgetData('total_students', function(d) {
                var card = $('.widget-card[data-widget="total_students"]');
                card.find('[data-field="total"]').text(d.total);
                card.find('[data-field="enrolled_this_month"]').text(d.enrolled_this_month);
            });

            // Total Teachers
            loadWidgetData('total_teachers', function(d) {
                var card = $('.widget-card[data-widget="total_teachers"]');
                card.find('[data-field="total"]').text(d.total);
                card.find('[data-field="active"]').text(d.active);
            });

            // Attendance Overview
            loadWidgetData('attendance_overview', function(d) {
                var card = $('.widget-card[data-widget="attendance_overview"]');
                card.find('[data-field="total"]').text(d.total);
                card.find('[data-field="present"]').text(d.present);
                card.find('[data-field="absent"]').text(d.absent);
                card.find('[data-field="late"]').text(d.late);
                card.find('[data-field="percentage"]').html(d.percentage + '<span class="text-lg">%</span>');
                var barColor = d.percentage >= 80 ? '#22c55e' : (d.percentage >= 50 ? '#f59e0b' : '#ef4444');
                card.find('[data-field="bar"]').css({width: d.percentage + '%', background: barColor});
            });

            // Monthly Earnings Table
            loadWidgetData('monthly_earnings_table', function(d) {
                var tbody = $('#monthlyEarningsBody');
                tbody.empty();
                var gt = d.grand_total || 0;
                d.months.forEach(function(m) {
                    var pct = gt > 0 ? ((m.total / gt) * 100).toFixed(1) : 0;
                    tbody.append('<tr class="border-b border-slate-100 dark:border-slate-700/50">' +
                        '<td class="py-1.5">' + m.month + '</td>' +
                        '<td class="py-1.5 text-right font-mono">' + formatTk(m.total) + '</td>' +
                        '<td class="py-1.5 text-right text-slate-400">' + pct + '%</td></tr>');
                });
                $('#monthlyEarningsGrandTotal').text(formatTk(gt));
            });

            // Recent Transactions
            loadWidgetData('recent_transactions', function(d) {
                var list = $('#recentTransactionsList');
                list.empty();
                if (!d.transactions || d.transactions.length === 0) {
                    list.html('<p class="text-slate-400 text-sm py-4 text-center">No transactions yet.</p>');
                    return;
                }
                d.transactions.forEach(function(tx) {
                    var isEarning = tx.type === 'earning';
                    var icon = isEarning ? 'monetization_on' : 'money_off';
                    var color = isEarning ? 'text-green-500' : 'text-red-500';
                    var bg = isEarning ? 'bg-green-500/10' : 'bg-red-500/10';
                    var sign = isEarning ? '+' : '-';
                    list.append('<div class="flex items-center gap-3 py-3">' +
                        '<div class="p-2 rounded-full ' + bg + '">' +
                            '<span class="material-symbols-outlined text-sm ' + color + '">' + icon + '</span>' +
                        '</div>' +
                        '<div class="flex-1 min-w-0">' +
                            '<p class="text-sm font-medium text-slate-700 dark:text-white truncate">' + tx.title + '</p>' +
                            '<p class="text-xs text-slate-400">' + tx.category + ' &middot; ' + (tx.date || '') + '</p>' +
                        '</div>' +
                        '<div class="text-sm font-mono font-semibold ' + color + '">' + sign + formatTk(tx.amount) + '</div>' +
                    '</div>');
                });
            });

            // Total Profit (with mini bar chart + trend)
            loadWidgetData('total_profit', function(d) {
                var card = $('.widget-card[data-widget="total_profit"]');
                card.find('[data-field="totalProfit"]').text(parseFloat(d.totalProfit).toLocaleString(undefined, {minimumFractionDigits: 2}));

                var bars = '';
                var max = d.maxEarning || 1;
                d.earnings.forEach(function(e) {
                    var h = (e.total / max) * 100;
                    bars += '<div class="w-1/6 bg-green-500 rounded-t transition-all" style="height:' + h + '%" title="' + e.month + ' : ৳' + e.total.toLocaleString() + '"></div>';
                });
                $('#profitMiniBars').html(bars);

                var trendIcon = d.growthTrend === 'up' ? 'trending_up' : (d.growthTrend === 'down' ? 'trending_down' : 'trending_flat');
                var trendColor = d.growthTrend === 'up' ? 'text-green-500' : (d.growthTrend === 'down' ? 'text-red-500' : 'text-gray-400');
                $('#profitTrend').html(
                    '<span class="material-symbols-outlined ' + trendColor + ' text-sm">' + trendIcon + '</span>' +
                    '<span class="' + trendColor + ' text-xs font-semibold">' + d.growthPercentage + '%</span>' +
                    '<span class="text-[#9da6b9] text-xs ml-1">vs last month</span>'
                );
            });

            // Yearly Earning
            loadWidgetData('yearly_earnings', function(d) {
                $('.widget-card[data-widget="yearly_earnings"] [data-field="thisYearEarnings"]')
                    .text(parseFloat(d.thisYearEarnings).toLocaleString(undefined, {minimumFractionDigits: 2}));
            });

            // Yearly Cost
            loadWidgetData('yearly_expenses', function(d) {
                $('.widget-card[data-widget="yearly_expenses"] [data-field="thisYearExpenses"]')
                    .text(parseFloat(d.thisYearExpenses).toLocaleString(undefined, {minimumFractionDigits: 2}));
            });

            // Yearly Profit + margin
            loadWidgetData('yearly_profit', function(d) {
                var card = $('.widget-card[data-widget="yearly_profit"]');
                card.find('[data-field="thisYearProfit"]').text(parseFloat(d.thisYearProfit).toLocaleString(undefined, {minimumFractionDigits: 2}));
                var margin = d.thisYearProfitMargin || 0;
                var icon = margin > 0 ? 'trending_up' : (margin < 0 ? 'trending_down' : 'trending_flat');
                var color = margin > 0 ? 'text-green-500' : (margin < 0 ? 'text-red-500' : 'text-gray-400');
                card.find('[data-field="marginIcon"]').text(icon).removeClass().addClass('material-symbols-outlined text-sm ' + color);
                card.find('[data-field="marginText"]').text(margin + '% margin').removeClass().addClass('text-xs font-semibold ' + color);
            });

            // Monthly Earning
            loadWidgetData('monthly_earnings', function(d) {
                $('.widget-card[data-widget="monthly_earnings"] [data-field="thisMonthEarnings"]')
                    .text(parseFloat(d.thisMonthEarnings).toLocaleString(undefined, {minimumFractionDigits: 2}));
            });

            // Monthly Cost
            loadWidgetData('monthly_expenses', function(d) {
                $('.widget-card[data-widget="monthly_expenses"] [data-field="thisMonthExpenses"]')
                    .text(parseFloat(d.thisMonthExpenses).toLocaleString(undefined, {minimumFractionDigits: 2}));
            });

            // Monthly Profit + margin
            loadWidgetData('monthly_profit', function(d) {
                var card = $('.widget-card[data-widget="monthly_profit"]');
                card.find('[data-field="thisMonthProfit"]').text(parseFloat(d.thisMonthProfit).toLocaleString(undefined, {minimumFractionDigits: 2}));
                var margin = d.thisMonthProfitMargin || 0;
                var icon = margin > 0 ? 'trending_up' : (margin < 0 ? 'trending_down' : 'trending_flat');
                var color = margin > 0 ? 'text-green-500' : (margin < 0 ? 'text-red-500' : 'text-gray-400');
                card.find('[data-field="monthlyMarginIcon"]').text(icon).removeClass().addClass('material-symbols-outlined text-sm ' + color);
                card.find('[data-field="monthlyMarginText"]').text(margin + '% margin').removeClass().addClass('text-xs font-semibold ' + color);
            });

            // Student Due Alert
            loadWidgetData('student_due_alert', function(d) {
                var card = $('.widget-card[data-widget="student_due_alert"]');
                card.find('[data-field="overdueCount"]').text(d.overdueCount);
                card.find('[data-field="totalAmount"]').text(parseFloat(d.totalAmount).toLocaleString());
            });

            // Teacher Payment Alert
            loadWidgetData('teacher_payment_alert', function(d) {
                var card = $('.widget-card[data-widget="teacher_payment_alert"]');
                card.find('[data-field="pendingCount"]').text(d.pendingCount);
                card.find('[data-field="totalAmount"]').text(parseFloat(d.totalAmount).toLocaleString());
            });

            // Revenue Chart (existing AJAX)
            if ($('#earningChart').length) {
                let earningChart = null;

                function loadRevenueChart(months) {
                    $.ajax({
                        url: "{{ route('admin.monthly.revenue', ':months') }}".replace(':months', months),
                        type: "GET",
                        dataType: "json",
                        success: function(res) {
                            if (earningChart) earningChart.destroy();
                            var ctx = document.getElementById('earningChart').getContext('2d');
                            earningChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: res.labels,
                                    datasets: [{
                                        label: 'Earnings',
                                        data: res.data,
                                        backgroundColor: '#135bec',
                                        borderRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: true },
                                        tooltip: {
                                            callbacks: {
                                                label: function(ctx) {
                                                    return '৳ ' + ctx.raw.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: { precision: 0 }
                                        }
                                    }
                                }
                            });
                        },
                        error: function() {
                            console.error('Failed to load revenue data');
                        }
                    });
                }

                loadRevenueChart($('#revenueRange').val());
                $('#revenueRange').on('change', function() {
                    loadRevenueChart($(this).val());
                });
            }

            // Alert Filters (uses dedicated endpoint with filter params)
            function loadAlertData() {
                $.get("{{ route('admin.alert-data') }}", {
                    month: $('#alertMonthFilter').val(),
                    year: $('#alertYearFilter').val(),
                    batch_id: $('#alertBatchFilter').val()
                }, function(response) {
                    var dueCard = $('.widget-card[data-widget="student_due_alert"]');
                    dueCard.find('[data-field="overdueCount"]').text(response.student_alerts.count);
                    dueCard.find('[data-field="totalAmount"]').text(parseFloat(response.student_alerts.totalAmount).toLocaleString());

                    var teacherCard = $('.widget-card[data-widget="teacher_payment_alert"]');
                    teacherCard.find('[data-field="pendingCount"]').text(response.teacher_alerts.count);
                    teacherCard.find('[data-field="totalAmount"]').text(parseFloat(response.teacher_alerts.totalAmount).toLocaleString());
                });
            }

            $('#alertMonthFilter, #alertYearFilter, #alertBatchFilter').on('change', loadAlertData);
            loadAlertData();

        });
    </script>
@endsection
