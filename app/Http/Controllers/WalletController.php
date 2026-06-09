<?php

namespace App\Http\Controllers;

use App\Models\WithdrawRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->wallet_access) {
                abort(403, 'Wallet access is disabled for your account.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $this->walletService->getOrCreateWallet($user->id);
        }

        if (empty($user->referral_code)) {
            $user->referral_code = \App\Models\User::generateReferralCode($user);
            $user->save();
        }

        $transactions = $wallet->transactions()->latest()->paginate(20);
        $withdrawRequests = $wallet->withdrawRequests()->latest()->get();

        return view('admin.wallet.index', compact('wallet', 'transactions', 'withdrawRequests'));
    }

    public function generateCode()
    {
        $user = Auth::user();
        $user->referral_code = \App\Models\User::generateReferralCode($user);
        $user->save();

        return redirect()->route('admin.wallet.index')->with('status', 'Referral code generated successfully!');
    }

    public function withdrawForm()
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance <= 0) {
            return redirect()->route('admin.wallet.index')->with('error', 'Insufficient balance.');
        }

        return view('admin.wallet.withdraw', compact('wallet'));
    }

    public function withdrawSubmit(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:bkash,nagad,bank,rocket'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $validated['amount']) {
            return back()->with('error', 'Insufficient balance.');
        }

        WithdrawRequest::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'account_number' => $validated['account_number'],
            'phone' => $validated['phone'],
            'status' => 'pending',
        ]);

        return redirect()->route('admin.wallet.index')->with('status', 'Withdraw request submitted successfully.');
    }
}
