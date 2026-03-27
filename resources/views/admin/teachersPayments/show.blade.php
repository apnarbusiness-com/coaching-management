@extends('layouts.admin')
@section('content')

    <!-- Page Scroll Container -->
    <div
        class="flex-1 overflow-y-auto p-6 lg:px-10 lg:py-8 bg-background-light dark:bg-background-dark transition-colors duration-200">
        <div class="max-w-[900px] mx-auto flex flex-col gap-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-sm">
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.home') }}">Dashboard</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="{{ route('admin.teachers-payments.index') }}">Teachers Payments</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <span class="text-text-main dark:text-white font-medium">Payment Details</span>
            </nav>

            <!-- Page Heading -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h1 class="text-3xl font-bold text-text-main dark:text-white tracking-tight">
                        {{ trans('global.show') }} {{ trans('cruds.teachersPayment.title_singular') }}
                    </h1>
                    <p class="text-text-secondary dark:text-gray-400">
                        Detailed information for transaction record #{{ $teachersPayment->id }}
                    </p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Amount</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">
                        ৳{{ number_format($teachersPayment->calculated_amount, 2) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Paid Amount</p>
                    <p class="text-2xl font-bold text-green-600">
                        ৳{{ number_format($teachersPayment->paid_amount, 2) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Remaining</p>
                    <p class="text-2xl font-bold {{ $teachersPayment->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ৳{{ number_format($teachersPayment->remaining_amount, 2) }}
                    </p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Header with Status -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">payments</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white">Payment Record</h3>
                            <p class="text-xs text-slate-500">ID: #{{ $teachersPayment->id }}</p>
                        </div>
                    </div>
                    <div>
                        @php
                            $status = $teachersPayment->payment_status;
                            $statusLabel = App\Models\TeachersPayment::PAYMENT_STATUS_SELECT[$status] ?? $status;
                            $colorClass = match ($status) {
                                'paid' => 'bg-green-100 text-green-700',
                                'partial' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-red-100 text-red-700',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $colorClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Teacher</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $teachersPayment->teacher->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Period</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $teachersPayment->month_year_name }}</p>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Form -->
            @if($teachersPayment->remaining_amount > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Add Payment</h3>
                </div>
                <form method="POST" action="{{ route('admin.teachers-payments.transactions.store', $teachersPayment->id) }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">৳</span>
                                <input type="number" name="amount" step="0.01" min="0.01" value="{{ $teachersPayment->remaining_amount }}"
                                    class="w-full pl-8 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Method</label>
                            <select name="payment_method" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Reference (Optional)</label>
                            <input type="text" name="reference" placeholder="Transaction ref"
                                class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-500 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg"></textarea>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90">
                            Add Payment
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Transactions List -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Payment Transactions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Method</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Reference</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Amount</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Recorded By</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($teachersPayment->transactions as $transaction)
                                <tr>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        @if($transaction->payment_method === 'cash')
                                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">Cash</span>
                                        @elseif($transaction->payment_method === 'bank_transfer')
                                            <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-700">Bank</span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">Mobile</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">{{ $transaction->reference ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-green-600">৳{{ number_format($transaction->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $transaction->createdBy->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.teachers-payments.transactions.destroy', [$teachersPayment->id, $transaction->id]) }}" onsubmit="return confirm('Delete this transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                        No transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <a href="{{ route('admin.teachers-payments.index') }}"
                    class="px-6 py-2.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to List
                </a>
            </div>
        </div>
    </div>

@endsection
