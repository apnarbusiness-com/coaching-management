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
            $data['studentAlerts'] = [
                'overdueCount' => 0,
                'overdueStudents' => collect(),
            ];
        }

        if (in_array('teacher_payment_alert', $visibleWidgets)) {
            $data['teacherPaymentAlerts'] = [
                'pendingCount' => 0,
                'pendingTeachers' => collect(),
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
}
