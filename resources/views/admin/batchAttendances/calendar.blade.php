@extends('layouts.admin')
@section('content')
<style>
    .calendar-wrap { padding: 18px; }
    .calendar-hero {
        background: radial-gradient(1200px 300px at 20% -10%, #dbeafe 0%, #f8fafc 45%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 18px;
    }
    .calendar-hero h3 { margin: 0 0 6px; font-weight: 800; color: #0f172a; }
    .calendar-hero .subtitle { color: #64748b; font-size: 13px; }
    .filter-bar {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 6px; }
    .filter-group label { font-size: 13px; font-weight: 600; color: #475569; }
    .filter-group select {
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        min-width: 180px;
    }
    .month-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    .month-nav .current-month {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        min-width: 160px;
        text-align: center;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        background: #e2e8f0;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .calendar-header {
        background: #f8fafc;
        padding: 12px 8px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }
    .calendar-cell {
        background: #fff;
        min-height: 80px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .calendar-cell.no-class { background: #f1f5f9; }
    .calendar-cell .day-number {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }
    .calendar-cell .day-name {
        font-size: 10px;
        color: #94a3b8;
    }
    .stats-row {
        display: flex;
        gap: 8px;
        font-size: 10px;
        flex-wrap: wrap;
    }
    .stat-pill {
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .stat-pill.present { background: #dcfce7; color: #166534; }
    .stat-pill.absent { background: #fee2e2; color: #991b1b; }
    .stat-pill.late { background: #fef9c3; color: #854d0e; }
    .percentage-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
        text-align: center;
    }
    .percentage-badge.high { background: #dcfce7; color: #166534; }
    .percentage-badge.medium { background: #fef9c3; color: #854d0e; }
    .percentage-badge.low { background: #fee2e2; color: #991b1b; }
    .percentage-badge.none { background: #f1f5f9; color: #94a3b8; }
    .summary-stats {
        display: flex;
        gap: 24px;
        margin-top: 20px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
    }
    .summary-stat { display: flex; flex-direction: column; gap: 4px; }
    .summary-stat .label { font-size: 12px; color: #64748b; }
    .summary-stat .value { font-size: 20px; font-weight: 800; color: #0f172a; }
    .legend {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        font-size: 12px;
        color: #64748b;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; }
    .legend-dot.present { background: #22c55e; }
    .legend-dot.absent { background: #ef4444; }
    .legend-dot.late { background: #eab308; }
    .legend-dot.no-class { background: #e2e8f0; }
    @media (max-width: 768px) {
        .calendar-wrap { padding: 12px; }
        .filter-bar { flex-direction: column; }
        .filter-group select { width: 100%; }
        .calendar-cell { min-height: 60px; padding: 6px; }
    }
</style>

<div class="calendar-wrap">
    <div class="calendar-hero">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3>Attendance Calendar</h3>
                <div class="subtitle">View attendance by batch and month</div>
            </div>
            <a href="{{ route('admin.batch-attendances.index') }}" class="btn btn-info" style="padding: 10px 16px; border-radius: 8px;">
                <i class="fa fa-list"></i> List View
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.batch-attendances.calendar') }}" class="filter-bar">
        <div class="filter-group">
            <label>Select Batch</label>
            <select name="batch_id" onchange="this.form.submit()">
                <option value="">-- Select Batch --</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ $selectedBatchId == $batch->id ? 'selected' : '' }}>
                        {{ $batch->batch_name }} ({{ $batch->subject?->name ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Month</label>
            <input type="month" name="month" value="{{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}" onchange="this.form.submit()">
        </div>
    </form>

    @if($selectedBatchId)
    <!-- Batch ID: {{ $selectedBatchId }} -->
    <div class="legend">
        <div class="legend-item"><span class="legend-dot present"></span> Present</div>
        <div class="legend-item"><span class="legend-dot absent"></span> Absent</div>
        <div class="legend-item"><span class="legend-dot late"></span> Late</div>
        <div class="legend-item"><span class="legend-dot no-class"></span> No Class</div>
    </div>

    <div class="summary-stats">
        <div class="summary-stat">
            <span class="label">Total Students</span>
            <span class="value">{{ $totalStudents }}</span>
        </div>
        <div class="summary-stat">
            <span class="label">Total Classes</span>
            <span class="value">{{ collect($calendarData)->where('has_class', true)->count() }}</span>
        </div>
        <div class="summary-stat">
            <span class="label">Days Marked</span>
            <span class="value">{{ collect($calendarData)->where('total_marked', '>', 0)->count() }}</span>
        </div>
        <div class="summary-stat">
            <span class="label">Avg Attendance</span>
            <span class="value">
                @php
                    $marked = collect($calendarData)->where('total_marked', '>', 0);
                    $avg = $marked->count() > 0 ? round($marked->avg('percentage'), 1) : 0;
                @endphp
                {{ $avg }}%
            </span>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="calendar-header">Sat</div>
        <div class="calendar-header">Sun</div>
        <div class="calendar-header">Mon</div>
        <div class="calendar-header">Tue</div>
        <div class="calendar-header">Wed</div>
        <div class="calendar-header">Thu</div>
        <div class="calendar-header">Fri</div>

        @php
            $firstDay = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $startDayOfWeek = $firstDay->dayOfWeek;
        @endphp

        @for($i = 0; $i < $startDayOfWeek; $i++)
            <div class="calendar-cell" style="background: #f1f5f9;"></div>
        @endfor
        
        @foreach($calendarData as $day)
            @php
                $pctClass = $day['percentage'] === null ? 'none' : ($day['percentage'] >= 80 ? 'high' : ($day['percentage'] >= 50 ? 'medium' : 'low'));
            @endphp
            <div class="calendar-cell {{ !$day['has_class'] ? 'no-class' : '' }}">
                <span class="day-number">{{ $day['day'] }}</span>
                <span class="day-name">{{ substr($day['day_name'], 0, 3) }}</span>
                
                @if($day['has_class'])
                    <div class="stats-row">
                        @if($day['present'] > 0)<span class="stat-pill present">{{ $day['present'] }}P</span>@endif
                        @if($day['absent'] > 0)<span class="stat-pill absent">{{ $day['absent'] }}A</span>@endif
                        @if($day['late'] > 0)<span class="stat-pill late">{{ $day['late'] }}L</span>@endif
                    </div>
                    @if($day['total_marked'] > 0)
                        <span class="percentage-badge {{ $pctClass }}">{{ $day['percentage'] }}%</span>
                    @else
                        <span class="percentage-badge none">Not marked</span>
                    @endif
                @else
                    <span style="font-size: 10px; color: #94a3b8;">No class</span>
                @endif
            </div>
        @endforeach
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #94a3b8;">
        Please select a batch to view attendance calendar
    </div>
    @endif
</div>
@endsection