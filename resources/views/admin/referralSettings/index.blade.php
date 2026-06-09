@extends('layouts.admin')
@section('title', 'Referral Settings')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-7xl mx-auto flex flex-col gap-6 pb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Referral Settings</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Control who can use the referral & wallet system.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('admin.referral-settings.enable-all') }}" onsubmit="return confirm('Enable wallet access for ALL users?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Enable All
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.referral-settings.disable-all') }}" onsubmit="return confirm('Disable wallet access for ALL users?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Disable All
                    </button>
                </form>
            </div>
        </div>

        {{-- Batch-wise toggle --}}
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 md:p-6">
            <h2 class="font-semibold text-slate-900 dark:text-white mb-3">Batch-wise Toggle</h2>
            <form method="POST" action="{{ route('admin.referral-settings.batch-wise-toggle') }}" class="flex flex-col md:flex-row gap-3 items-start md:items-end">
                @csrf
                <div class="w-full md:w-64">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Select Batch</label>
                    <select name="batch_id" class="custom-select mt-1" required>
                        <option value="">Choose batch...</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" name="action" value="enable" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm">
                    Enable All Students
                </button>
                <button type="submit" name="action" value="disable" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-sm">
                    Disable All Students
                </button>
            </form>
        </div>

        {{-- Filter tabs --}}
        <div class="flex gap-1 bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-1 w-fit">
            <a href="{{ route('admin.referral-settings.index', ['filter' => 'all']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $filter === 'all' ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                All
            </a>
            <a href="{{ route('admin.referral-settings.index', ['filter' => 'active']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $filter === 'active' ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                Active
            </a>
            <a href="{{ route('admin.referral-settings.index', ['filter' => 'inactive']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $filter === 'inactive' ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                Inactive
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.referral-settings.index') }}">
            <div class="flex gap-2">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by name, email, roll, or user ID..."
                    class="form-control max-w-md">
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold text-sm">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('admin.referral-settings.index', ['filter' => $filter]) }}"
                        class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- User list --}}
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <span class="text-sm text-slate-500">Total: {{ $users->total() }}</span>
                {{-- Batch actions --}}
                <div class="flex gap-2 items-center">
                    <span class="text-xs text-slate-400" id="selectedCount">0 selected</span>
                    <button id="batchEnableBtn" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-semibold disabled:opacity-40" disabled onclick="batchAction('enable')">
                        Enable Selected
                    </button>
                    <button id="batchDisableBtn" class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-semibold disabled:opacity-40" disabled onclick="batchAction('disable')">
                        Disable Selected
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            </th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Referral Code</th>
                            <th class="px-4 py-3 text-center">Wallet Access</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-700">{{ $role->title }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                @if($user->referral_code)
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-semibold text-slate-900 dark:text-white">{{ $user->referral_code }}</span>
                                    <button onclick="copyCode('{{ $user->referral_code }}', this)"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copy
                                    </button>
                                </div>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('admin.referral-settings.toggle', $user->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all border
                                        {{ $user->wallet_access ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100' }}">
                                        <span class="w-2 h-2 rounded-full {{ $user->wallet_access ? 'bg-green-500' : 'bg-slate-300' }}"></span>
                                        {{ $user->wallet_access ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<form id="batchToggleForm" method="POST" action="{{ route('admin.referral-settings.batch-toggle') }}" style="display:none">
    @csrf
    <input type="hidden" name="action" id="batchActionInput">
    <div id="batchUserIds"></div>
</form>

<script>
function toggleAll(master) {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = master.checked);
    updateSelectedCount();
}

document.querySelectorAll('.user-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.user-checkbox:checked');
    const count = checked.length;
    document.getElementById('selectedCount').textContent = count + ' selected';
    document.getElementById('batchEnableBtn').disabled = count === 0;
    document.getElementById('batchDisableBtn').disabled = count === 0;
}

function batchAction(action) {
    const checked = document.querySelectorAll('.user-checkbox:checked');
    if (checked.length === 0) return;
    const ids = Array.from(checked).map(cb => cb.value);
    document.getElementById('batchActionInput').value = action;

    const container = document.getElementById('batchUserIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    document.getElementById('batchToggleForm').submit();
}

function copyCode(code, btn) {
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    const orig = btn.innerHTML;
    btn.innerHTML = 'Copied!';
    setTimeout(() => btn.innerHTML = orig, 1500);
}
</script>
@endsection
