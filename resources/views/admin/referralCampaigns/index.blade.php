@extends('layouts.admin')
@section('title', 'Referral Campaigns')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Referral Campaigns</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Configure referral reward amounts per campaign.</p>
            </div>
            <a href="{{ route('admin.referral-campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">
                + New Campaign
            </a>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-sm text-slate-500">Total: {{ $campaigns->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-right">Reward</th>
                            <th class="px-4 py-3 text-left">Duration</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($campaigns as $campaign)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $campaign->name }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">{{ number_format($campaign->reward_amount, 2) }} TK</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                @if(is_null($campaign->start_date) && is_null($campaign->end_date))
                                    <span class="text-green-600 dark:text-green-400 font-semibold">Always Active</span>
                                @else
                                    {{ $campaign->start_date?->format('d M Y') ?? 'Any' }} — {{ $campaign->end_date?->format('d M Y') ?? 'Unlimited' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $campaign->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.referral-campaigns.edit', $campaign->id) }}" class="text-primary font-semibold hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.referral-campaigns.destroy', $campaign->id) }}" onsubmit="return confirm('Delete this campaign?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 font-semibold hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">No campaigns yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
