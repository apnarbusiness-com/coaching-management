@extends('layouts.admin')
@section('title', 'Withdraw Requests')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-6xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Withdraw Requests</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">Manage user withdrawal requests.</p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-sm text-slate-500">Total: {{ $requests->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Account</th>
                            <th class="px-4 py-3 text-left">Phone</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $req->user?->name }}</div>
                                <div class="text-xs text-slate-500">{{ $req->user?->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ number_format($req->amount, 2) }} TK</td>
                            <td class="px-4 py-3 text-slate-900 dark:text-white uppercase">{{ $req->payment_method }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900 dark:text-white">{{ $req->account_number ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-900 dark:text-white">{{ $req->phone }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $req->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($req->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $req->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($req->status === 'pending')
                                <div class="flex gap-2">
                                    <button type="button" onclick="showApproveModal({{ $req->id }}, '{{ addslashes($req->user?->name ?? '') }}', {{ $req->amount }}, {{ $req->user_id }}, '{{ addslashes($studentInfoMap[$req->id]['student_id_no'] ?? '') }}', '{{ addslashes($studentInfoMap[$req->id]['student_name'] ?? '') }}')" class="text-green-600 dark:text-green-400 font-semibold hover:underline">Approve</button>
                                    <button type="button" onclick="showRejectModal({{ $req->id }})" class="text-red-600 dark:text-red-400 font-semibold hover:underline">Reject</button>
                                </div>
                                @else
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->format('d M Y') : '' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No requests.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<div id="approveModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Approve Withdraw Request</h3>
                <button onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <form id="approveForm" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="withdraw_request_id" id="approveModalRequestId">

            <div class="bg-slate-50 dark:bg-slate-900 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Recipient</span>
                    <span id="approveModalRecipient" class="text-sm font-semibold text-slate-900 dark:text-slate-100"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Amount</span>
                    <span id="approveModalAmount" class="text-sm font-bold text-green-600"></span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Payment Method <span class="text-red-500">*</span></label>
                @if ($cashBooks->isNotEmpty())
                    @if (setting('cashbook_display_type') == 'card')
                        @php
                            $icons = ['wallet'=>'💰','money'=>'💵','bank'=>'🏦','mobile'=>'📱','card'=>'💳','gift'=>'🎁','gold'=>'🪙','dollar'=>'💲'];
                        @endphp
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($cashBooks as $cb)
                                <label class="cursor-pointer">
                                    <input class="payment-card-input sr-only" type="radio" name="cash_book_id" value="{{ $cb->id }}" {{ $defaultCashBook && $defaultCashBook->id == $cb->id ? 'checked' : ($loop->first ? 'checked' : '') }} required>
                                    <div class="payment-card flex flex-col items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-center text-slate-700 dark:text-slate-200">
                                        @if ($cb->image)
                                            <img src="{{ Storage::url($cb->image) }}" class="w-6 h-6 mb-1 object-contain">
                                        @elseif ($cb->icon && isset($icons[$cb->icon]))
                                            <span class="text-lg mb-1">{{ $icons[$cb->icon] }}</span>
                                        @else
                                            <span class="material-symbols-outlined mb-1 text-lg">account_balance</span>
                                        @endif
                                        <span class="text-xs font-medium leading-tight">{{ $cb->title }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <select name="cash_book_id" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary" required>
                            <option value="">— Select Account —</option>
                            @foreach ($cashBooks as $cb)
                                <option value="{{ $cb->id }}" {{ $defaultCashBook && $defaultCashBook->id == $cb->id ? 'selected' : '' }}>{{ $cb->title }}</option>
                            @endforeach
                        </select>
                    @endif
                @else
                    <p class="text-sm text-red-400">No cash book accounts available. Please create one first.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Note</label>
                <textarea name="note" id="approveModalNote" rows="3" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-700">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90">
                    Confirm & Approve
                </button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Reject Withdraw Request</h3>
        <form method="POST" action="" id="rejectForm">
            @csrf
            <div class="form-group mb-4">
                <label class="font-medium text-slate-700 dark:text-slate-300">Reason (optional)</label>
                <textarea name="admin_notes" class="form-control mt-1" rows="3"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>

<style>
.payment-card-input:checked + .payment-card {
    border-color: #10b981;
    background-color: #f0fdf4;
}
.dark .payment-card-input:checked + .payment-card {
    border-color: #34d399;
    background-color: rgba(16, 185, 129, 0.1);
}
</style>

<script>
function showApproveModal(id, name, amount, referrerId, studentIdNo, studentName) {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModalRequestId').value = id;
    document.getElementById('approveModalRecipient').textContent = name + ' (ID: ' + referrerId + ')';
    document.getElementById('approveModalAmount').textContent = '৳' + parseFloat(amount).toFixed(2);

    var defaultNote = '';
    if (studentIdNo) {
        defaultNote = 'For ID ' + studentIdNo + ' Admission commission To ID ' + referrerId;
    } else if (studentName) {
        defaultNote = 'Admission commission for ' + studentName + ' To ID ' + referrerId;
    } else {
        defaultNote = 'Admission commission — Referrer ID: ' + referrerId;
    }
    document.getElementById('approveModalNote').value = defaultNote;

    var form = document.getElementById('approveForm');
    form.action = '/admin/withdraw-requests/' + id + '/approve';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
    }
});

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/withdraw-requests/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}
function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
