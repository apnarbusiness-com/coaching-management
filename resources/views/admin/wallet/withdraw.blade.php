@extends('layouts.admin')
@section('title', 'Request Withdrawal')
@section('content')
<div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
    <div class="max-w-2xl mx-auto flex flex-col gap-6 pb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Request Withdrawal</h1>
            <p class="mt-1 text-slate-500 dark:text-slate-400">Current balance: <strong>{{ number_format($wallet->balance, 2) }} TK</strong></p>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <form method="POST" action="{{ route('admin.wallet.withdraw.submit') }}">
                @csrf

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Amount (TK)</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="number" name="amount" id="withdrawAmount" class="form-control flex-1" required min="1" max="{{ $wallet->balance }}" step="0.01" value="{{ old('amount', $wallet->balance) }}">
                        <button type="button" onclick="document.getElementById('withdrawAmount').value = '{{ $wallet->balance }}'"
                            class="px-3 py-2 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 shrink-0">
                            Max
                        </button>
                    </div>
                    @error('amount') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Payment Method</label>
                    <select name="payment_method" class="custom-select mt-1" required>
                        <option value="">Select</option>
                        <option value="bkash" {{ old('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ old('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="rocket" {{ old('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                    @error('payment_method') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Account Number (optional)</label>
                    <input type="text" name="account_number" class="form-control mt-1" value="{{ old('account_number') }}" placeholder="e.g. 01XXXXXXXXX">
                    @error('account_number') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="font-medium text-slate-700 dark:text-slate-300">Phone Number</label>
                    <input type="text" name="phone" class="form-control mt-1" required value="{{ old('phone') }}" placeholder="Your contact number">
                    @error('phone') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold">Submit Request</button>
                    <a href="{{ route('admin.wallet.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
