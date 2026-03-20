@extends('layouts.admin')
@section('content')
<style>
    @media (max-width: 768px) {
        .attendance-page {
            padding: 10px !important;
        }
        .attendance-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            padding: 10px 0;
            border-bottom: 2px solid #e2e8f0;
        }
        .stats-bar {
            display: flex;
            justify-content: space-around;
            background: #f8fafc;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .stat-item { text-align: center; }
        .stat-item .value { font-size: 18px; font-weight: 700; }
        .stat-item.present .value { color: #16a34a; }
        .stat-item.absent .value { color: #dc2626; }
        .stat-item.late .value { color: #ca8a04; }
        .stat-item.marked .value { color: #2563eb; }
        .student-card {
            display: flex;
            align-items: center;
            padding: 12px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s;
        }
        .student-card.has-due {
            border-left: 4px solid #dc2626;
            background: #fef2f2;
        }
        .student-card.has-due .due-badge {
            display: inline-flex;
        }
        .student-card .student-info {
            flex: 1;
            min-width: 0;
        }
        .student-card .student-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .student-card .student-meta {
            font-size: 11px;
            color: #64748b;
        }
        .student-card .due-badge {
            display: none;
            background: #dc2626;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 4px;
        }
        .attendance-buttons {
            display: flex;
            gap: 6px;
        }
        .btn-attend {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }
        .btn-attend.present { background: #dcfce7; color: #16a34a; }
        .btn-attend.absent { background: #fee2e2; color: #dc2626; }
        .btn-attend.late { background: #fef9c3; color: #ca8a04; }
        .btn-attend.active.present { background: #16a34a; color: white; border-color: #16a34a; }
        .btn-attend.active.absent { background: #dc2626; color: white; border-color: #dc2626; }
        .btn-attend.active.late { background: #ca8a04; color: white; border-color: #ca8a04; }
        .date-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .date-nav input[type="date"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-save {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            margin-top: 10px;
        }
        .legend {
            display: flex;
            gap: 12px;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 12px;
        }
        .legend-item { display: flex; align-items: center; gap: 4px; }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
        .legend-dot.due { background: #dc2626; }
    }
    @media (min-width: 769px) {
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        .stat-item .value { font-size: 24px; }
        .student-card {
            flex-direction: row;
            padding: 15px 20px;
        }
        .btn-attend { width: 60px; height: 50px; }
        .btn-attend.active.present { background: #16a34a; color: white; border-color: #16a34a; }
        .btn-attend.active.absent { background: #dc2626; color: white; border-color: #dc2626; }
        .btn-attend.active.late { background: #ca8a04; color: white; border-color: #ca8a04; }
    }
</style>

<div class="attendance-page">
    <div class="attendance-header">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">{{ $batch->batch_name }}</h5>
            <span class="badge badge-secondary">{{ $batch->subject?->name ?? 'N/A' }}</span>
        </div>

        <div class="date-nav">
            <a href="{{ route('admin.batch-attendances.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-arrow-left"></i>
            </a>
            <input type="date" id="attendance-date" value="{{ $date }}" max="{{ date('Y-m-d') }}"
                   onchange="changeDate(this.value)">
            <a href="{{ route('admin.batch-attendances.report', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fa fa-chart-bar"></i> Report
            </a>
        </div>

        <div class="stats-bar">
            <div class="stat-item marked">
                <div class="value">{{ $stats['marked'] }}/{{ $stats['total'] }}</div>
                <div>Marked</div>
            </div>
            <div class="stat-item present">
                <div class="value">{{ $stats['present'] }}</div>
                <div>Present</div>
            </div>
            <div class="stat-item absent">
                <div class="value">{{ $stats['absent'] }}</div>
                <div>Absent</div>
            </div>
            <div class="stat-item late">
                <div class="value">{{ $stats['late'] }}</div>
                <div>Late</div>
            </div>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot due"></div>
                <span>Due (Highlighted)</span>
            </div>
        </div>
    </div>

    <form id="attendance-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="student-list">
            @forelse($formattedStudents as $student)
            <div class="student-card {{ $student['has_due'] ? 'has-due' : '' }}" data-student-id="{{ $student['id'] }}">
                <div class="student-info">
                    <div class="student-name">{{ $student['name'] }}</div>
                    <div class="student-meta">
                        Roll: {{ $student['roll'] ?: 'N/A' }} | ID: {{ $student['id_no'] ?: 'N/A' }}
                    </div>
                    @if($student['has_due'])
                    <div class="due-badge">
                        <i class="fa fa-exclamation-triangle"></i> Due: {{ number_format($student['due_amount'], 2) }}
                    </div>
                    @endif
                </div>
                <div class="attendance-buttons">
                    <button type="button" class="btn-attend present {{ $student['status'] === 'present' ? 'active' : '' }}"
                            onclick="setStatus({{ $student['id'] }}, 'present')">
                        <i class="fa fa-check"></i>
                        P
                    </button>
                    <button type="button" class="btn-attend absent {{ $student['status'] === 'absent' ? 'active' : '' }}"
                            onclick="setStatus({{ $student['id'] }}, 'absent')">
                        <i class="fa fa-times"></i>
                        A
                    </button>
                    <button type="button" class="btn-attend late {{ $student['status'] === 'late' ? 'active' : '' }}"
                            onclick="setStatus({{ $student['id'] }}, 'late')">
                        <i class="fa fa-clock"></i>
                        L
                    </button>
                </div>
                <input type="hidden" name="attendance[{{ $student['id'] }}]" id="status-{{ $student['id'] }}"
                       value="{{ $student['status'] ?? '' }}">
            </div>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">No students enrolled in this batch.</p>
            </div>
            @endforelse
        </div>

        @if($formattedStudents->count() > 0)
        <button type="submit" class="btn btn-success btn-save">
            <i class="fa fa-save"></i> Save Attendance
        </button>
        @endif
    </form>
</div>
@endsection

@section('scripts')
@parent
<script>
function setStatus(studentId, status) {
    const card = document.querySelector(`[data-student-id="${studentId}"]`);
    const input = document.getElementById(`status-${studentId}`);
    
    card.querySelectorAll('.btn-attend').forEach(btn => {
        btn.classList.remove('active');
    });
    
    input.value = status;
    const activeBtn = card.querySelector(`.btn-attend.${status}`);
    if (activeBtn) activeBtn.classList.add('active');
    
    updateStats();
}

function updateStats() {
    let marked = 0, present = 0, absent = 0, late = 0;
    document.querySelectorAll('input[name^="attendance"]').forEach(input => {
        if (input.value) {
            marked++;
            if (input.value === 'present') present++;
            else if (input.value === 'absent') absent++;
            else if (input.value === 'late') late++;
        }
    });
    
    document.querySelector('.stat-item.marked .value').textContent = marked + '/' + {{ $stats['total'] }};
    document.querySelector('.stat-item.present .value').textContent = present;
    document.querySelector('.stat-item.absent .value').textContent = absent;
    document.querySelector('.stat-item.late .value').textContent = late;
}

function changeDate(date) {
    const batchId = {{ $batch->id }};
    window.location.href = `/admin/batch-attendances/${batchId}/take?date=${date}`;
}

document.getElementById('attendance-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const batchId = {{ $batch->id }};
    const formData = new FormData(this);
    
    fetch(`/admin/batch-attendances/${batchId}/take`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Attendance saved successfully!');
            updateStats();
        } else {
            alert('Error saving attendance');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving attendance');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});
</script>
@endsection
