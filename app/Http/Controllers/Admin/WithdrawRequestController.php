<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WithdrawRequestController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function index()
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $requests = WithdrawRequest::with(['user', 'wallet'])
            ->orderByRaw("case when status = 'pending' then 0 when status = 'approved' then 1 else 2 end")
            ->latest()
            ->paginate(20);

        return view('admin.withdrawRequests.index', compact('requests'));
    }

    public function approve(WithdrawRequest $withdrawRequest)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($withdrawRequest->status !== 'pending') {
            return back()->with('error', 'Already processed.');
        }

        $wallet = $withdrawRequest->wallet;
        if ($wallet->balance < $withdrawRequest->amount) {
            return back()->with('error', 'Insufficient balance in referrer wallet.');
        }

        $wallet->decrement('balance', $withdrawRequest->amount);
        $wallet->increment('total_withdrawn', $withdrawRequest->amount);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $withdrawRequest->amount,
            'type' => 'withdraw',
            'reference_type' => 'App\Models\WithdrawRequest',
            'reference_id' => $withdrawRequest->id,
            'description' => "Withdrawal approved via {$withdrawRequest->payment_method} ({$withdrawRequest->account_number})",
        ]);

        $withdrawRequest->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => Carbon::now(),
        ]);

        return back()->with('status', 'Withdraw request approved.');
    }

    public function reject(Request $request, WithdrawRequest $withdrawRequest)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($withdrawRequest->status !== 'pending') {
            return back()->with('error', 'Already processed.');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $withdrawRequest->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'processed_by' => Auth::id(),
            'processed_at' => Carbon::now(),
        ]);

        return back()->with('status', 'Withdraw request rejected.');
    }
}
