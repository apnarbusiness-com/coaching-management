<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Widget Definitions
    |--------------------------------------------------------------------------
    |
    | Define all available dashboard widgets with their labels and default
    | visibility settings. Each widget has a unique key used throughout
    | the system for configuration.
    |
    | widget_key: unique identifier for the widget
    | label: display name shown in admin configuration
    | default_visible: whether widget is visible by default for new roles
    | permission: optional permission required (falls back to this if no role config)
    |
    */

    'widgets' => [
        // Level 1: Hero Cards (Financial Overview)
        'total_profit' => [
            'label' => 'Total Profit',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'yearly_earnings' => [
            'label' => 'Yearly Earning',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'yearly_expenses' => [
            'label' => 'Yearly Cost',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'yearly_profit' => [
            'label' => 'Yearly Profit',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'monthly_earnings' => [
            'label' => 'Monthly Earning',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'monthly_expenses' => [
            'label' => 'Monthly Cost',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'monthly_profit' => [
            'label' => 'Monthly Profit',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],

        // Level 2: Alert Zone
        'student_due_alert' => [
            'label' => 'Student Due Alert',
            'default_visible' => true,
            'permission' => 'dashboard_view_alerts',
        ],
        'teacher_payment_alert' => [
            'label' => 'Teacher Payment Pending Alert',
            'default_visible' => true,
            'permission' => 'dashboard_view_alerts',
        ],

        // Level 3: Charts & Financial Overview
        'monthly_revenue_chart' => [
            'label' => 'Monthly Revenue Breakdown Chart',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],
        'financial_overview' => [
            'label' => 'Financial Overview',
            'default_visible' => true,
            'permission' => 'dashboard_view_financials',
        ],

        // Level 4: Subject-wise Earnings Table
        'subject_earnings_table' => [
            'label' => 'Subject-wise Earnings',
            'default_visible' => true,
            'permission' => 'dashboard_view_reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Roles Configuration
    |--------------------------------------------------------------------------
    |
    | Define which roles see which widgets by default when no explicit
    | configuration exists in the database.
    |
    */
    'role_defaults' => [
        'admin' => [
            'total_profit',
            'yearly_earnings',
            'yearly_expenses',
            'yearly_profit',
            'monthly_earnings',
            'monthly_expenses',
            'monthly_profit',
            'student_due_alert',
            'teacher_payment_alert',
            'monthly_revenue_chart',
            'financial_overview',
            'subject_earnings_table',
        ],
        'manager' => [
            'total_profit',
            'yearly_earnings',
            'yearly_expenses',
            'yearly_profit',
            'monthly_earnings',
            'monthly_expenses',
            'monthly_profit',
            'student_due_alert',
            'monthly_revenue_chart',
            'financial_overview',
        ],
        'user' => [
            'student_due_alert',
        ],
    ],
];