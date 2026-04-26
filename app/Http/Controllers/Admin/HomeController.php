<?php

namespace App\Http\Controllers\Admin;

use App\Models\Earning;
use App\Models\Expense;
use App\Models\StudentMonthlyDue;
use App\Models\Batch;
use App\Services\DueCalculationService;
use App\Services\DashboardWidgetService;
use Carbon\Carbon;
use App\Models\TeachersPayment;
use App\Services\TeacherSalaryCalculationService;
use Illuminate\Http\Request;

class HomeController
{

    public function loadAdminDashboard()
    {
        $user = auth()->user();


        $visibleWidgets = DashboardWidgetService::getVisibleWidgetKeys($user);

        // return $visibleWidgets;

        $widgetData = DashboardWidgetService::getWidgetDataForUser($user);

        $transactionData = $widgetData['transactionData'] ?? [];
        $lastSixMonthsData = $widgetData['lastSixMonthsData'] ?? [
            'earnings' => [],
            'maxEarning' => 1,
            'growthPercentage' => 0,
            'growthTrend' => 'flat',
        ];

        $batches = Batch::orderBy('batch_name')->get();

        $currentMonth = (int) now()->format('n');
        $currentYear = (int) now()->format('Y');

        $studentAlerts = $transactionData['studentAlerts'] ?? ['overdueCount' => 0, 'overdueStudents' => collect()];
        $teacherPaymentAlerts = $transactionData['teacherPaymentAlerts'] ?? ['pendingCount' => 0, 'pendingTeachers' => collect()];

        return view('home', compact('transactionData', 'lastSixMonthsData', 'visibleWidgets', 'batches', 'currentMonth', 'currentYear', 'studentAlerts', 'teacherPaymentAlerts'));
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
                'myBatches' => collect(),
            ]);
        }

        $salaryService = new TeacherSalaryCalculationService();

        $dueService = new DueCalculationService($salaryService);
        $dueInfo = $dueService->calculateStudentTotalDue($student->id);

        $unpaidDues = StudentMonthlyDue::where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with(['batch', 'academicClass'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $paymentQuery = Earning::with('earning_category')
            ->where('student_id', $student->id)
            ->whereHas('earning_category', function ($query) {
                $query->where('is_student_connected', true);
            })
            ->orderByDesc('earning_date')
            ->orderByDesc('id');

        $latestPayment = (clone $paymentQuery)->first();
        $paymentHistory = $paymentQuery->take(20)->get();

        $myBatches = $student->batches()
            ->with(['subjects', 'class', 'teachers'])
            ->wherePivot('enrolled_at', '<=', now())
            ->get();

        return view('student.home', compact('latestPayment', 'paymentHistory', 'dueInfo', 'unpaidDues', 'myBatches'));
    }

    public function loadTeacherDashboard()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return view('teacher.home', [
                'teacher' => null,
                'myBatches' => collect(),
                'paymentHistory' => collect(),
                'partialPayments' => collect(),
            ]);
        }

        $teacher->load('subjects', 'batches');

        $myBatches = $teacher->batches()
            ->with(['subjects', 'class', 'students'])
            ->get();

        $paymentHistory = TeachersPayment::with(['teacher', 'transactions'])
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('id')
            ->take(20)
            ->get();

        $partialPayments = $paymentHistory->filter(function ($p) {
            return in_array($p->payment_status, ['partial', 'pending']);
        });

        return view('teacher.home', compact('teacher', 'myBatches', 'paymentHistory', 'partialPayments'));
    }
    public function teacherProfile()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('admin.home')->with('error', 'Teacher profile not found.');
        }

        $teacher->load('user', 'subjects', 'batches');

        return view('teacher.profile', compact('teacher'));
    }

    public function index()
    {
        // $user = auth()->user();
        // $permissions = [];
        // $roles = $user->roles;

        // return response()->json([
        //     'permissions' => $permissions,
        //     'roles' => $roles,
        // ]);
        $isStudent = auth()->check()
            && auth()->user()->isStudent();
        $isTeacher = auth()->check()
            && auth()->user()->isTeacher();
        // $homeView = $isStudent ? 'student.home' : 'home';
        if ($isTeacher) {
            return $this->loadTeacherDashboard();
        } elseif ($isStudent) {
            return $this->loadStudentDashboard();
        } else {
            return $this->loadAdminDashboard();
        }
    }

    public function myIdCard()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('admin.home')->with('error', 'Teacher profile not found.');
        }

        return view('teacher.id_card', compact('teacher'));
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

    public function myBatches()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('admin.home')->with('error', 'Student profile not found.');
        }

        $batches = $student->batches()
            ->with(['subjects', 'class', 'teachers'])
            ->wherePivot('enrolled_at', '<=', now())
            ->get();

        return view('student.my-batches', compact('batches'));
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

    public function getAlertData(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $batchId = $request->input('batch_id');

        $studentQuery = StudentMonthlyDue::where('month', $month)
            ->where('year', $year)
            ->whereIn('status', ['unpaid', 'partial']);

        if ($batchId) {
            $studentQuery->where('batch_id', $batchId);
        }

        $dueRecords = $studentQuery->get();
        $studentData = [
            'count' => $dueRecords->count(),
            'totalAmount' => $dueRecords->sum('due_remaining'),
            'students' => $dueRecords->take(10)->map(function ($due) {
                return [
                    'id' => $due->student_id,
                    'name' => $due->student->first_name . ' ' . ($due->student->last_name ?? ''),
                    'batch' => $due->batch->batch_name ?? 'N/A',
                    'due_amount' => $due->due_amount,
                    'paid_amount' => $due->paid_amount,
                    'due_remaining' => $due->due_remaining,
                ];
            })->values(),
        ];

        $teacherQuery = TeachersPayment::where('month', $month)
            ->where('year', $year)
            ->whereIn('payment_status', ['due', 'partial']);

        if ($batchId) {
            $teacherQuery->where('batch_id', $batchId);
        }

        $paymentRecords = $teacherQuery->get();
        $teacherData = [
            'count' => $paymentRecords->count(),
            'totalAmount' => $paymentRecords->sum('amount'),
            'teachers' => $paymentRecords->take(10)->map(function ($payment) {
                $teacher = \App\Models\Teacher::find($payment->teacher_id);
                return [
                    'id' => $payment->teacher_id,
                    'name' => $teacher?->name ?? 'Unknown',
                    'batch' => $payment->batch->batch_name ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_status' => $payment->payment_status,
                ];
            })->values(),
        ];

        return response()->json([
            'student_alerts' => $studentData,
            'teacher_alerts' => $teacherData,
        ]);
    }
}
