<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Earning;
use App\Models\Expense;
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

    public function getExpenseDetails(Request $request)
    {
        abort_if(Gate::denies('financial_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batchId = $request->input('batch_id');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        $batch = Batch::findOrFail($batchId);
        $expenses = Expense::where('batch_id', $batchId)
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->whereNotNull('teacher_id')
            ->get();

        $batchTeachers = $batch->teachers()->get()->keyBy('id');

        $teachers = [];
        foreach ($expenses as $exp) {
            $teacher = Teacher::find($exp->teacher_id);
            $teacherName = $teacher ? trim($teacher->name ?? $teacher->first_name . ' ' . $teacher->last_name) : 'Unknown';

            $batchTeacher = $batchTeachers->get($exp->teacher_id);
            $salaryType = $batchTeacher ? ($batchTeacher->pivot->salary_amount_type ?? 'fixed') : 'fixed';
            $salaryAmount = $batchTeacher ? ($batchTeacher->pivot->salary_amount ?? 0) : 0;

            $teachers[] = [
                'teacher_id' => $exp->teacher_id,
                'teacher_name' => $teacherName,
                'amount' => (float) $exp->amount,
                'salary_type' => $salaryType,
                'salary_amount' => (float) $salaryAmount,
            ];
        }

        return response()->json([
            'batch_name' => $batch->batch_name,
            'month' => $month,
            'teachers' => $teachers
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
}