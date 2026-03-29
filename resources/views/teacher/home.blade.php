@extends('layouts.admin')

@section('style')
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Manrope', sans-serif;
        }

        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
@endsection
@section('content')
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-container": "#949eb8",
                        "secondary-fixed-dim": "#ffb77b",
                        "secondary": "#6366f1",
                        "on-error": "#ffffff",
                        "background": "#f8fafc",
                        "on-tertiary": "#ffffff",
                        "on-secondary-container": "#683700",
                        "on-primary-fixed": "#13183f",
                        "on-primary-fixed-variant": "#3f446d",
                        "on-primary": "#ffffff",
                        "outline-variant": "#c7c5cf",
                        "on-secondary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#77767f",
                        "inverse-surface": "#2e3132",
                        "on-error-container": "#93000a",
                        "on-background": "#1e293b",
                        "inverse-primary": "#818cf8",
                        "secondary-fixed": "#c7d2fe",
                        "on-tertiary-fixed": "#111b30",
                        "surface-container": "#f1f5f9",
                        "tertiary-fixed": "#d8e2ff",
                        "inverse-on-surface": "#f0f1f2",
                        "primary-fixed-dim": "#c7d2fe",
                        "primary-container": "#4338ca",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#e0e7ff",
                        "surface-container-highest": "#e2e8f0",
                        "on-secondary-fixed": "#2e1500",
                        "secondary-container": "#818cf8",
                        "surface-variant": "#e2e8f0",
                        "surface-dim": "#cbd5e1",
                        "on-surface": "#1e293b",
                        "on-surface-variant": "#475569",
                        "surface-container-low": "#f8fafc",
                        "primary": "#4f46e5",
                        "surface-container-high": "#e2e8f0",
                        "surface": "#f8fafc",
                        "primary-fixed": "#e0e7ff",
                        "tertiary": "#1e3a5f",
                        "on-tertiary-fixed-variant": "#3d475e",
                        "tertiary-fixed-dim": "#bcc6e2",
                        "on-secondary-fixed-variant": "#6d3900",
                        "tertiary-container": "#1e3a5f",
                        "surface-tint": "#6366f1",
                        "surface-bright": "#f8fafc",
                        "error": "#ef4444",
                        "success": "#10b981",
                        "accent": "#f59e0b"
                    },
                    fontFamily: {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Manrope"],
                        "label": ["Manrope"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>

    @php
        $teacherName = $teacher ? $teacher->name : 'Teacher';
        $teacherCode = $teacher ? ($teacher->emloyee_code ?? 'N/A') : 'N/A';
        $profileImg = $teacher && $teacher->profile_img ? $teacher->profile_img->thumbnail : null;
        
        $totalStudents = $myBatches->sum(function($batch) {
            return $batch->students ? $batch->students->count() : 0;
        });
        
        $activeBatches = $myBatches->count();
        
        $paidPayments = $paymentHistory->filter(function($p) { return $p->payment_status === 'paid'; });
        $pendingPayments = $paymentHistory->filter(function($p) { return in_array($p->payment_status, ['due', 'pending', 'partial']); });
        
        $totalPaid = $paidPayments->sum('calculated_amount');
        $totalPending = $pendingPayments->sum(function($p) {
            return $p->calculated_amount - $p->paid_amount;
        });
        
        $latestPayment = $paymentHistory->first();
        
        $currentMonth = now()->format('F');
        $currentYear = now()->format('Y');
    @endphp


    <div class="bg-background text-on-background min-h-screen">

        <!-- Main Content Canvas -->
        <main class="p-6 lg:p-10">
            <!-- Dashboard Header & Welcome -->
            <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-secondary font-bold text-sm tracking-widest uppercase font-label">Dashboard
                        Overview</span>
                    <h2 class="text-4xl font-extrabold text-primary font-headline tracking-tight mt-1">Welcome back, 
                        {{ $teacherName }}
                    </h2>
                    <p class="text-on-surface-variant mt-2 text-lg">Your teaching dashboard for managing classes and payments.</p>
                </div>
                <div
                    class="flex items-center gap-3 bg-surface-container-low p-2 rounded-full border border-outline-variant/10 shadow-sm">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-primary-container flex items-center justify-center">
                        @if($profileImg)
                            <img class="w-full h-full object-cover" src="{{ $profileImg }}" alt="{{ $teacherName }}" />
                        @else
                            <span class="material-symbols-outlined text-white">person</span>
                        @endif
                    </div>
                    <div class="pr-4">
                        <p class="text-sm font-bold text-primary">Teacher ID: #{{ $teacherCode }}</p>
                        <p class="text-xs text-secondary font-semibold">{{ $activeBatches }} Active Batches</p>
                    </div>
                </div>
            </header>
            
            <!-- Payment Alert Section -->
            @if($latestPayment)
            <div class="mb-10">
                @php
                    $alertType = $latestPayment->payment_status;
                    $isAlert = in_array($alertType, ['paid']);
                    $isWarning = in_array($alertType, ['partial', 'pending']);
                    $isDue = $alertType === 'due';
                    
                    $alertBg = $isAlert ? 'from-emerald-50 via-teal-50 to-cyan-50' : ($isWarning ? 'from-amber-50 via-orange-50 to-yellow-50' : 'from-red-50 via-rose-50 to-pink-50');
                    $alertBorder = $isAlert ? 'border-emerald-200' : ($isWarning ? 'border-amber-200' : 'border-red-200');
                    $alertText = $isAlert ? 'text-emerald-700' : ($isWarning ? 'text-amber-700' : 'text-red-700');
                    $alertIcon = $isAlert ? 'check_circle' : ($isWarning ? 'schedule' : 'warning');
                @endphp
                <div class="rounded-2xl border {{ $alertBorder }} bg-gradient-to-r {{ $alertBg }} p-6 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full {{ $isAlert ? 'bg-emerald-600/10' : ($isWarning ? 'bg-amber-600/10' : 'bg-red-600/10') }} px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $alertText }}">
                                <span class="material-symbols-outlined text-sm">{{ $alertIcon }}</span>
                                Salary Payment Status
                            </div>
                            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                                @if($isAlert)
                                    Payment Received
                                @elseif($isWarning)
                                    Payment {{ ucfirst($alertType) }}
                                @else
                                    Payment Due
                                @endif
                            </h2>
                            <p class="mt-1 text-slate-600 text-sm">
                                @if($isAlert)
                                    Your latest salary payment has been credited to your account.
                                @elseif($isWarning)
                                    Your salary payment is {{ $alertType }}. Please contact admin for details.
                                @else
                                    Your salary payment is pending. Please follow up with administration.
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-4 shadow-sm">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Amount</p>
                            <p class="text-2xl font-extrabold {{ $isAlert ? 'text-emerald-600' : ($isWarning ? 'text-amber-600' : 'text-red-600') }}">
                                {{ config('panel.currency_symbol', '৳') }}{{ number_format($latestPayment->calculated_amount, 2) }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $latestPayment->month_year_name }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Status</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase {{ $isAlert ? 'bg-emerald-100 text-emerald-700' : ($isWarning ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $latestPayment->payment_status }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Paid Amount</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                {{ config('panel.currency_symbol', '৳') }}{{ number_format($latestPayment->paid_amount, 2) }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Remaining</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                {{ config('panel.currency_symbol', '৳') }}{{ number_format($latestPayment->remaining_amount, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-10">
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 via-gray-50 to-slate-50 p-6 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-slate-600/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-700">
                                <span class="material-symbols-outlined text-sm">info</span>
                                No Payments Yet
                            </div>
                            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                                No Salary Records Found
                            </h2>
                            <p class="mt-1 text-slate-600 text-sm">
                                Your salary payment history will appear here once generated by the admin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Bento Grid: Financials and Key Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                <!-- Financial Summary Card (Wide) -->
                <div
                    class="lg:col-span-8 bg-surface-container-lowest rounded-2xl p-8 shadow-lg relative overflow-hidden border border-slate-200/60">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full -mr-20 -mt-20 pointer-events-none">
                    </div>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 relative z-10">
                        <h3 class="text-xl font-bold text-primary font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
                            Financial Summary
                        </h3>
                        <a href="{{ route('admin.teachers-payments.index') }}"
                            class="text-primary text-sm font-bold border-b-2 border-secondary/30 hover:border-secondary transition-all">View
                            Payments</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                        <div class="space-y-1">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Latest Payment</p>
                            <p class="text-3xl font-extrabold text-primary tracking-tight">{{ config('panel.currency_symbol', '৳') }}{{ number_format($latestPayment ? $latestPayment->calculated_amount : 0, 2) }}</p>
                            @if($latestPayment)
                            <p class="text-xs text-emerald-600 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                {{ $latestPayment->month_year_name }}
                            </p>
                            @endif
                        </div>
                        <div class="space-y-1 md:border-l md:border-outline-variant/20 md:pl-8">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Pending Earnings</p>
                            <p class="text-3xl font-extrabold text-secondary tracking-tight">{{ config('panel.currency_symbol', '৳') }}{{ number_format($totalPending, 2) }}</p>
                            <p class="text-xs text-on-surface-variant font-bold">{{ $pendingPayments->count() }} pending payment(s)</p>
                        </div>
                        <div class="space-y-1 md:border-l md:border-outline-variant/20 md:pl-8">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Total Received ({{ $currentYear }})</p>
                            <p class="text-3xl font-extrabold text-primary tracking-tight">{{ config('panel.currency_symbol', '৳') }}{{ number_format($totalPaid, 0) }}</p>
                            <div class="h-1.5 w-full bg-surface-container-highest rounded-full mt-2 overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-secondary to-primary-container w-3/4 rounded-full">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Stats Card -->
                <div
                    class="lg:col-span-4 bg-gradient-to-br from-primary to-indigo-700 rounded-2xl p-8 text-white flex flex-col justify-between relative overflow-hidden shadow-lg">
                    <div class="absolute inset-0 opacity-10 pointer-events-none"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold font-headline mb-4">Teaching Overview</h3>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">Active Batches</span>
                                <span class="text-xl font-bold bg-white/20 px-3 py-1 rounded-lg">{{ $activeBatches }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">Total Students</span>
                                <span class="text-xl font-bold bg-white/20 px-3 py-1 rounded-lg">{{ $totalStudents }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">This Month</span>
                                <span class="text-xl font-bold bg-white/20 px-3 py-1 rounded-lg">{{ $currentMonth }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
                        <a href="{{ route('admin.teacher.profile') }}"
                            class="w-full bg-white text-primary py-2.5 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-opacity-90 transition-all">
                            <span class="material-symbols-outlined text-sm">person</span>
                            View Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Partial Payments / Transactions Section -->
            @if($partialPayments->count() > 0)
            <div class="mb-10">
                <h3 class="text-xl font-bold text-primary font-headline mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">pending</span>
                    Pending & Partial Payments
                </h3>
                <div class="space-y-4">
                    @foreach($partialPayments as $payment)
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-amber-200/50">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-primary">{{ $payment->month_year_name }}</h4>
                                <p class="text-sm text-on-surface-variant">
                                    Total: {{ config('panel.currency_symbol', '৳') }}{{ number_format($payment->calculated_amount, 2) }} | 
                                    Paid: {{ config('panel.currency_symbol', '৳') }}{{ number_format($payment->paid_amount, 2) }} | 
                                    Remaining: {{ config('panel.currency_symbol', '৳') }}{{ number_format($payment->remaining_amount, 2) }}
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase">
                                {{ $payment->payment_status }}
                            </span>
                        </div>
                        
                        @if($payment->transactions->count() > 0)
                        <div class="mt-4">
                            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Transaction History</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-surface-container-low">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-bold text-primary">Date</th>
                                            <th class="px-4 py-2 text-xs font-bold text-primary">Method</th>
                                            <th class="px-4 py-2 text-xs font-bold text-primary">Reference</th>
                                            <th class="px-4 py-2 text-xs font-bold text-primary">Amount</th>
                                            <th class="px-4 py-2 text-xs font-bold text-primary">Received By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-container-high">
                                        @foreach($payment->transactions as $transaction)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-on-surface-variant">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-on-surface-variant">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-primary">{{ $transaction->reference ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-emerald-600">{{ config('panel.currency_symbol', '৳') }}{{ number_format($transaction->amount, 2) }}</td>
                                            <td class="px-4 py-3 text-sm text-on-surface-variant">{{ $transaction->receivedBy->name ?? 'System' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Two Column Layout: Batches & History -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <!-- Active Batches Column -->
                <div class="xl:col-span-4 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-bold text-primary font-headline">Active Batches</h3>
                        <span class="px-3 py-1 bg-secondary/10 text-secondary text-xs font-bold rounded-full">Weekly
                            Schedule</span>
                    </div>
                    
                    @forelse($myBatches as $index => $batch)
                    @php
                        $borderColors = ['border-secondary', 'border-primary', 'border-accent', 'border-success', 'border-tertiary'];
                        $colorClass = $borderColors[$index % count($borderColors)];
                    @endphp
                    <div
                        class="bg-surface-container-lowest p-5 rounded-xl border-l-4 {{ $colorClass }} shadow-sm hover:translate-x-1 transition-transform cursor-pointer hover:shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-xs font-bold text-on-surface-variant font-label">BATCH #{{ $batch->id }}</p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest bg-primary/5 px-2 py-0.5 rounded text-primary">{{ $batch->subject->name ?? 'N/A' }}</span>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">{{ $batch->batch_name }}</h4>
                        <div class="flex flex-wrap gap-y-2 gap-x-4 mb-3">
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                <span class="text-xs font-semibold">
                                    @if($batch->class_schedule)
                                        {{ implode(', ', array_map(function($day) { return ucfirst(substr($day, 0, 3)); }, array_keys($batch->class_schedule))) }}
                                    @else
                                        No schedule
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">group</span>
                                <span class="text-xs font-semibold">{{ $batch->students->count() ?? 0 }} Students</span>
                            </div>
                        </div>
                        @if($batch->class_schedule)
                        @php
                            $firstDay = array_key_first($batch->class_schedule);
                            $schedule = $batch->class_schedule[$firstDay] ?? null;
                            $time = is_array($schedule) ? ($schedule['time'] ?? '') : $schedule;
                        @endphp
                        @if($time)
                        <div class="flex items-center gap-1.5 text-secondary">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span class="text-xs font-bold">{{ $time }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                    @empty
                    <div class="bg-surface-container-lowest p-8 rounded-xl text-center border border-dashed border-slate-300">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">school</span>
                        <p class="text-on-surface-variant">No batches assigned yet.</p>
                    </div>
                    @endforelse
                </div>
                <!-- Payment History Column -->
                <div class="xl:col-span-8">
                    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                        <div class="p-6 border-b border-surface-container-high flex justify-between items-center">
                            <h3 class="text-xl font-bold text-primary font-headline">Payment History</h3>
                            <div class="flex gap-2">
                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                                    <input
                                        class="bg-surface-container-low border-none rounded-full pl-9 pr-4 py-1.5 text-xs focus:ring-2 focus:ring-primary/20 w-48 font-medium"
                                        placeholder="Search transactions..." type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-surface-container-low">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Reference
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Period
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Amount
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Paid
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container-high">
                                    @forelse($paymentHistory as $payment)
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-5 font-bold text-sm text-primary">#TRX-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-bold text-primary">{{ $payment->month_year_name }}</p>
                                            <p class="text-xs text-on-surface-variant">Salary Payment</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-bold text-primary">{{ config('panel.currency_symbol', '৳') }}{{ number_format($payment->calculated_amount, 2) }}</td>
                                        <td class="px-6 py-5 text-sm font-medium text-on-surface-variant">{{ config('panel.currency_symbol', '৳') }}{{ number_format($payment->paid_amount, 2) }}</td>
                                        <td class="px-6 py-5">
                                            @php
                                                $status = $payment->payment_status;
                                                $statusClass = match($status) {
                                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                                    'partial' => 'bg-amber-100 text-amber-700',
                                                    'pending', 'due' => 'bg-secondary/10 text-secondary',
                                                    default => 'bg-slate-100 text-slate-700'
                                                };
                                            @endphp
                                            <span
                                                class="px-3 py-1 {{ $statusClass }} text-[10px] font-extrabold rounded-full uppercase">{{ $status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">receipt_long</span>
                                            <p class="text-on-surface-variant">No payment history available.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($paymentHistory->count() > 0)
                        <div class="p-6 border-t border-surface-container-high flex items-center justify-between">
                            <p class="text-xs font-bold text-on-surface-variant font-label">Showing {{ $paymentHistory->count() }} transactions
                            </p>
                            <a href="{{ route('admin.teachers-payments.index') }}" class="flex gap-2 text-primary hover:text-secondary transition-colors">
                                <span class="text-xs font-bold">View All</span>
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Footer Accent -->
            <footer class="mt-16 pt-8 border-t border-outline-variant/10 text-center">
                <div class="inline-block bg-white px-8 py-3 rounded-full border border-outline-variant/5 shadow-sm">
                    <p class="text-xs font-bold text-on-surface-variant flex items-center gap-4">
                        <span>Academic Year: <span class="text-secondary">{{ date('Y') }}</span></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span>System Status: <span class="text-emerald-500">Operational</span></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span>Dev Coaching Management</span>
                    </p>
                </div>
            </footer>
        </main>
    </div>
@endsection
