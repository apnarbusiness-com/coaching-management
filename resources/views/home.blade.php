@extends('layouts.admin')
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
                        <!-- Total Transaction -->
                        <div
                            class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col gap-1">
                                    <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Total Profit</p>
                                    <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                        <strong>৳</strong>
                                        {{ number_format($transactionData['totalProfit'] ?? 0, 2) }}
                                    </h3>
                                </div>
                                <div class="p-2 bg-green-500/10 rounded-lg">
                                    <span
                                        class="material-symbols-outlined text-green-500 text-xl">account_balance_wallet</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="h-8 w-full flex items-end gap-1">
                                    @foreach ($lastSixMonthsData['earnings'] as $earning)
                                        @php
                                            $height = ($earning['total'] / $lastSixMonthsData['maxEarning']) * 100;
                                        @endphp
                                        <div class="w-1/6 bg-green-500 rounded-t transition-all"
                                            style="height: {{ $height }}%"
                                            title="{{ $earning['month'] }} : {{ number_format($earning['total']) }}"></div>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-1">
                                    @php
                                        $icon = match ($lastSixMonthsData['growthTrend']) {
                                            'up' => 'trending_up',
                                            'down' => 'trending_down',
                                            default => 'trending_flat',
                                        };
                                        $color = match ($lastSixMonthsData['growthTrend']) {
                                            'up' => 'text-green-500',
                                            'down' => 'text-red-500',
                                            default => 'text-gray-400',
                                        };
                                    @endphp
                                    <span class="material-symbols-outlined {{ $color }} text-sm">
                                        {{ $icon }}
                                    </span>
                                    <span class="{{ $color }} text-xs font-semibold">
                                        {{ $lastSixMonthsData['growthPercentage'] }}%
                                    </span>
                                    <span class="text-[#9da6b9] text-xs ml-1">vs last month</span>
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
                            <!-- Yearly Earning -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Earning</p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            {{-- $85,000 --}}
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisYearEarnings'], 2) }}
                                        </h3>
                                    </div>
                                    <div class="p-2 bg-blue-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-blue-500 text-xl">monetization_on</span>
                                    </div>
                                </div>
                                {{-- <div class="flex flex-col gap-2">
                            <div class="w-full bg-slate-100 dark:bg-[#282e39] rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 80%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-[#9da6b9] text-xs">Goal Progress</span>
                                <span class="text-blue-500 text-xs font-semibold">80%</span>
                            </div>
                        </div> --}}
                            </div>
                        @endif
                        @if (in_array('yearly_expenses', $visibleWidgets))
                            <!-- Yearly Cost -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Cost</p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisYearExpenses'] ?? 0, 2) }}
                                        </h3>
                                    </div>
                                    <div class="p-2 bg-orange-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-orange-500 text-xl">shopping_cart</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('yearly_profit', $visibleWidgets))
                            <!-- Yearly Profit -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Yearly Profit</p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisYearProfit'] ?? 0, 2) }}
                                        </h3>
                                        <div class="flex items-center gap-1">
                                            @php
                                                $margin = $transactionData['thisYearProfitMargin'] ?? 0;
                                                if ($margin > 0) {
                                                    $marginColor = 'green';
                                                    $marginIcon = 'trending_up';
                                                } elseif ($margin < 0) {
                                                    $marginColor = 'red';
                                                    $marginIcon = 'trending_down';
                                                } else {
                                                    $marginColor = 'gray';
                                                    $marginIcon = 'trending_flat';
                                                }
                                            @endphp
                                            <span class="material-symbols-outlined text-{{ $marginColor }}-500 text-sm">
                                                {{ $marginIcon }}
                                            </span>
                                            <span class="text-{{ $marginColor }}-500 text-xs font-semibold">
                                                {{ $margin }}% margin
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-green-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-green-500 text-xl">savings</span>
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
                            <!-- Monthly Earning -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Earning
                                        </p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisMonthEarnings'] ?? 0, 2) }}
                                        </h3>
                                    </div>
                                    <div class="p-2 bg-blue-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-blue-500 text-xl">monetization_on</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('monthly_expenses', $visibleWidgets))
                            <!-- Monthly Cost -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Cost</p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisMonthExpenses'] ?? 0, 2) }}
                                        </h3>
                                    </div>
                                    <div class="p-2 bg-orange-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-orange-500 text-xl">shopping_cart</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('monthly_profit', $visibleWidgets))
                            <!-- Monthly Profit -->
                            <div
                                class="flex flex-col justify-between gap-4 rounded-xl p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-slate-500 dark:text-[#9da6b9] text-sm font-medium">Monthly Profit</p>
                                        <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                            <strong>৳</strong>
                                            {{ number_format($transactionData['thisMonthProfit'] ?? 0, 2) }}
                                        </h3>
                                        <div class="flex items-center gap-1">
                                            @php
                                                $margin = $transactionData['thisMonthProfitMargin'] ?? 0;
                                                if ($margin > 0) {
                                                    $marginColor = 'green';
                                                    $marginIcon = 'trending_up';
                                                } elseif ($margin < 0) {
                                                    $marginColor = 'red';
                                                    $marginIcon = 'trending_down';
                                                } else {
                                                    $marginColor = 'gray';
                                                    $marginIcon = 'trending_flat';
                                                }
                                            @endphp
                                            <span class="material-symbols-outlined text-{{ $marginColor }}-500 text-sm">
                                                {{ $marginIcon }}
                                            </span>
                                            <span class="text-{{ $marginColor }}-500 text-xs font-semibold">
                                                {{ $margin }}% margin
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-green-500/10 rounded-lg">
                                        <span class="material-symbols-outlined text-green-500 text-xl">savings</span>
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
                                <!-- Student Due Alert -->
                                <div
                                    class="flex items-center gap-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 relative overflow-hidden group">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-red-500"></div>
                                    <div class="p-3 bg-red-500/20 rounded-full">
                                        <span class="material-symbols-outlined text-red-500">warning</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-slate-900 dark:text-white font-semibold">Student Due Alert</h4>
                                        <p class="text-slate-600 dark:text-[#9da6b9] text-sm">
                                            <span id="studentDueCount">{{ $studentAlerts['overdueCount'] ?? 0 }}</span>
                                            Students have overdue payments.
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">Total: <span id="studentDueAmount">৳0</span>
                                        </p>
                                    </div>
                                    <button
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">View
                                        List</button>
                                </div>
                            @endif
                            @if (in_array('teacher_payment_alert', $visibleWidgets))
                                <!-- Teacher Payment Pending -->
                                <div
                                    class="flex items-center gap-4 p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 relative overflow-hidden group">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-orange-500"></div>
                                    <div class="p-3 bg-orange-500/20 rounded-full">
                                        <span class="material-symbols-outlined text-orange-500">pending_actions</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-slate-900 dark:text-white font-semibold">Teacher Payment Pending
                                        </h4>
                                        <p class="text-slate-600 dark:text-[#9da6b9] text-sm">
                                            <span
                                                id="teacherPaymentCount">{{ $teacherPaymentAlerts['pendingCount'] ?? 0 }}</span>
                                            Teachers pending payment.
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">Total: <span
                                                id="teacherPaymentAmount">৳0</span></p>
                                    </div>
                                    <button
                                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">Review</button>
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

            </div>
        </main>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {


            if (!$('#earningChart').length) {
                return;
            }

            let earningChart = null;

            // initial load (default selected value)
            loadRevenueChart($('#revenueRange').val());

            // on dropdown change
            $('#revenueRange').on('change', function() {
                loadRevenueChart($(this).val());
            });

            function loadRevenueChart(months) {

                $.ajax({
                    url: "{{ route('admin.monthly.revenue', ':months') }}".replace(':months', months),
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        renderEarningChart(res.labels, res.data);
                    },
                    error: function() {
                        console.error('Failed to load revenue data');
                    }
                });
            }

            function renderEarningChart(labels, data) {

                if (earningChart) {
                    earningChart.destroy(); // 🔥 important
                }

                let ctx = document.getElementById('earningChart').getContext('2d');

                earningChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Earnings',
                            data: data,
                            backgroundColor: '#135bec',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            },
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
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Alert Filters
            function loadAlertData() {
                const month = $('#alertMonthFilter').val();
                const year = $('#alertYearFilter').val();
                const batchId = $('#alertBatchFilter').val();

                $.get("{{ route('admin.alert-data') }}", {
                    month: month,
                    year: year,
                    batch_id: batchId
                }, function(response) {
                    $('#studentDueCount').text(response.student_alerts.count);
                    $('#studentDueAmount').text('৳' + parseFloat(response.student_alerts
                        .totalAmount).toLocaleString());
                    $('#teacherPaymentCount').text(response.teacher_alerts.count);
                    $('#teacherPaymentAmount').text('৳' + parseFloat(response
                        .teacher_alerts.totalAmount).toLocaleString());
                });
            }

            $('#alertMonthFilter, #alertYearFilter, #alertBatchFilter').on('change',
                function() {
                    loadAlertData();
                });

            // Initial load
            loadAlertData();

        });
    </script>
@endsection
