<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Earning;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
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

        $activeBatches = Batch::where('status', 1)->count();
        $totalExpense = Expense::whereYear('expense_date', $year)->sum('amount');
        $netProfit = $grandTotal - $totalExpense;
        $profitMargin = $grandTotal > 0 ? round(($netProfit / $grandTotal) * 100, 1) : 0;

        $currentMonth = date('n');
        $currentQuarter = ceil($currentMonth / 3);
        $fiscalMonth = date('M');
        $fiscalCycle = 'Q' . $currentQuarter . ' - ' . $fiscalMonth;

        $previousYear = $year - 1;
        $previousYearEarning = Earning::whereYear('earning_date', $previousYear)->sum('amount');
        $percentChange = $previousYearEarning > 0 ? round((($grandTotal - $previousYearEarning) / $previousYearEarning) * 100, 1) : 0;

        return view('admin.financialLedgers.index', compact(
            'batchEarnings',
            'totalPerMonth',
            'grandTotal',
            'months',
            'year',
            'activeBatches',
            'totalExpense',
            'netProfit',
            'profitMargin',
            'fiscalCycle',
            'percentChange'
        ));
    }
}