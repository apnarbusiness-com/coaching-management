@extends('layouts.admin')
@section('title', 'Adjust Wallet')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-2xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Adjust Wallet</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $wallet->user?->name }} — Balance: {{ number_format($wallet->balance, 2) }} TK</p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <form method="POST" action="{{ route('admin.wallets.adjust.submit', $wallet->id) }}">
                @csrf

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Type</label>
                    <select name="type" class="custom-select mt-1" required>
                        <option value="credit">Credit (+)</option>
                        <option value="debit">Debit (-)</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Amount (TK)</label>
                    <input type="number" name="amount" class="form-control mt-1" required min="1" step="0.01">
                    @error('amount') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Description (optional)</label>
                    <textarea name="description" class="form-control mt-1" rows="2">{{ old('description') }}</textarea>
                    @error('description') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">Save</button>
                    <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
