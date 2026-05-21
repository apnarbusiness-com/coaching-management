<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CashBook;
use App\Models\Earning;
use App\Models\EarningCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FinancialLedgerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $year = $request->input('year', date('Y'));
        $statusFilter = $request->input('status_filter', 'all');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $batchesQuery = Batch::orderBy('batch_name')->with(['subject']);
        if ($statusFilter === 'active') {
            $batchesQuery->where('status', 1);
        } elseif ($statusFilter === 'inactive') {
            $batchesQuery->where('status', 0);
        }
        $batches = $batchesQuery->get();

        $batchEarnings = [];
        foreach ($batches as $batch) {
            $monthlyData = [];
            $totalEarning = 0;

            for ($m = 1; $m <= 12; $m++) {
                $amount = Earning::where('batch_id', $batch->id)
                    ->whereYear('earning_date', $year)
                    ->whereMonth('earning_date', $m)
                    ->sum('amount');

                $monthlyData[$m] = $amount;
                $totalEarning += $amount;
            }

            $batchEarnings[] = [
                'id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'subject' => $batch->subject->name ?? 'N/A',
                'monthly' => $monthlyData,
                'total' => $totalEarning,
            ];
        }

        $totalPerMonth = [];
        $grandTotal = 0;
        for ($m = 1; $m <= 12; $m++) {
            $amount = Earning::whereYear('earning_date', $year)
                ->whereMonth('earning_date', $m)
                ->sum('amount');
            $totalPerMonth[$m] = $amount;
            $grandTotal += $amount;
        }

        // --- Extra Earnings (non-batch) ---
        $extraEarningPerMonth = [];
        $grandTotalExtraEarning = 0;
        for ($m = 1; $m <= 12; $m++) {
            $amount = Earning::whereYear('earning_date', $year)
                ->whereMonth('earning_date', $m)
                ->whereNull('batch_id')
                ->sum('amount');
            $extraEarningPerMonth[$m] = $amount;
            $grandTotalExtraEarning += $amount;
        }

        // --- Batch Expenses ---

        $batchExpenses = [];
        foreach ($batches as $batch) {
            $monthlyExpenses = [];
            $totalExpenses = 0;

            for ($m = 1; $m <= 12; $m++) {
                $amount = Expense::where('batch_id', $batch->id)
                    ->whereMonth('expense_date', $m)
                    ->whereYear('expense_date', $year)
                    ->sum('amount');

                $monthlyExpenses[$m] = $amount;
                $totalExpenses += $amount;
            }

            if ($totalExpenses > 0) {
                $batchExpenses[] = [
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->batch_name,
                    'monthly' => $monthlyExpenses,
                    'total' => $totalExpenses,
                ];
            }
        }

        $totalExpensePerMonth = [];
        $grandTotalExpense = 0;
        for ($m = 1; $m <= 12; $m++) {
            $amount = Expense::whereMonth('expense_date', $m)
                ->whereYear('expense_date', $year)
                ->whereNotNull('batch_id')
                ->sum('amount');
            $totalExpensePerMonth[$m] = $amount;
            $grandTotalExpense += $amount;
        }

        // --- Other Expenses (non-batch) ---

        $totalOtherExpensePerMonth = [];
        $grandTotalOtherExpense = 0;
        for ($m = 1; $m <= 12; $m++) {
            $amount = Expense::whereMonth('expense_date', $m)
                ->whereYear('expense_date', $year)
                ->whereNull('batch_id')
                ->sum('amount');
            $totalOtherExpensePerMonth[$m] = $amount;
            $grandTotalOtherExpense += $amount;
        }

        $batchOtherExpenses = $grandTotalOtherExpense > 0
            ? [[
                'batch_id' => 0,
                'batch_name' => 'Other Expenses',
                'monthly' => $totalOtherExpensePerMonth,
                'total' => $grandTotalOtherExpense,
            ]]
            : [];

        // --- Uncategorized (missing month/year) ---
        $uncategorizedEarningTotal = Earning::whereNull('earning_date')->sum('amount');
        $uncategorizedEarningCount = Earning::whereNull('earning_date')->count();
        $uncategorizedExpenseTotal = Expense::whereNull('expense_date')->sum('amount');
        $uncategorizedExpenseCount = Expense::whereNull('expense_date')->count();

        // --- Cash Book ---

        $cashBooks = CashBook::where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title', 'amount', 'icon', 'image']);
        $totalCashInHand = $cashBooks->sum('amount');

        $activeBatches = $batches->count();
        $totalExpensesCombined = $grandTotalExpense + $grandTotalOtherExpense;
        $netProfit = $grandTotal - $totalExpensesCombined;
        $profitMargin = $grandTotal > 0 ? round(($netProfit / $grandTotal) * 100, 1) : 0;

        $previousYear = $year - 1;
        $previousYearEarning = Earning::whereYear('earning_date', $previousYear)->sum('amount');
        $percentChange = $previousYearEarning > 0 ? round((($grandTotal - $previousYearEarning) / $previousYearEarning) * 100, 1) : 0;

        $todayEarning = Earning::whereDate('earning_date', today())->sum('amount');
        $thisWeekEarning = Earning::whereBetween('earning_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');

        return view('admin.financialLedgers.index', compact(
            'batchEarnings',
            'totalPerMonth',
            'grandTotal',
            'extraEarningPerMonth',
            'grandTotalExtraEarning',
            'months',
            'year',
            'batchExpenses',
            'totalExpensePerMonth',
            'grandTotalExpense',
            'batchOtherExpenses',
            'totalOtherExpensePerMonth',
            'grandTotalOtherExpense',
            'cashBooks',
            'totalCashInHand',
            'activeBatches',
            'netProfit',
            'profitMargin',
            'percentChange',
            'statusFilter',
            'todayEarning',
            'thisWeekEarning',
            'uncategorizedEarningTotal',
            'uncategorizedEarningCount',
            'uncategorizedExpenseTotal',
            'uncategorizedExpenseCount'
        ));
    }

    public function getExpenseDetails(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batchId = $request->input('batch_id');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        $batch = Batch::findOrFail($batchId);
        $expenses = Expense::where('batch_id', $batchId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->get();

        $items = [];
        foreach ($expenses as $exp) {
            $category = $exp->expense_category_id ? ExpenseCategory::find($exp->expense_category_id) : null;
            $teacher = $exp->teacher_id ? Teacher::find($exp->teacher_id) : null;

            $items[] = [
                'id' => $exp->id,
                'title' => $exp->title ?? 'Untitled',
                'category' => $category ? $category->name : 'Uncategorized',
                'details' => $exp->details ?? '',
                'amount' => (float) $exp->amount,
                'teacher_name' => $teacher ? trim($teacher->name ?? $teacher->first_name . ' ' . $teacher->last_name) : null,
            ];
        }

        return response()->json([
            'batch_name' => $batch->batch_name,
            'month' => $month,
            'expenses' => $items
        ]);
    }

    public function getEarningDetails(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batchId = $request->input('batch_id');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        $batch = Batch::findOrFail($batchId);
        $earnings = Earning::where('batch_id', $batchId)
            ->whereYear('earning_date', $year)
            ->whereMonth('earning_date', $month)
            ->get();

        $payments = [];
        foreach ($earnings as $earning) {
            $student = StudentBasicInfo::find($earning->student_id);
            $studentName = $student ? trim($student->first_name . ' ' . $student->last_name) : 'Unknown';
            $idNo = $student ? $student->id_no : '';

            $discountAmount = 0;
            if ($earning->student_monthly_due_id) {
                $due = StudentMonthlyDue::find($earning->student_monthly_due_id);
                if ($due) {
                    $discountAmount = (float) $due->discount_amount;
                }
            }

            $payments[] = [
                'student_id' => $earning->student_id,
                'student_name' => $studentName,
                'id_no' => $idNo,
                'amount' => (float) $earning->amount,
                'discount_amount' => $discountAmount,
            ];
        }

        return response()->json([
            'batch_name' => $batch->batch_name,
            'month' => $month,
            'payments' => $payments
        ]);
    }

    public function getExtraEarningDetails(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        $earnings = Earning::whereNull('batch_id')
            ->whereYear('earning_date', $year)
            ->whereMonth('earning_date', $month)
            ->get();

        $items = [];
        foreach ($earnings as $earning) {
            $category = $earning->earning_category_id ? EarningCategory::find($earning->earning_category_id) : null;
            $student = $earning->student_id ? StudentBasicInfo::find($earning->student_id) : null;

            $items[] = [
                'id' => $earning->id,
                'title' => $earning->title ?? 'Untitled',
                'category' => $category ? $category->name : 'Uncategorized',
                'student_name' => $student ? trim($student->first_name . ' ' . $student->last_name) : 'N/A',
                'amount' => (float) $earning->amount,
                'earning_date' => $earning->earning_date,
            ];
        }

        return response()->json([
            'month' => $month,
            'year' => $year,
            'earnings' => $items
        ]);
    }

    public function getOtherExpenseDetails(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        $expenses = Expense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->whereNull('batch_id')
            ->get();

        $items = [];
        foreach ($expenses as $exp) {
            $category = $exp->expense_category_id ? ExpenseCategory::find($exp->expense_category_id) : null;

            $items[] = [
                'id' => $exp->id,
                'title' => $exp->title ?? 'Untitled',
                'category' => $category ? $category->name : 'Uncategorized',
                'details' => $exp->details ?? '',
                'amount' => (float) $exp->amount,
            ];
        }

        return response()->json([
            'month' => $month,
            'expenses' => $items,
        ]);
    }

    public function getUncategorizedEarningDetails()
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earnings = Earning::whereNull('earning_date')->get();

        $items = [];
        foreach ($earnings as $earning) {
            $category = $earning->earning_category_id ? EarningCategory::find($earning->earning_category_id) : null;
            $student = $earning->student_id ? StudentBasicInfo::find($earning->student_id) : null;

            $items[] = [
                'id' => $earning->id,
                'title' => $earning->title ?? 'Untitled',
                'category' => $category ? $category->name : 'Uncategorized',
                'student_name' => $student ? trim($student->first_name . ' ' . $student->last_name) : 'N/A',
                'amount' => (float) $earning->amount,
                'created_at' => $earning->created_at ? $earning->created_at->format('d M Y') : 'N/A',
            ];
        }

        return response()->json([
            'earnings' => $items,
        ]);
    }

    public function getUncategorizedExpenseDetails()
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses = Expense::whereNull('expense_date')->get();

        $items = [];
        foreach ($expenses as $exp) {
            $category = $exp->expense_category_id ? ExpenseCategory::find($exp->expense_category_id) : null;

            $items[] = [
                'id' => $exp->id,
                'title' => $exp->title ?? 'Untitled',
                'category' => $category ? $category->name : 'Uncategorized',
                'details' => $exp->details ?? '',
                'amount' => (float) $exp->amount,
                'created_at' => $exp->created_at ? $exp->created_at->format('d M Y') : 'N/A',
            ];
        }

        return response()->json([
            'expenses' => $items,
        ]);
    }
}