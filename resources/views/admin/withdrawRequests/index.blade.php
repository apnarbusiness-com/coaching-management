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
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($req->amount, 2) }} TK</td>
                            <td class="px-4 py-3">{{ strtoupper($req->payment_method) }}</td>
                            <td class="px-4 py-3 text-xs">{{ $req->account_number ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $req->phone }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $req->status === 'approved' ? 'bg-green-100 text-green-700' : ($req->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $req->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($req->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.withdraw-requests.approve', $req->id) }}">
                                        @csrf
                                        <button type="submit" class="text-green-600 font-semibold hover:underline">Approve</button>
                                    </form>
                                    <button type="button" onclick="showRejectModal({{ $req->id }})" class="text-red-600 font-semibold hover:underline">Reject</button>
                                </div>
                                @else
                                <span class="text-xs text-slate-400">{{ $req->processed_at?->format('d M Y') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">No requests.</td>
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

<script>
function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/withdraw-requests/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}
function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
