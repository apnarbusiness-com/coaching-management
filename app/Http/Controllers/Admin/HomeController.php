<?php

namespace App\Http\Controllers\Admin;

use App\Models\Earning;
use App\Models\Expense;
use App\Models\StudentMonthlyDue;
use App\Services\DueCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController
{

    public function loadAdminDashboard()
    {
        $totalEarnings = Earning::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $totalProfit = $totalEarnings - $totalExpenses;

        $thisYearEarnings = Earning::whereYear('earning_date', date('Y'))->sum('amount');
        $thisYearExpenses = Expense::whereYear('expense_date', date('Y'))->sum('amount');
        $thisYearProfit = $thisYearEarnings - $thisYearExpenses;


        $thisYearProfitMargin = $thisYearEarnings > 0
            ? round(($thisYearProfit / $thisYearEarnings) * 100, 2)
            : 0;

        $thisMonthEarnings = Earning::whereMonth('earning_date', date('m'))->sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', date('m'))->sum('amount');
        $thisMonthProfit = $thisMonthEarnings - $thisMonthExpenses;
        $thisMonthProfitMargin = $thisMonthEarnings > 0
            ? round(($thisMonthProfit / $thisMonthEarnings) * 100, 2)
            : 0;


        $transactionData = [
            'totalEarnings' => $totalEarnings,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,

            'thisYearEarnings' => $thisYearEarnings,
            'thisYearExpenses' => $thisYearExpenses,
            'thisYearProfit' => $thisYearProfit,
            'thisYearProfitMargin' => $thisYearProfitMargin,

            'thisMonthEarnings' => $thisMonthEarnings,
            'thisMonthExpenses' => $thisMonthExpenses,
            'thisMonthProfit' => $thisMonthProfit,
            'thisMonthProfitMargin' => $thisMonthProfitMargin,
        ];

        // return $transactionData;

        // Step 1: last 6 months range (current month included)
        $months = collect();
        $now = Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        // Step 2: DB query (earning_date based)
        $data = Earning::selectRaw('
            YEAR(earning_date) as year,
            MONTH(earning_date) as month,
            SUM(amount) as total
            ')
            ->where('earning_date', '>=', $months->first())
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . $item->month);

        // Step 3: Format output (zero-fill)
        $last6MonthsEarnings = [];

        foreach ($months as $month) {
            $key = $month->year . '-' . $month->month;

            $last6MonthsEarnings[] = [
                'month' => $month->format('M Y'),
                'total' => $data[$key]->total ?? 0,
            ];
        }

        // return $last6MonthsEarnings;


        $maxEarningInLast6MonthsEarnings = collect($last6MonthsEarnings)->max('total') ?: 1;

        $lastMonth = $last6MonthsEarnings[4]['total'] ?? 0;
        $currentMonth = $last6MonthsEarnings[5]['total'] ?? 0;

        // $growthPercentage = $lastMonth > 0
        //     ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1)
        //     : 0;

        if ($lastMonth == 0 && $currentMonth == 0) {
            $trend = 'flat';
            $growthPercentage = 0;
        } elseif ($lastMonth == 0) {
            $trend = 'up';
            $growthPercentage = 100;
        } else {
            $change = (($currentMonth - $lastMonth) / $lastMonth) * 100;
            $growthPercentage = round(abs($change), 1);
            $trend = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
        }

        $lastSixMonthsData = [
            'earnings' => $last6MonthsEarnings,
            'maxEarning' => $maxEarningInLast6MonthsEarnings,
            'growthPercentage' => $growthPercentage,
            'growthTrend' => $trend,

        ];

        // return $last6MonthsEarnings;

        return view('home', compact('transactionData', 'lastSixMonthsData'));
    }

    public function loadStudentDashboard()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return view('student.home', [
                'latestPayment' => null,
                'paymentHistory' => collect(),
                'totalDue' => 0,
                'unpaidDues' => collect(),
            ]);
        }

        $dueService = new DueCalculationService();
        $dueInfo = $dueService->calculateStudentTotalDue($student->id);

        $paymentQuery = Earning::with('earning_category')
            ->where('student_id', $student->id)
            ->whereHas('earning_category', function ($query) {
                $query->where('is_student_connected', true);
            })
            ->orderByDesc('earning_date')
            ->orderByDesc('id');

        $latestPayment = (clone $paymentQuery)->first();
        $paymentHistory = $paymentQuery->take(20)->get();

        return view('student.home', compact('latestPayment', 'paymentHistory', 'dueInfo'));
    }

    public function studentProfile()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('admin.home')->with('error', 'Student profile not found.');
        }

        $student->load('class', 'section', 'shift', 'academicBackground', 'subjects', 'studentDetails', 'batches');

        return view('student.profile', compact('student'));
    }

    public function index()
    {
        $isStudent = auth()->check()
            && auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['student'])->exists();

        // $homeView = $isStudent ? 'student.home' : 'home';
        if ($isStudent) {
            return $this->loadStudentDashboard();
        } else {
            return $this->loadAdminDashboard();
        }
    }



    public function getMonthLyRevenueBreakdown(Request $request, $months = 6)
    {
        // only allow ajax/api
        // if (!$request->ajax()) {
        //     abort(404);
        // }

        $end = Carbon::now()->startOfMonth();
        $start = $end->copy()->subMonths($months - 1);

        // fetch grouped earnings
        $rows = Earning::selectRaw('
            YEAR(earning_date) as year,
            MONTH(earning_date) as month,
            SUM(amount) as total
        ')
            ->whereBetween('earning_date', [$start, $end->copy()->endOfMonth()])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . $item->month);

        // zero-fill months
        $labels = [];
        $data = [];

        for ($i = 0; $i < $months; $i++) {
            $date = $start->copy()->addMonths($i);
            $key = $date->year . '-' . $date->month;

            $labels[] = $date->format('M Y');
            $data[] = $rows[$key]->total ?? 0;
        }


        // $labels = [
        //     "Aug 2025",
        //     "Sep 2025",
        //     "Oct 2025",
        //     "Nov 2025",
        //     "Dec 2025",
        //     "Jan 2026"
        // ];
        // $data = [
        //     2,
        //     3,
        //     3,
        //     40,
        //     50,
        //     60
        // ];

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }
}
