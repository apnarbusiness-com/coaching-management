@extends('layouts.admin')
@section('title', 'New Referral Campaign')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-2xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">New Campaign</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">Create a referral campaign with reward amount.</p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <form method="POST" action="{{ route('admin.referral-campaigns.store') }}">
                @csrf

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Campaign Name</label>
                    <input type="text" name="name" class="form-control mt-1" required value="{{ old('name') }}" placeholder="e.g. HSC-26 Farewell Referral">
                    @error('name') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Reward Amount (TK)</label>
                    <input type="number" name="reward_amount" class="form-control mt-1" required min="0" step="0.01" value="{{ old('reward_amount') }}">
                    @error('reward_amount') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Description (optional)</label>
                    <textarea name="description" class="form-control mt-1" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-medium text-slate-700 dark:text-slate-300">Start Date</label>
                        <input type="date" name="start_date" class="form-control mt-1" value="{{ old('start_date') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-medium text-slate-700 dark:text-slate-300">End Date</label>
                        <input type="date" name="end_date" class="form-control mt-1" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">Create</button>
                    <a href="{{ route('admin.referral-campaigns.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
