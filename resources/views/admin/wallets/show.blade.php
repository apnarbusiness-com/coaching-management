@extends('layouts.admin')
@section('title', 'Wallet Details')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-4xl mx-auto flex flex-col gap-6 pb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Wallet</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $wallet->user?->name }} ({{ $wallet->user?->email }})</p>
            </div>
            <a href="{{ route('admin.wallets.adjust', $wallet->id) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">
                Adjust Balance
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <p class="text-sm text-slate-500">Balance</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($wallet->balance, 2) }} TK</p>
            </div>
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <p class="text-sm text-slate-500">Total Earned</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($wallet->total_earned, 2) }} TK</p>
            </div>
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <p class="text-sm text-slate-500">Total Withdrawn</p>
                <p class="text-3xl font-bold text-amber-600">{{ number_format($wallet->total_withdrawn, 2) }} TK</p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-semibold text-slate-900 dark:text-white">Transaction History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-left">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($transactions as $txn)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $txn->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $txn->type === 'credit' ? 'bg-green-100 text-green-700' : ($txn->type === 'debit' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($txn->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($txn->amount, 2) }} TK</td>
                            <td class="px-4 py-3 text-slate-600">{{ $txn->description ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">No transactions.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
