@extends('layouts.admin')
@section('title', 'Attendance View — Monthly Overview')
@section('content')
<style>
    .att-view-wrap {
        padding: 18px;
    }
    .att-view-hero {
        background: radial-gradient(1200px 300px at 20% -10%, #dbeafe 0%, #f8fafc 45%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 18px;
    }
    .att-view-hero h3 {
        margin: 0 0 6px;
        font-weight: 800;
        color: #0f172a;
    }
    .att-view-hero .subtitle {
        color: #64748b;
        font-size: 13px;
    }
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-group label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .filter-group select,
    .filter-group input {
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        background: #fff;
        min-width: 160px;
    }
    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
    }
    .btn-filter {
        padding: 9px 20px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: opacity 0.2s;
    }
    .btn-filter:hover { opacity: 0.9; }
    .btn-export {
        padding: 9px 20px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-export:hover { background: #e0e7ff; }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-icon.blue { background: #dbeafe; color: #1e40af; }
    .stat-icon.red { background: #fee2e2; color: #dc2626; }
    .stat-icon.green { background: #dcfce7; color: #16a34a; }
    .stat-icon.amber { background: #fef3c7; color: #d97706; }
    .stat-info .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .stat-info .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .stat-info .stat-value.small { font-size: 16px; }
    .stat-info .stat-value.text-danger { color: #dc2626; }
    .table-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(15,23,42,0.06);
    }
    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-scroll::-webkit-scrollbar { height: 6px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .att-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px;
        font-size: 13px;
    }
    .att-table th {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        text-align: left;
    }
    .att-table th.sticky-left {
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 2;
    }
    .att-table th.sticky-left-2 {
        position: sticky;
        left: 60px;
        background: #f8fafc;
        z-index: 2;
    }
    .att-table th.sticky-left-3 {
        position: sticky;
        left: 200px;
        background: #f8fafc;
        z-index: 2;
    }
    .att-table th.text-center { text-align: center; }
    .att-table th.cal-header {
        text-align: center;
        padding: 6px 2px;
        font-size: 9px;
        border-left: 1px solid #e2e8f0;
        min-width: 28px;
        width: 28px;
    }
    .att-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .att-table td.sticky-left {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 1;
    }
    .att-table td.sticky-left-2 {
        position: sticky;
        left: 60px;
        background: #fff;
        z-index: 1;
    }
    .att-table td.sticky-left-3 {
        position: sticky;
        left: 200px;
        background: #fff;
        z-index: 1;
    }
    .att-table tr:hover td {
        background: #f8fafc;
    }
    .att-table tr:hover td.sticky-left,
    .att-table tr:hover td.sticky-left-2,
    .att-table tr:hover td.sticky-left-3 {
        background: #f8fafc;
    }
    .att-table tr.batch-sep td {
        border-bottom: 2px solid #e2e8f0;
    }
    .student-name {
        font-weight: 600;
        color: #0f172a;
    }
    .student-meta {
        font-size: 11px;
        color: #94a3b8;
    }
    .batch-badge {
        display: inline-block;
        background: #eef2ff;
        color: #4338ca;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .rate-pill {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
    }
    .rate-high { background: #dcfce7; color: #166534; }
    .rate-medium { background: #fef9c3; color: #854d0e; }
    .rate-low { background: #fee2e2; color: #991b1b; }
    .cal-cell {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        border-radius: 6px;
        margin: 0 auto;
    }
    .cal-cell.present { background: #22c55e; color: #fff; }
    .cal-cell.absent { background: #ef4444; color: #fff; }
    .cal-cell.late { background: #f59e0b; color: #fff; }
    .cal-cell.not-marked { background: #fff; border: 1px solid #e2e8f0; color: #94a3b8; }
    .cal-cell.no-class { background: #f1f5f9; border: 1px solid #f1f5f9; color: #cbd5e1; }
    .legend-bar {
        padding: 12px 16px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }
    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        flex-shrink: 0;
    }
    .legend-dot.present { background: #22c55e; }
    .legend-dot.absent { background: #ef4444; }
    .legend-dot.late { background: #f59e0b; }
    .legend-dot.not-marked { background: #fff; border: 1px solid #e2e8f0; }
    .legend-dot.no-class { background: #f1f5f9; border: 1px solid #f1f5f9; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; }
    .total-row td {
        font-weight: 700;
        background: #f1f5f9 !important;
        border-top: 2px solid #e2e8f0;
    }
    .td-center { text-align: center; }
    @media (max-width: 768px) {
        .att-view-wrap { padding: 10px; }
        .att-view-hero { padding: 14px; }
        .att-view-hero h3 { font-size: 18px; }
        .att-view-hero > div { flex-direction: column; gap: 10px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .stat-card { padding: 10px; gap: 10px; }
        .stat-icon { width: 36px; height: 36px; font-size: 16px; }
        .stat-info .stat-value { font-size: 18px; }
        .stat-info .stat-value.small { font-size: 14px; }
        .filter-bar { flex-direction: column; gap: 10px; padding: 14px; }
        .filter-group { width: 100%; }
        .filter-group select,
        .filter-group input { width: 100%; min-width: unset; }
        .filter-group input[type="month"] { width: 100%; }
        .btn-filter, .btn-export {
            width: 100%;
            justify-content: center;
            padding: 11px 20px;
        }
        .legend-bar { flex-wrap: wrap; gap: 10px; }
        .legend-bar > div:last-child { margin-left: 0; width: 100%; text-align: center; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }
        .stat-card { flex-direction: column; text-align: center; padding: 10px 8px; }
        .stat-info .stat-value { font-size: 16px; }
        .stat-info .stat-label { font-size: 10px; }
        .att-view-hero { padding: 12px; }
        .filter-bar { padding: 10px; }
    }
</style>

<div class="att-view-wrap">
    <div class="att-view-hero">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3>Attendance View</h3>
                <div class="subtitle">Monthly overview — {{ $monthLabel }}</div>
            </div>
            <a href="{{ route('admin.batch-attendances.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.batch-attendances.view') }}" class="filter-bar">
        <div class="filter-group">
            <label>Month</label>
            <input type="month" name="month"
                value="{{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}">
        </div>
        <div class="filter-group">
            <label>Batch</label>
            <select name="batch_id">
                <option value="">All Batches</option>
                @foreach ($batches as $batch)
                    <option value="{{ $batch['id'] }}" {{ $selectedBatchId == $batch['id'] ? 'selected' : '' }}>
                        {{ $batch['name'] }} ({{ $batch['subject'] }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="flex:1; min-width:180px;">
            <label>Search</label>
            <input type="text" id="student-search" placeholder="Search by name or roll..."
                style="padding:9px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:14px; background:#fff; width:100%; box-sizing:border-box;">
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa fa-filter"></i> Apply Filters
        </button>
        <button type="button" class="btn-export" onclick="window.print()">
            <i class="fa fa-download"></i> Export PDF
        </button>
    </form>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Students</div>
                <div class="stat-value">{{ $totalStudents }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-label">Avg Attendance</div>
                <div class="stat-value">{{ $avgRate }}%</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fa fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <div class="stat-label">Critical Drop (&lt;50%)</div>
                <div class="stat-value text-danger">{{ $criticalDrop }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fa fa-trophy"></i></div>
            <div class="stat-info">
                <div class="stat-label">Top Performer</div>
                <div class="stat-value small">{{ $topBatch ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-scroll">
            <table class="att-table">
                <thead>
                    <tr>
                        <th class="sticky-left" style="width:60px;">ID</th>
                        <th class="sticky-left-2" style="width:140px;">Name</th>
                        <th class="sticky-left-3" style="width:120px;">Batch</th>
                        <th class="text-center" style="width:60px;">Total</th>
                        <th class="text-center" style="width:50px;">Att</th>
                        <th class="text-center" style="width:70px;">Rate</th>
                        @php
                            $daysInMonth = Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
                            $dayLabels = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                        @endphp
                        <th class="cal-header" colspan="{{ $daysInMonth }}" style="border-left:1px solid #e2e8f0;">
                            Class Date &amp; Day (Calendar Wise)
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6" style="border:none;"></th>
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, $day);
                                $label = $dayLabels[$date->dayOfWeek];
                            @endphp
                            <th class="cal-header">{{ $label }}<br>{{ $day }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr data-search="{{ strtolower($row['student_name'].' '.$row['roll'].' '.$row['id_no'].' '.$row['batch_name']) }}">
                            <td class="sticky-left" style="font-size:12px; color:#64748b;">{{ $row['id_no'] ?: $row['roll'] }}</td>
                            <td class="sticky-left-2">
                                <div class="student-name">{{ $row['student_name'] }}</div>
                                <div class="student-meta">Roll: {{ $row['roll'] ?: 'N/A' }}</div>
                            </td>
                            <td class="sticky-left-3">
                                <span class="batch-badge">{{ $row['batch_name'] }}</span>
                            </td>
                            <td class="td-center" style="font-weight:600;">{{ $row['total_days'] }}</td>
                            <td class="td-center" style="font-weight:600;">{{ $row['present'] }}</td>
                            <td class="td-center">
                                @php
                                    $rateClass = $row['att_rate'] >= 80 ? 'rate-high' : ($row['att_rate'] >= 50 ? 'rate-medium' : 'rate-low');
                                @endphp
                                <span class="rate-pill {{ $rateClass }}">{{ $row['att_rate'] }}%</span>
                            </td>
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $status = $row['daily'][$day] ?? 'no_class';
                                    $cellClass = $status === 'present' ? 'present' : ($status === 'absent' ? 'absent' : ($status === 'late' ? 'late' : ($status === 'not_marked' ? 'not-marked' : 'no-class')));
                                @endphp
                                <td class="td-center" style="padding:2px;">
                                    <div class="cal-cell {{ $cellClass }}">{{ $day }}</div>
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $daysInMonth }}">
                                <div class="empty-state">
                                    <div class="icon"><i class="fa fa-calendar-times"></i></div>
                                    <p>No attendance records found for the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="legend-bar">
            <div class="legend-item">
                <span class="legend-dot present"></span> Present
            </div>
            <div class="legend-item">
                <span class="legend-dot absent"></span> Absent
            </div>
            <div class="legend-item">
                <span class="legend-dot late"></span> Late
            </div>
            <div class="legend-item">
                <span class="legend-dot not-marked"></span> Not Marked
            </div>
            <div class="legend-item">
                <span class="legend-dot no-class"></span> No Class
            </div>
            <div class="ml-auto" style="font-size:12px; color:#94a3b8; font-style:italic; margin-left:auto;">
                Displaying records for {{ $monthLabel }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('student-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const term = this.value.trim().toLowerCase();
        document.querySelectorAll('.att-table tbody tr').forEach(function(row) {
            if (!row.hasAttribute('data-search')) return;
            const searchData = row.getAttribute('data-search');
            row.style.display = term === '' || searchData.includes(term) ? '' : 'none';
        });
    });
});
</script>
@endsection
