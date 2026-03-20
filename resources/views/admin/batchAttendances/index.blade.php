@extends('layouts.admin')
@section('content')
<style>
    .batch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        padding: 16px;
    }
    .batch-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .batch-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .batch-card .batch-name {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }
    .batch-card .subject-badge {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .batch-card .student-count {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 16px;
    }
    .batch-card .action-btn {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
    }
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .header-actions h3 {
        margin: 0;
    }
</style>

<div class="header-actions">
    <h3>Batch Attendance</h3>
</div>

<div class="batch-grid">
    @forelse($batches as $batch)
    <div class="batch-card">
        <div class="batch-name">{{ $batch['batch_name'] }}</div>
        <div class="subject-badge">{{ $batch['subject_name'] }}</div>
        <div class="student-count">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            {{ count($batch->students) }} Students
        </div>
        <a href="{{ route('admin.batch-attendances.take', $batch['id']) }}" class="btn btn-primary action-btn">
            Take Attendance
        </a>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <p class="text-muted">No batches found with enrolled students.</p>
    </div>
    @endforelse
</div>
@endsection
