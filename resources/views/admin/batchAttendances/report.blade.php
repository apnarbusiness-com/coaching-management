@extends('layouts.admin')
@section('content')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .date-filter {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .date-filter input {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }
    .report-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    .report-table th {
        background: #f8fafc;
        padding: 12px;
        text-align: left;
        font-weight: 600;
    }
    .report-table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .percentage-high { color: #16a34a; font-weight: 700; }
    .percentage-medium { color: #ca8a04; font-weight: 600; }
    .percentage-low { color: #dc2626; font-weight: 600; }
    @media (max-width: 768px) {
        .report-header {
            flex-direction: column;
            gap: 10px;
        }
        .date-filter {
            flex-wrap: wrap;
            width: 100%;
        }
        .date-filter input, .date-filter select {
            flex: 1;
            min-width: 140px;
        }
    }
</style>

<div class="attendance-page">
    <div class="report-header">
        <div>
            <h4>{{ $batch->batch_name }} - Attendance Report</h4>
            <p class="text-muted mb-0">{{ $batch->subject?->name ?? 'N/A' }}</p>
        </div>
        <div class="date-filter">
            <a href="{{ route('admin.batch-attendances.take', $batch->id) }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="GET" class="date-filter">
                <input type="date" name="start_date" value="{{ $startDate }}">
                <span>to</span>
                <input type="date" name="end_date" value="{{ $endDate }}">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Roll</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Late</th>
                            <th class="text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $student)
                        <?php
                            $percentageClass = $student['percentage'] >= 80 ? 'percentage-high' : ($student['percentage'] >= 60 ? 'percentage-medium' : 'percentage-low');
                        ?>
                        <tr>
                            <td>{{ $student['name'] }}</td>
                            <td>{{ $student['roll'] ?: 'N/A' }}</td>
                            <td class="text-center">{{ $student['total_days'] }}</td>
                            <td class="text-center text-success">{{ $student['present'] }}</td>
                            <td class="text-center text-danger">{{ $student['absent'] }}</td>
                            <td class="text-center text-warning">{{ $student['late'] }}</td>
                            <td class="text-center">
                                <span class="{{ $percentageClass }}">{{ $student['percentage'] }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No attendance records found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
