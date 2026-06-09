<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function index()
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $wallets = Wallet::with('user')->latest()->paginate(20);

        return view('admin.wallets.index', compact('wallets'));
    }

    public function show(Wallet $wallet)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $wallet->load('user');
        $transactions = $wallet->transactions()->latest()->paginate(20);

        return view('admin.wallets.show', compact('wallet', 'transactions'));
    }

    public function adjustForm(Wallet $wallet)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.wallets.adjust', compact('wallet'));
    }

    public function adjustSubmit(Request $request, Wallet $wallet)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['type'] === 'credit') {
            $this->walletService->credit(
                $wallet->user_id,
                $validated['amount'],
                null,
                null,
                $validated['description'] ?? 'Manual adjustment'
            );
        } else {
            try {
                $this->walletService->debit(
                    $wallet->user_id,
                    $validated['amount'],
                    $validated['description'] ?? 'Manual adjustment'
                );
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return redirect()->route('admin.wallets.show', $wallet->id)->with('status', 'Wallet adjusted.');
    }
}
