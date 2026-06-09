@extends('layouts.admin')
@section('title', 'Edit Campaign')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-2xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Campaign</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $referralCampaign->name }}</p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <form method="POST" action="{{ route('admin.referral-campaigns.update', $referralCampaign->id) }}">
                @csrf @method('PUT')

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Campaign Name</label>
                    <input type="text" name="name" class="form-control mt-1" required value="{{ old('name', $referralCampaign->name) }}">
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Reward Amount (TK)</label>
                    <input type="number" name="reward_amount" class="form-control mt-1" required min="0" step="0.01" value="{{ old('reward_amount', $referralCampaign->reward_amount) }}">
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea name="description" class="form-control mt-1" rows="3">{{ old('description', $referralCampaign->description) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-medium text-slate-700 dark:text-slate-300">Start Date</label>
                        <input type="date" name="start_date" class="form-control mt-1" value="{{ old('start_date', $referralCampaign->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-medium text-slate-700 dark:text-slate-300">End Date</label>
                        <input type="date" name="end_date" class="form-control mt-1" value="{{ old('end_date', $referralCampaign->end_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $referralCampaign->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">Update</button>
                    <a href="{{ route('admin.referral-campaigns.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
