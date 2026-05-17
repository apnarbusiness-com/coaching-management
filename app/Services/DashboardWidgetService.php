<?php

namespace App\Services;

use App\Models\DashboardWidgetConfig;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardWidgetService
{
    /**
     * Get all widget keys defined in config
     */
    public static function getAllWidgetKeys(): array
    {
        return array_keys(config('dashboard_widgets.widgets', []));
    }

    /**
     * Get all widgets with their metadata
     */
    public static function getAllWidgets(): array
    {
        return config('dashboard_widgets.widgets', []);
    }

    /**
     * Get visible widget keys for a user based on their role
     */
    public static function getVisibleWidgetKeys(User $user): array
    {
        $role = $user->roles()->first();

        // dd($role);

        if (!$role) {
            return self::getDefaultWidgetKeys();
        }

        $dbConfigs = DashboardWidgetConfig::where('role_id', $role->id)
            ->where('is_visible', true)
            ->pluck('widget_key')
            ->toArray();
        // dd($dbConfigs);

        return $dbConfigs;

        // if (!empty($dbConfigs)) {
        //     return $dbConfigs;
        // }

        // $roleTitle = strtolower($role->title);
        // $roleDefaults = config('dashboard_widgets.role_defaults', []);

        // if (isset($roleDefaults[$roleTitle])) {
        //     return $roleDefaults[$roleTitle];
        // }

        return self::getDefaultWidgetKeys();
    }

    /**
     * Get default widget keys for admin
     */
    public static function getDefaultWidgetKeys(): array
    {
        return config('dashboard_widgets.role_defaults.user', []);
    }

    /**
     * Check if a specific widget is visible for user
     */
    public static function isWidgetVisible(User $user, string $widgetKey): bool
    {
        $visibleKeys = self::getVisibleWidgetKeys($user);
        return in_array($widgetKey, $visibleKeys);
    }

    /**
     * Get widget config for a specific role
     */
    public static function getWidgetConfigForRole(int $roleId): Collection
    {
        // $dbConfigs = DashboardWidgetConfig::where('role_id', $roleId)->get()->keyBy('widget_key');
        // $allWidgets = self::getAllWidgets();


        // $result = [];

        // foreach ($allWidgets as $key => $widget) {
        //     $isVisible = true;

        //     if (isset($dbConfigs[$key])) {
        //         $isVisible = $dbConfigs[$key]->is_visible;
        //     } else {
        //         $isVisible = $widget['default_visible'] ?? false;
        //     }

        //     $result[$key] = [
        //         'key' => $key,
        //         'label' => $widget['label'] ?? $key,
        //         'is_visible' => $isVisible,
        //         'has_db_config' => isset($dbConfigs[$key]),
        //     ];
        // }

        // dd($dbConfigs,$roleId,$allWidgets,$result);

        $widgets = DashboardWidgetConfig::where('role_id', $roleId)->get();
        $result = [];

        foreach ($widgets as $key => $widget) {
            // dd($widget);
            $result[$widget->widget_key] = [
                'key' => $widget->widget_key,
                'label' => str_replace('_', " ", $widget->widget_key),
                'is_visible' => $widget->is_visible
            ];
        }

        return collect($result);
    }

    /**
     * Save widget configuration for a role
     */
    public static function saveWidgetConfigForRole(int $roleId, array $widgetVisibility): void
    {
        foreach ($widgetVisibility as $widgetKey => $isVisible) {
            DashboardWidgetConfig::updateOrCreate(
                [
                    'role_id' => $roleId,
                    'widget_key' => $widgetKey,
                ],
                [
                    'is_visible' => (bool) $isVisible,
                ]
            );
        }

        DashboardWidgetConfig::where('role_id', $roleId)
            ->whereNotIn('widget_key', array_keys($widgetVisibility))
            ->delete();
    }

    /**
     * Get widget data needed for visible widgets
     * This is used by HomeController to load only necessary data
     */
    public static function getWidgetDataForUser(User $user): array
    {
        $visibleWidgets = self::getVisibleWidgetKeys($user);
        $data = [];

        if (in_array('total_profit', $visibleWidgets)) {
            $data['transactionData']['totalEarnings'] = \App\Models\Earning::sum('amount');
            $data['transactionData']['totalExpenses'] = \App\Models\Expense::sum('amount');
            $data['transactionData']['totalProfit'] = $data['transactionData']['totalEarnings'] - $data['transactionData']['totalExpenses'];
        }

        if (in_array('yearly_earnings', $visibleWidgets) || in_array('yearly_profit', $visibleWidgets) || in_array('yearly_expenses', $visibleWidgets)) {
            $thisYearEarnings = \App\Models\Earning::whereYear('earning_date', date('Y'))->sum('amount');
            $thisYearExpenses = \App\Models\Expense::whereYear('expense_date', date('Y'))->sum('amount');

            $data['transactionData']['thisYearEarnings'] = $thisYearEarnings;
            $data['transactionData']['thisYearExpenses'] = $thisYearExpenses;
            $data['transactionData']['thisYearProfit'] = $thisYearEarnings - $thisYearExpenses;
            $data['transactionData']['thisYearProfitMargin'] = $thisYearEarnings > 0
                ? round(($data['transactionData']['thisYearProfit'] / $thisYearEarnings) * 100, 2)
                : 0;
        }

        if (in_array('monthly_earnings', $visibleWidgets) || in_array('monthly_profit', $visibleWidgets) || in_array('monthly_expenses', $visibleWidgets)) {
            $thisMonthEarnings = \App\Models\Earning::whereMonth('earning_date', date('m'))->sum('amount');
            $thisMonthExpenses = \App\Models\Expense::whereMonth('expense_date', date('m'))->sum('amount');

            $data['transactionData']['thisMonthEarnings'] = $thisMonthEarnings;
            $data['transactionData']['thisMonthExpenses'] = $thisMonthExpenses;
            $data['transactionData']['thisMonthProfit'] = $thisMonthEarnings - $thisMonthExpenses;
            $data['transactionData']['thisMonthProfitMargin'] = $thisMonthEarnings > 0
                ? round(($data['transactionData']['thisMonthProfit'] / $thisMonthEarnings) * 100, 2)
                : 0;
        }

        if (in_array('student_due_alert', $visibleWidgets)) {
            $currentMonth = (int) now()->format('n');
            $currentYear = (int) now()->format('Y');

            $overdueDues = \App\Models\StudentMonthlyDue::where(function ($query) use ($currentMonth, $currentYear) {
                $query->where('year', '<', $currentYear)
                    ->orWhere(function ($q) use ($currentMonth, $currentYear) {
                        $q->where('year', $currentYear)->where('month', '<', $currentMonth);
                    });
            })
            ->where('due_remaining', '>', 0)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with('student')
            ->get();

            $overdueStudents = $overdueDues->map(function ($due) {
                return [
                    'id' => $due->student_id,
                    'id_no' => $due->student->id_no ?? 'N/A',
                    'name' => $due->student->first_name ?? 'N/A',
                    'month' => $due->month,
                    'year' => $due->year,
                    'due_amount' => $due->due_remaining,
                ];
            });

            $data['studentAlerts'] = [
                'overdueCount' => $overdueDues->count(),
                'overdueStudents' => $overdueStudents,
            ];
        }

        if (in_array('teacher_payment_alert', $visibleWidgets)) {
            $currentMonth = (int) now()->format('n');
            $currentYear = (int) now()->format('Y');

            $pendingPayments = \App\Models\TeachersPayment::where(function ($query) use ($currentMonth, $currentYear) {
                $query->where(function ($q) use ($currentMonth, $currentYear) {
                    $q->where('year', '<', $currentYear)
                        ->orWhere(function ($q2) use ($currentMonth, $currentYear) {
                            $q2->where('year', $currentYear)->where('month', '<', $currentMonth);
                        });
                })->where('payment_status', '!=', 'paid');
            })
            ->with('teacher')
            ->get();

            $pendingTeachers = $pendingPayments->map(function ($payment) {
                return [
                    'id' => $payment->teacher_id,
                    'name' => $payment->teacher->name ?? 'N/A',
                    'month' => $payment->month,
                    'year' => $payment->year,
                    'amount' => $payment->amount,
                ];
            });

            $data['teacherPaymentAlerts'] = [
                'pendingCount' => $pendingPayments->count(),
                'pendingTeachers' => $pendingTeachers,
            ];
        }

        if (in_array('monthly_revenue_chart', $visibleWidgets)) {
            $months = collect();
            $now = \Carbon\Carbon::now()->startOfMonth();

            for ($i = 5; $i >= 0; $i--) {
                $months->push($now->copy()->subMonths($i));
            }

            $earningData = \App\Models\Earning::selectRaw('
                YEAR(earning_date) as year,
                MONTH(earning_date) as month,
                SUM(amount) as total
                ')
                ->where('earning_date', '>=', $months->first())
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(fn($item) => $item->year . '-' . $item->month);

            $last6MonthsEarnings = [];
            $maxEarning = 1;

            foreach ($months as $month) {
                $key = $month->year . '-' . $month->month;
                $total = $earningData[$key]->total ?? 0;
                $last6MonthsEarnings[] = [
                    'month' => $month->format('M Y'),
                    'total' => $total,
                ];
                if ($total > $maxEarning) {
                    $maxEarning = $total;
                }
            }

            $lastMonth = $last6MonthsEarnings[4]['total'] ?? 0;
            $currentMonth = $last6MonthsEarnings[5]['total'] ?? 0;

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

            $data['lastSixMonthsData'] = [
                'earnings' => $last6MonthsEarnings,
                'maxEarning' => $maxEarning ?: 1,
                'growthPercentage' => $growthPercentage,
                'growthTrend' => $trend,
            ];
        }

        if (in_array('subject_earnings_table', $visibleWidgets)) {
            $data['subjectEarnings'] = collect();
        }

        return $data;
    }

    public static function getWidgetData(string $widgetKey): array
    {
        return match ($widgetKey) {
            'total_profit' => self::getTotalProfitData(),
            'yearly_earnings' => self::getYearlyEarningsData(),
            'yearly_expenses' => self::getYearlyExpensesData(),
            'yearly_profit' => self::getYearlyProfitData(),
            'monthly_earnings' => self::getMonthlyEarningsData(),
            'monthly_expenses' => self::getMonthlyExpensesData(),
            'monthly_profit' => self::getMonthlyProfitData(),
            'student_due_alert' => self::getStudentDueAlertData(),
            'teacher_payment_alert' => self::getTeacherPaymentAlertData(),
            'monthly_revenue_chart' => self::getMonthlyRevenueChartData(),
            'total_batches' => self::getTotalBatchesData(),
            'total_students' => self::getTotalStudentsData(),
            'total_teachers' => self::getTotalTeachersData(),
            'attendance_overview' => self::getAttendanceOverviewData(),
            'monthly_earnings_table' => self::getMonthlyEarningsTableData(),
            'recent_transactions' => self::getRecentTransactionsData(),
            default => ['error' => 'Unknown widget'],
        };
    }

    private static function getTotalProfitData(): array
    {
        $totalEarnings = \App\Models\Earning::sum('amount');
        $totalExpenses = \App\Models\Expense::sum('amount');

        $months = collect();
        $now = \Carbon\Carbon::now()->startOfMonth();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }
        $earningData = \App\Models\Earning::selectRaw('
            YEAR(earning_date) as year,
            MONTH(earning_date) as month,
            SUM(amount) as total
        ')
            ->where('earning_date', '>=', $months->first())
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . $item->month);

        $last6MonthsEarnings = [];
        $maxEarning = 1;
        foreach ($months as $month) {
            $key = $month->year . '-' . $month->month;
            $total = $earningData[$key]->total ?? 0;
            $last6MonthsEarnings[] = ['month' => $month->format('M Y'), 'total' => $total];
            if ($total > $maxEarning) $maxEarning = $total;
        }

        $lastMonth = $last6MonthsEarnings[4]['total'] ?? 0;
        $currentMonthVal = $last6MonthsEarnings[5]['total'] ?? 0;

        if ($lastMonth == 0 && $currentMonthVal == 0) {
            $trend = 'flat';
            $growthPercentage = 0;
        } elseif ($lastMonth == 0) {
            $trend = 'up';
            $growthPercentage = 100;
        } else {
            $change = (($currentMonthVal - $lastMonth) / $lastMonth) * 100;
            $growthPercentage = round(abs($change), 1);
            $trend = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
        }

        return [
            'totalProfit' => $totalEarnings - $totalExpenses,
            'totalEarnings' => $totalEarnings,
            'totalExpenses' => $totalExpenses,
            'earnings' => $last6MonthsEarnings,
            'maxEarning' => $maxEarning,
            'growthPercentage' => $growthPercentage,
            'growthTrend' => $trend,
        ];
    }

    private static function getYearlyEarningsData(): array
    {
        return ['thisYearEarnings' => \App\Models\Earning::whereYear('earning_date', date('Y'))->sum('amount')];
    }

    private static function getYearlyExpensesData(): array
    {
        return ['thisYearExpenses' => \App\Models\Expense::whereYear('expense_date', date('Y'))->sum('amount')];
    }

    private static function getYearlyProfitData(): array
    {
        $thisYearEarnings = \App\Models\Earning::whereYear('earning_date', date('Y'))->sum('amount');
        $thisYearExpenses = \App\Models\Expense::whereYear('expense_date', date('Y'))->sum('amount');
        $profit = $thisYearEarnings - $thisYearExpenses;
        $margin = $thisYearEarnings > 0 ? round(($profit / $thisYearEarnings) * 100, 2) : 0;
        return [
            'thisYearEarnings' => $thisYearEarnings,
            'thisYearExpenses' => $thisYearExpenses,
            'thisYearProfit' => $profit,
            'thisYearProfitMargin' => $margin,
        ];
    }

    private static function getMonthlyEarningsData(): array
    {
        return ['thisMonthEarnings' => \App\Models\Earning::whereMonth('earning_date', date('m'))->whereYear('earning_date', date('Y'))->sum('amount')];
    }

    private static function getMonthlyExpensesData(): array
    {
        return ['thisMonthExpenses' => \App\Models\Expense::whereMonth('expense_date', date('m'))->whereYear('expense_date', date('Y'))->sum('amount')];
    }

    private static function getMonthlyProfitData(): array
    {
        $thisMonthEarnings = \App\Models\Earning::whereMonth('earning_date', date('m'))->whereYear('earning_date', date('Y'))->sum('amount');
        $thisMonthExpenses = \App\Models\Expense::whereMonth('expense_date', date('m'))->whereYear('expense_date', date('Y'))->sum('amount');
        $profit = $thisMonthEarnings - $thisMonthExpenses;
        $margin = $thisMonthEarnings > 0 ? round(($profit / $thisMonthEarnings) * 100, 2) : 0;
        return [
            'thisMonthEarnings' => $thisMonthEarnings,
            'thisMonthExpenses' => $thisMonthExpenses,
            'thisMonthProfit' => $profit,
            'thisMonthProfitMargin' => $margin,
        ];
    }

    private static function getStudentDueAlertData(): array
    {
        $currentMonth = (int) now()->format('n');
        $currentYear = (int) now()->format('Y');
        $overdueDues = \App\Models\StudentMonthlyDue::where(function ($query) use ($currentMonth, $currentYear) {
            $query->where('year', '<', $currentYear)
                ->orWhere(function ($q) use ($currentMonth, $currentYear) {
                    $q->where('year', $currentYear)->where('month', '<', $currentMonth);
                });
        })
        ->where('due_remaining', '>', 0)
        ->whereIn('status', ['unpaid', 'partial'])
        ->with('student')
        ->get();

        $totalAmount = $overdueDues->sum('due_remaining');
        return [
            'overdueCount' => $overdueDues->count(),
            'totalAmount' => $totalAmount,
        ];
    }

    private static function getTeacherPaymentAlertData(): array
    {
        $currentMonth = (int) now()->format('n');
        $currentYear = (int) now()->format('Y');
        $pendingPayments = \App\Models\TeachersPayment::where(function ($query) use ($currentMonth, $currentYear) {
            $query->where(function ($q) use ($currentMonth, $currentYear) {
                $q->where('year', '<', $currentYear)
                    ->orWhere(function ($q2) use ($currentMonth, $currentYear) {
                        $q2->where('year', $currentYear)->where('month', '<', $currentMonth);
                    });
            })->where('payment_status', '!=', 'paid');
        })
        ->get();

        return [
            'pendingCount' => $pendingPayments->count(),
            'totalAmount' => $pendingPayments->sum('amount'),
        ];
    }

    private static function getMonthlyRevenueChartData(): array
    {
        $months = collect();
        $now = \Carbon\Carbon::now()->startOfMonth();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }
        $earningData = \App\Models\Earning::selectRaw('
            YEAR(earning_date) as year,
            MONTH(earning_date) as month,
            SUM(amount) as total
        ')
            ->where('earning_date', '>=', $months->first())
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . $item->month);

        $last6MonthsEarnings = [];
        $maxEarning = 1;
        foreach ($months as $month) {
            $key = $month->year . '-' . $month->month;
            $total = $earningData[$key]->total ?? 0;
            $last6MonthsEarnings[] = ['total' => $total];
            if ($total > $maxEarning) $maxEarning = $total;
        }

        $lastMonth = $last6MonthsEarnings[4]['total'] ?? 0;
        $currentMonthVal = $last6MonthsEarnings[5]['total'] ?? 0;

        if ($lastMonth == 0 && $currentMonthVal == 0) {
            $trend = 'flat';
            $growthPercentage = 0;
        } elseif ($lastMonth == 0) {
            $trend = 'up';
            $growthPercentage = 100;
        } else {
            $change = (($currentMonthVal - $lastMonth) / $lastMonth) * 100;
            $growthPercentage = round(abs($change), 1);
            $trend = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
        }

        return [
            'earnings' => $last6MonthsEarnings,
            'maxEarning' => $maxEarning,
            'growthPercentage' => $growthPercentage,
            'growthTrend' => $trend,
        ];
    }

    private static function getTotalBatchesData(): array
    {
        $total = \App\Models\Batch::count();
        $active = \App\Models\Batch::where('status', 1)->count();
        $inactive = $total - $active;

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    private static function getTotalStudentsData(): array
    {
        $total = \App\Models\StudentBasicInfo::count();
        $enrolledThisMonth = \App\Models\StudentBasicInfo::whereHas('batches', function ($q) {
            $q->whereYear('batch_student_basic_info.enrolled_at', now()->year)
              ->whereMonth('batch_student_basic_info.enrolled_at', now()->month);
        })->count();

        return [
            'total' => $total,
            'enrolled_this_month' => $enrolledThisMonth,
        ];
    }

    private static function getTotalTeachersData(): array
    {
        $total = \App\Models\Teacher::count();
        $active = \App\Models\Teacher::where('status', 1)->count();

        return [
            'total' => $total,
            'active' => $active,
        ];
    }

    private static function getAttendanceOverviewData(): array
    {
        $allAttendances = \App\Models\BatchAttendance::whereYear('attendance_date', now()->year)->get();
        $total = $allAttendances->count();
        $present = $allAttendances->where('status', 'present')->count();
        $absent = $allAttendances->where('status', 'absent')->count();
        $late = $allAttendances->where('status', 'late')->count();
        $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'percentage' => $percentage,
        ];
    }

    private static function getMonthlyEarningsTableData(): array
    {
        $rows = \App\Models\Earning::selectRaw('
            YEAR(earning_date) as year,
            MONTH(earning_date) as month,
            SUM(amount) as total
        ')
            ->whereYear('earning_date', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = [
                'month' => \Carbon\Carbon::createFromDate(null, $m)->format('F'),
                'total' => (float) ($rows[$m]->total ?? 0),
            ];
        }

        $grandTotal = array_sum(array_column($months, 'total'));

        return ['months' => $months, 'grand_total' => $grandTotal];
    }

    private static function getRecentTransactionsData(): array
    {
        $earnings = \App\Models\Earning::with('earning_category')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'type' => 'earning',
                'title' => $e->title,
                'category' => $e->earningCategory->name ?? 'N/A',
                'amount' => (float) $e->amount,
                'date' => $e->earning_date,
                'url' => route('admin.earnings.show', $e->id),
            ]);

        $expenses = \App\Models\Expense::with('expenseCategory')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'type' => 'expense',
                'title' => $e->title ?? 'Untitled',
                'category' => $e->expenseCategory->name ?? 'N/A',
                'amount' => (float) $e->amount,
                'date' => $e->expense_date,
                'url' => route('admin.expenses.show', $e->id),
            ]);

        $transactions = $earnings->concat($expenses)->sortByDesc('date')->take(10)->values();

        return ['transactions' => $transactions];
    }
}
