<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Earning;
use App\Models\Expense;
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
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $batches = Batch::where('status', 1)
            ->orderBy('batch_name')
            ->with(['subject'])
            ->get();

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

        $batchExpenses = [];
        foreach ($batches as $batch) {
            $monthlyExpenses = [];
            $totalExpenses = 0;

            $batchDetailExpenses = Expense::where('batch_id', $batch->id)
                ->where('expense_year', $year)
                ->whereNotNull('teacher_id')
                ->get()
                ->groupBy('teacher_id');

            $teacherDetails = [];
            foreach ($batchDetailExpenses as $teacherId => $expenses) {
                $teacher = Teacher::find($teacherId);
                $teacherName = $teacher ? ($teacher->name ?? ($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) : 'Unknown';
                $teacherName = trim($teacherName);

                $monthlyBreakdown = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyBreakdown[$m] = 0;
                }

                foreach ($expenses as $exp) {
                    $monthlyBreakdown[$exp->expense_month] = $exp->amount;
                }

                $teacherDetails[] = [
                    'teacher_id' => $teacherId,
                    'teacher_name' => $teacherName,
                    'monthly' => $monthlyBreakdown,
                    'total' => $expenses->sum('amount'),
                ];
            }

            for ($m = 1; $m <= 12; $m++) {
                $amount = Expense::where('batch_id', $batch->id)
                    ->where('expense_month', $m)
                    ->where('expense_year', $year)
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
                    'teachers' => $teacherDetails,
                ];
            }
        }

        $totalExpensePerMonth = [];
        $grandTotalExpense = 0;
        for ($m = 1; $m <= 12; $m++) {
            $amount = Expense::where('expense_month', $m)
                ->where('expense_year', $year)
                ->sum('amount');
            $totalExpensePerMonth[$m] = $amount;
            $grandTotalExpense += $amount;
        }

        $activeBatches = Batch::where('status', 1)->count();
        $netProfit = $grandTotal - $grandTotalExpense;
        $profitMargin = $grandTotal > 0 ? round(($netProfit / $grandTotal) * 100, 1) : 0;

        $previousYear = $year - 1;
        $previousYearEarning = Earning::whereYear('earning_date', $previousYear)->sum('amount');
        $percentChange = $previousYearEarning > 0 ? round((($grandTotal - $previousYearEarning) / $previousYearEarning) * 100, 1) : 0;

        return view('admin.financialLedgers.index', compact(
            'batchEarnings',
            'totalPerMonth',
            'grandTotal',
            'months',
            'year',
            'batchExpenses',
            'totalExpensePerMonth',
            'grandTotalExpense',
            'activeBatches',
            'netProfit',
            'profitMargin',
            'percentChange'
        ));
    }
}