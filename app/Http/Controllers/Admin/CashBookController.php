<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\CashBookTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CashBookController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('cash_book_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cashBooks = CashBook::orderBy('order')->orderBy('title')->get();
        $totalBalance = $cashBooks->sum('amount');

        return view('admin.cashBooks.index', compact('cashBooks', 'totalBalance'));
    }

    public function create()
    {
        abort_if(Gate::denies('cash_book_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.cashBooks.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('cash_book_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'is_financial_account' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['status'] = 'active';
        $data['is_financial_account'] = $request->boolean('is_financial_account');
        $data['is_default'] = $request->boolean('is_default');
        $data['order'] = $request->input('order', 0);

        if ($data['is_default']) {
            CashBook::where('is_default', true)->update(['is_default' => false]);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cash-books', 'public');
        }

        $data['icon'] = $request->input('icon') ?: null;

        $cashBook = CashBook::create($data);

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => 0,
            'new_amount' => $data['amount'],
            'action_type' => 'create',
            'note' => $data['note'] ?? null,
            'created_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.cash-books.index')->with('status', 'Cash book entry created successfully.');
    }

    public function edit(CashBook $cashBook)
    {
        abort_if(Gate::denies('cash_book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.cashBooks.edit', compact('cashBook'));
    }

    public function update(Request $request, CashBook $cashBook)
    {
        abort_if(Gate::denies('cash_book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'remove_image' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'is_financial_account' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_financial_account'] = $request->boolean('is_financial_account');
        $data['is_default'] = $request->boolean('is_default');
        $data['order'] = $request->input('order', 0);
        $oldAmount = $cashBook->amount;

        if ($data['is_default']) {
            CashBook::where('id', '!=', $cashBook->id)->where('is_default', true)->update(['is_default' => false]);
        }

        if ($request->boolean('remove_image') || $request->hasFile('image')) {
            if ($cashBook->image) {
                Storage::disk('public')->delete($cashBook->image);
            }
            $data['image'] = $request->hasFile('image') ? $request->file('image')->store('cash-books', 'public') : null;
        } else {
            unset($data['image']);
        }

        if ($request->filled('icon')) {
            $data['icon'] = $request->input('icon');
        } else {
            $data['icon'] = null;
        }

        $cashBook->update($data);

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => $oldAmount,
            'new_amount' => $data['amount'],
            'action_type' => 'update',
            'note' => $data['note'] ?? null,
            'created_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.cash-books.index')->with('status', 'Cash book entry updated successfully.');
    }

    public function destroy(CashBook $cashBook)
    {
        abort_if(Gate::denies('cash_book_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $oldAmount = $cashBook->amount;

        if ($cashBook->image) {
            Storage::disk('public')->delete($cashBook->image);
        }

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => $oldAmount,
            'new_amount' => 0,
            'action_type' => 'delete',
            'note' => 'Entry deleted',
            'created_by_id' => auth()->id(),
        ]);

        $cashBook->delete();

        return back()->with('status', 'Cash book entry deleted.');
    }

    public function transactions(CashBook $cashBook, Request $request)
    {
        abort_if(Gate::denies('cash_book_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $page = (int) $request->input('page', 1);

        $transactions = $cashBook->transactions()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'page', $page);

        return response()->json([
            'cash_book' => $cashBook,
            'transactions' => $transactions->items(),
            'has_more' => $transactions->hasMorePages(),
            'next_page' => $transactions->currentPage() + 1,
        ]);
    }

    public function transfer(Request $request)
    {
        abort_if(Gate::denies('cash_book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'from_cash_book_id' => 'required|integer|exists:cash_books,id',
            'to_cash_book_id'   => 'required|integer|exists:cash_books,id|different:from_cash_book_id',
            'amount'            => 'required|numeric|min:0.01',
            'note'              => 'nullable|string|max:500',
        ]);

        $from = CashBook::findOrFail($data['from_cash_book_id']);
        $to   = CashBook::findOrFail($data['to_cash_book_id']);

        if ($from->amount < $data['amount']) {
            return back()->withErrors([
                'amount' => "Insufficient balance in '{$from->title}'. Available: Tk " . number_format($from->amount, 2),
            ])->withInput();
        }

        $fromOld = $from->amount;
        $toOld   = $to->amount;

        $from->update(['amount' => $fromOld - $data['amount']]);
        $to->update(['amount'   => $toOld + $data['amount']]);

        $note = trim($data['note'] ?? '');

        CashBookTransaction::create([
            'cash_book_id'  => $from->id,
            'old_amount'    => $fromOld,
            'new_amount'    => $from->amount,
            'action_type'   => 'transfer_out',
            'note'          => "Transferred Tk " . number_format($data['amount'], 2) . " to '{$to->title}'." . ($note ? " Note: {$note}" : ''),
            'created_by_id' => auth()->id(),
        ]);

        CashBookTransaction::create([
            'cash_book_id'  => $to->id,
            'old_amount'    => $toOld,
            'new_amount'    => $to->amount,
            'action_type'   => 'transfer_in',
            'note'          => "Received Tk " . number_format($data['amount'], 2) . " from '{$from->title}'." . ($note ? " Note: {$note}" : ''),
            'created_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.cash-books.index')->with('status', 'Funds transferred successfully.');
    }

    public function updateDisplayType(Request $request)
    {
        abort_if(Gate::denies('cash_book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'display_type' => 'required|in:select,card',
        ]);

        Setting::updateOrCreate(
            ['key' => 'cashbook_display_type'],
            ['value' => $data['display_type']]
        );

        return redirect()->route('admin.cash-books.index')->with('status', 'Display type updated successfully.');
    }

    public function setDefault(CashBook $cashBook)
    {
        abort_if(Gate::denies('cash_book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        CashBook::where('is_default', true)->update(['is_default' => false]);
        $cashBook->update(['is_default' => true]);

        return redirect()->route('admin.cash-books.index')->with('status', "'{$cashBook->title}' set as default account.");
    }
}