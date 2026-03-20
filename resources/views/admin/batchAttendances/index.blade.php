@extends('layouts.admin')
@section('content')
<style>
    .attendance-wrap {
        padding: 18px;
    }
    .attendance-hero {
        background: radial-gradient(1200px 300px at 20% -10%, #dbeafe 0%, #f8fafc 45%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 18px;
    }
    .attendance-hero h3 {
        margin: 0 0 6px;
        font-weight: 800;
        color: #0f172a;
    }
    .attendance-hero .subtitle {
        color: #64748b;
        font-size: 13px;
    }
    .batch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }
    .batch-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .batch-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.12);
    }
    .batch-name {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }
    .subject-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4338ca;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        width: fit-content;
    }
    .student-count {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        font-size: 13px;
    }
    .student-count .count-pill {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 12px;
    }
    .action-btn {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        font-weight: 700;
        text-transform: none;
        letter-spacing: 0.2px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 16px;
        color: #94a3b8;
    }
    @media (max-width: 768px) {
        .attendance-wrap { padding: 12px; }
        .attendance-hero { padding: 16px; }
        .batch-card { padding: 16px; }
    }
</style>

<div class="attendance-wrap">
    <div class="attendance-hero">
        <h3>Batch Attendance</h3>
        <div class="subtitle">
            Current enrollment month: {{ $attendanceMonthLabel ?? '' }}
        </div>
    </div>

    <div class="batch-grid">
        @forelse($batches as $batch)
        <div class="batch-card">
            <div class="batch-name">{{ $batch->batch_name }}</div>
            <div class="subject-badge">
                <i class="fa fa-book"></i> {{ $batch->subject?->name ?? 'N/A' }}
            </div>
            <div class="student-count">
                <i class="fa fa-users"></i>
                <span class="count-pill">{{ (int) ($batch->students_count ?? 0) }}</span>
                Students Enrolled
            </div>
            <a href="{{ route('admin.batch-attendances.take', $batch->id) }}" class="btn btn-primary action-btn">
                Take Attendance
            </a>
        </div>
        @empty
        <div class="empty-state">
            <p>No batches found with enrolled students.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
