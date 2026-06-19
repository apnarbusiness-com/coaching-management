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
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="text-2xl sm:text-3xl md:text-4xl font-bold font-mono tracking-[4px] break-all">{{ auth()->user()->referral_code }}</span>
                        <button onclick="copyReferralCode()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-semibold transition-all"
                            id="copyBtn">
                            <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span id="copyText">Copy</span>
                        </button>
                        <a href="{{ route('admin.wallet.generate.code') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-semibold transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Regenerate
                        </a>
                        <button onclick="openCustomCodeModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-semibold transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Set Custom Code
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
            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
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
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $wr->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ number_format($wr->amount, 2) }} TK</td>
                            <td class="px-4 py-3 text-slate-900 dark:text-white uppercase">{{ $wr->payment_method }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $wr->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($wr->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
                                    {{ ucfirst($wr->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile cards --}}
            <div class="sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($withdrawRequests as $wr)
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">{{ $wr->created_at?->format('d M Y') }}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $wr->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($wr->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
                            {{ ucfirst($wr->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Amount</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($wr->amount, 2) }} TK</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Method</span>
                        <span class="text-slate-900 dark:text-white uppercase">{{ $wr->payment_method }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-semibold text-slate-900 dark:text-white">Transaction History</h2>
            </div>
            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
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
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $txn->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $txn->type === 'credit' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($txn->type === 'debit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                    {{ ucfirst($txn->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ number_format($txn->amount, 2) }} TK</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $txn->description ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No transactions yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Mobile cards --}}
            <div class="sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($transactions as $txn)
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">{{ $txn->created_at?->format('d M Y') }}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $txn->type === 'credit' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($txn->type === 'debit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') }}">
                            {{ ucfirst($txn->type) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Amount</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($txn->amount, 2) }} TK</span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span>{{ $txn->description ?? '—' }}</span>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-sm text-slate-500">No transactions yet.</div>
                @endforelse
            </div>
            <div class="p-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

<div id="customCodeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Set Custom Referral Code</h3>
            <button onclick="closeCustomCodeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="customCodeForm" method="POST" action="{{ route('admin.wallet.set-custom-code') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Referral Code</label>
                <input type="text" name="referral_code" id="customCodeInput"
                    class="form-control w-full text-lg font-mono font-bold tracking-wider"
                    value="{{ auth()->user()->referral_code ?? '' }}"
                    maxlength="50"
                    placeholder="Enter your custom code">
                <div id="codeStatus" class="mt-2 text-sm font-medium"></div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submitCustomCode"
                    class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Save Code
                </button>
                <button type="button" onclick="closeCustomCodeModal()"
                    class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 font-semibold transition-all">
                    Cancel
                </button>
            </div>
        </form>
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

let checkTimeout;

function openCustomCodeModal() {
    const modal = document.getElementById('customCodeModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    const input = document.getElementById('customCodeInput');
    const status = document.getElementById('codeStatus');
    const submitBtn = document.getElementById('submitCustomCode');
    status.innerHTML = '';
    submitBtn.disabled = false;
    checkCodeUniqueness(input.value);
}

function closeCustomCodeModal() {
    const modal = document.getElementById('customCodeModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.getElementById('customCodeInput')?.addEventListener('input', function() {
    const status = document.getElementById('codeStatus');
    const submitBtn = document.getElementById('submitCustomCode');
    const value = this.value.trim();

    if (!value) {
        status.innerHTML = '<span class="text-amber-500">Code cannot be empty.</span>';
        submitBtn.disabled = true;
        return;
    }
    if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        status.innerHTML = '<span class="text-red-500">Only letters, numbers, and underscores allowed.</span>';
        submitBtn.disabled = true;
        return;
    }

    clearTimeout(checkTimeout);
    checkTimeout = setTimeout(() => checkCodeUniqueness(value), 400);
});

function checkCodeUniqueness(code) {
    const status = document.getElementById('codeStatus');
    const submitBtn = document.getElementById('submitCustomCode');

    if (!code) {
        submitBtn.disabled = true;
        return;
    }

    fetch('{{ route("admin.wallet.check-code") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code })
    })
    .then(res => res.json())
    .then(data => {
        if (data.unique) {
            status.innerHTML = '<span class="text-green-500">✓ Code is available!</span>';
            submitBtn.disabled = false;
        } else {
            status.innerHTML = '<span class="text-red-500">✕ This code is already taken by another user.</span>';
            submitBtn.disabled = true;
        }
    })
    .catch(() => {
        status.innerHTML = '<span class="text-amber-500">Could not verify uniqueness. Try again.</span>';
        submitBtn.disabled = false;
    });
}

document.getElementById('customCodeModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCustomCodeModal();
});
</script>
@endsection
