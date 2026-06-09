@extends('layouts.admin')
@section('title', 'My Wallet')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-4xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">My Wallet</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">Referral earnings, balance & withdrawals.</p>
        </div>

        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl shadow-lg p-6 md:p-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-teal-100 text-sm font-medium uppercase tracking-wider">Your Referral Code</p>
                    @if(auth()->user()->referral_code)
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-3xl md:text-4xl font-bold font-mono tracking-[4px]">{{ auth()->user()->referral_code }}</span>
                        <button onclick="copyReferralCode()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-semibold transition-all"
                            id="copyBtn">
                            <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span id="copyText">Copy</span>
                        </button>
                    </div>
                    @else
                    <div class="mt-2">
                        <p class="text-teal-100 text-sm mb-3">You don't have a referral code yet.</p>
                        <a href="{{ route('admin.wallet.generate.code') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-teal-700 rounded-lg hover:bg-teal-50 font-semibold transition-all text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Generate Referral Code
                        </a>
                    </div>
                    @endif
                </div>
                @if(auth()->user()->referral_code)
                <div class="text-right">
                    <p class="text-teal-100 text-sm">Share this code with friends</p>
                    <p class="text-teal-200 text-xs mt-0.5">Earn reward per successful referral!</p>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <p class="text-sm text-slate-500">Current Balance</p>
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

        @if($wallet->balance > 0)
        <div>
            <a href="{{ route('admin.wallet.withdraw.form') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Request Withdrawal
            </a>
        </div>
        @endif

        @if($withdrawRequests->count() > 0)
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-semibold text-slate-900 dark:text-white">Withdraw Requests</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($withdrawRequests as $wr)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $wr->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($wr->amount, 2) }} TK</td>
                            <td class="px-4 py-3">{{ strtoupper($wr->payment_method) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $wr->status === 'approved' ? 'bg-green-100 text-green-700' : ($wr->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($wr->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

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
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">No transactions yet.</td>
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

<style>
.toast-copy {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(80px);
    background: #0f172a;
    color: #fff;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    opacity: 0;
    transition: all 0.35s ease;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    pointer-events: none;
}
.toast-copy.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
    pointer-events: auto;
}
.toast-copy svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}
</style>

<div id="toastCopied" class="toast-copy">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>Referral code copied to clipboard!</span>
</div>

<script>
function copyReferralCode() {
    const code = "{{ auth()->user()->referral_code }}";
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.body.removeChild(textarea);

    const btn = document.getElementById('copyText');
    const icon = document.getElementById('copyIcon');
    btn.textContent = 'Copied!';
    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />';

    const toast = document.getElementById('toastCopied');
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
        btn.textContent = 'Copy';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />';
    }, 2500);
}
</script>
@endsection
