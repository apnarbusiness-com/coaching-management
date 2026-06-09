@extends('layouts.admin')
@section('title', 'All Wallets')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-6xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">All Wallets</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">Manage user wallet balances.</p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-sm text-slate-500">Total wallets: {{ $wallets->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Referral Code</th>
                            <th class="px-4 py-3 text-right">Balance</th>
                            <th class="px-4 py-3 text-right">Total Earned</th>
                            <th class="px-4 py-3 text-right">Total Withdrawn</th>
                            <th class="px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($wallets as $wallet)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $wallet->user?->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500">{{ $wallet->user?->email }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $wallet->user?->referral_code ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($wallet->balance, 2) }}</td>
                            <td class="px-4 py-3 text-right text-green-600">{{ number_format($wallet->total_earned, 2) }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">{{ number_format($wallet->total_withdrawn, 2) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="text-primary font-semibold hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No wallets found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $wallets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
