<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\CashBookTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\StudentBasicInfo;
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

        $studentInfoMap = [];
        foreach ($requests as $req) {
            if ($req->status !== 'pending' || !$req->wallet) continue;

            $txn = WalletTransaction::where('wallet_id', $req->wallet_id)
                ->where('type', 'credit')
                ->where('reference_type', 'App\Models\StudentBasicInfo')
                ->latest()
                ->first();

            if ($txn) {
                $student = StudentBasicInfo::find($txn->reference_id);
                if ($student) {
                    $studentInfoMap[$req->id] = [
                        'student_id' => $student->id,
                        'student_id_no' => $student->id_no,
                        'student_name' => trim($student->first_name . ' ' . ($student->last_name ?? '')),
                    ];
                }
            }
        }

        $cashBooks = CashBook::where('is_financial_account', true)->orderBy('order')->orderBy('title')->get();
        $defaultCashBook = CashBook::where('is_financial_account', true)->where('is_default', true)->first();

        return view('admin.withdrawRequests.index', compact('requests', 'cashBooks', 'defaultCashBook', 'studentInfoMap'));
    }

    public function approve(Request $request, WithdrawRequest $withdrawRequest)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($withdrawRequest->status !== 'pending') {
            return back()->with('error', 'Already processed.');
        }

        $validated = $request->validate([
            'cash_book_id' => ['required', 'integer', 'exists:cash_books,id'],
            'note' => ['required', 'string', 'max:500'],
        ]);

        $wallet = $withdrawRequest->wallet;
        if (!$wallet || $wallet->balance < $withdrawRequest->amount) {
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
            'admin_notes' => $validated['note'],
            'processed_by' => Auth::id(),
            'processed_at' => Carbon::now(),
        ]);

        $cashBook = CashBook::findOrFail($validated['cash_book_id']);

        $expenseCategoryId = ExpenseCategory::where('name', 'like', '%commission%')
            ->orWhere('name', 'like', '%referral%')
            ->first()?->id
            ?? ExpenseCategory::first()?->id;

        Expense::create([
            'expense_category_id' => $expenseCategoryId,
            'title' => $validated['note'],
            'details' => "Withdrawal approved for {$withdrawRequest->user?->name} (ID: {$withdrawRequest->user_id})",
            'amount' => $withdrawRequest->amount,
            'expense_date' => now(),
            'expense_month' => now()->month,
            'expense_year' => now()->year,
            'payment_method' => $cashBook->title,
            'cash_book_id' => $cashBook->id,
            'paid_by' => Auth::user()->name,
            'created_by_id' => Auth::id(),
        ]);

        $oldAmount = $cashBook->amount;
        $newAmount = $oldAmount - (float) $withdrawRequest->amount;
        $cashBook->update(['amount' => $newAmount]);

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'action_type' => 'expense_subtracted',
            'note' => "Referral commission withdrawal — {$withdrawRequest->user?->name} — {$validated['note']}",
            'created_by_id' => Auth::id(),
        ]);

        return back()->with('status', 'Withdraw request approved and expense recorded.');
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
