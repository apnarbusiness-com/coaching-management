@extends('layouts.admin')
@section('content')
<style>
    .attendance-shell {
        padding: 16px;
        background: #f8fafc;
        min-height: 100%;
    }
    .attendance-header {
        /* position: sticky; */
        top: 0;
        z-index: 100;
        background: #f8fafc;
        padding-bottom: 12px;
    }
    .attendance-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .header-title h4 {
        margin: 0;
        font-weight: 800;
        color: #0f172a;
    }
    .header-title .meta {
        color: #64748b;
        font-size: 12px;
    }
    .date-controls {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
    }
    .date-controls input[type="date"] {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        background: #fff;
    }
    .toolbar {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        margin-bottom: 12px;
    }
    .toolbar input[type="search"] {
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        background: #ffffff;
    }
    .toolbar .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 12px;
    }
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }
    .stat-item {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        font-size: 11px;
        color: #475569;
    }
    .stat-item .value {
        font-size: 18px;
        font-weight: 800;
    }
    .stat-item.present .value { color: #16a34a; }
    .stat-item.absent .value { color: #dc2626; }
    .stat-item.late .value { color: #ca8a04; }
    .stat-item.marked .value { color: #2563eb; }
    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 11px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
    .legend-dot.due { background: #dc2626; }
    .student-list {
        margin-top: 14px;
        display: grid;
        gap: 10px;
    }
    .student-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 14px 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .student-card.has-due {
        border-left: 4px solid #dc2626;
        background: #fef2f2;
    }
    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #e2e8f0;
        color: #0f172a;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 14px;
    }
    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .student-info {
        min-width: 0;
    }
    .student-name {
        font-weight: 700;
        font-size: 14px;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .student-meta {
        font-size: 11px;
        color: #64748b;
    }
    .due-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        background: #dc2626;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
    }
    .attendance-buttons {
        display: flex;
        gap: 6px;
    }
    .btn-attend {
        width: 52px;
        height: 46px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-attend:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
    }
    .btn-attend.present { background: #dcfce7; color: #16a34a; }
    .btn-attend.absent { background: #fee2e2; color: #dc2626; }
    .btn-attend.late { background: #fef9c3; color: #ca8a04; }
    .btn-attend.active.present { background: #16a34a; color: #fff; border-color: #16a34a; }
    .btn-attend.active.absent { background: #dc2626; color: #fff; border-color: #dc2626; }
    .btn-attend.active.late { background: #ca8a04; color: #fff; border-color: #ca8a04; }
    .btn-save {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        font-weight: 800;
        border-radius: 12px;
        margin-top: 14px;
    }
    .due-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .due-modal-backdrop.show {
        display: flex;
    }
    .due-modal {
        background: #ffffff;
        border-radius: 16px;
        width: min(720px, 92vw);
        max-height: 85vh;
        overflow: auto;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
    }
    .due-modal-header {
        padding: 16px 18px 10px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .due-modal-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }
    .due-modal-body {
        padding: 16px 18px 20px;
    }
    .due-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }
    .due-summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        font-size: 11px;
        color: #64748b;
    }
    .due-summary-card .value {
        display: block;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .due-summary-card.due .value { color: #dc2626; }
    .due-summary-card.paid .value { color: #16a34a; }
    .due-summary-card.discount .value { color: #0ea5e9; }
    .due-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .due-table th,
    .due-table td {
        padding: 8px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .due-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .due-status {
        font-weight: 700;
        text-transform: capitalize;
    }
    .due-status.unpaid { color: #dc2626; }
    .due-status.partial { color: #ca8a04; }
    .due-status.paid { color: #16a34a; }
    @media (max-width: 992px) {
        .attendance-shell { padding: 12px; }
        .date-controls { grid-template-columns: auto 1fr; }
        .date-controls a.btn-outline-primary { grid-column: 1 / -1; }
        .toolbar { grid-template-columns: 1fr; }
        .stats-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .student-card { grid-template-columns: auto 1fr; }
        .attendance-buttons { grid-column: 1 / -1; justify-content: flex-end; }
    }
    @media (max-width: 576px) {
        .btn-attend { width: 44px; height: 44px; font-size: 10px; }
        .attendance-buttons { justify-content: space-between; }
        .due-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .due-table th:nth-child(3),
        .due-table td:nth-child(3),
        .due-table th:nth-child(4),
        .due-table td:nth-child(4) {
            display: none;
        }
    }
</style>

<div class="attendance-shell">
    <div class="attendance-header">
        <div class="attendance-card">
            <div class="header-top">
                <div class="header-title">
                    <h4>{{ $batch->batch_name }}</h4>
                    <div class="meta">
                        {{ $batch->subject?->name ?? 'N/A' }} • {{ $attendanceMonthLabel ?? '' }}
                    </div>
                </div>
                <a href="{{ route('admin.batch-attendances.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="date-controls">
                <a href="{{ route('admin.batch-attendances.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-list"></i>
                </a>
                <input type="date" id="attendance-date" value="{{ $date }}" max="{{ date('Y-m-d') }}"
                       onchange="changeDate(this.value)">
                <a href="{{ route('admin.batch-attendances.report', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-chart-bar"></i> Report
                </a>
            </div>

            <div class="toolbar">
                <input type="search" id="student-search" placeholder="Search by name, roll or ID">
                <button type="button" class="btn btn-outline-success" onclick="markAll('present')">
                    <i class="fa fa-check"></i> All Present
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="clearAll()">
                    <i class="fa fa-eraser"></i> Clear
                </button>
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
    </div>

    <form id="attendance-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="student-list">
            @forelse($formattedStudents as $student)
            <div class="student-card {{ $student['has_due'] ? 'has-due' : '' }}"
                 data-student-id="{{ $student['id'] }}"
                 data-name="{{ strtolower($student['name']) }}"
                 data-roll="{{ strtolower($student['roll'] ?: '') }}"
                 data-idno="{{ strtolower($student['id_no'] ?: '') }}">
                <div class="student-avatar">
                    @if($student['image'])
                        <img src="{{ $student['image'] }}" alt="{{ $student['name'] }}">
                    @else
                        {{ strtoupper(substr($student['name'], 0, 1)) }}
                    @endif
                </div>
                <div class="student-info">
                    <div class="student-name">{{ $student['name'] }}</div>
                    <div class="student-meta">
                        Roll: {{ $student['roll'] ?: 'N/A' }} | ID: {{ $student['id_no'] ?: 'N/A' }}
                    </div>
                    @if($student['has_due'])
                    <button type="button" class="due-badge due-btn" data-student-id="{{ $student['id'] }}">
                        <i class="fa fa-exclamation-triangle"></i> Due: {{ number_format($student['due_amount'], 2) }}
                    </button>
                    @endif
                    @if($student['attendance_history']->count() > 0)
                    <div class="attendance-history mt-2 flex flex-wrap gap-1">
                        @foreach($student['attendance_history'] as $record)
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $record['status'] === 'present' ? 'bg-green-100 text-green-700' : ($record['status'] === 'late' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $record['date'] }}
                        </span>
                        @endforeach
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
                <p class="text-muted">No students enrolled in this batch for the selected month.</p>
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

<div class="due-modal-backdrop" id="due-modal-backdrop" aria-hidden="true">
    <div class="due-modal" role="dialog" aria-modal="true" aria-labelledby="due-modal-title">
        <div class="due-modal-header">
            <h5 class="due-modal-title" id="due-modal-title">Due Summary</h5>
            <button type="button" class="btn btn-sm btn-secondary" id="due-modal-close">Close</button>
        </div>
        <div class="due-modal-body">
            <div class="due-summary-grid">
                <div class="due-summary-card due">
                    <span class="value" id="due-total-remaining">0.00</span>
                    Remaining
                </div>
                <div class="due-summary-card paid">
                    <span class="value" id="due-total-paid">0.00</span>
                    Paid
                </div>
                <div class="due-summary-card discount">
                    <span class="value" id="due-total-discount">0.00</span>
                    Discount
                </div>
                <div class="due-summary-card">
                    <span class="value" id="due-total-billed">0.00</span>
                    Billed
                </div>
            </div>

            <div class="table-responsive">
                <table class="due-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Status</th>
                            <th>Due</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody id="due-modal-rows">
                        <tr>
                            <td colspan="5">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

function markAll(status) {
    document.querySelectorAll('input[name^="attendance"]').forEach(input => {
        input.value = status;
        const card = input.closest('.student-card');
        if (!card) return;
        card.querySelectorAll('.btn-attend').forEach(btn => btn.classList.remove('active'));
        const activeBtn = card.querySelector(`.btn-attend.${status}`);
        if (activeBtn) activeBtn.classList.add('active');
    });
    updateStats();
}

function clearAll() {
    document.querySelectorAll('input[name^="attendance"]').forEach(input => {
        input.value = '';
        const card = input.closest('.student-card');
        if (!card) return;
        card.querySelectorAll('.btn-attend').forEach(btn => btn.classList.remove('active'));
    });
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

    const searchInput = document.getElementById('student-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.student-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const roll = card.getAttribute('data-roll') || '';
                const idNo = card.getAttribute('data-idno') || '';
                const match = name.includes(term) || roll.includes(term) || idNo.includes(term);
                card.style.display = match ? '' : 'none';
            });
        });
    }

    const modalBackdrop = document.getElementById('due-modal-backdrop');
    const modalClose = document.getElementById('due-modal-close');
    const modalTitle = document.getElementById('due-modal-title');

    function closeDueModal() {
        modalBackdrop.classList.remove('show');
        modalBackdrop.setAttribute('aria-hidden', 'true');
    }

    if (modalClose) {
        modalClose.addEventListener('click', closeDueModal);
    }
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function (e) {
            if (e.target === modalBackdrop) closeDueModal();
        });
    }

    document.querySelectorAll('.due-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.getAttribute('data-student-id');
            const batchId = {{ $batch->id }};

            modalBackdrop.classList.add('show');
            modalBackdrop.setAttribute('aria-hidden', 'false');
            modalTitle.textContent = 'Due Summary';

            const rows = document.getElementById('due-modal-rows');
            rows.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';

            fetch(`/admin/batch-attendances/${batchId}/students/${studentId}/due-summary`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    rows.innerHTML = '<tr><td colspan="5">Unable to load due details.</td></tr>';
                    return;
                }

                modalTitle.textContent = `${data.student.name} • ${data.batch.name}`;

                document.getElementById('due-total-remaining').textContent = data.totals.total_remaining.toFixed(2);
                document.getElementById('due-total-paid').textContent = data.totals.total_paid.toFixed(2);
                document.getElementById('due-total-discount').textContent = data.totals.total_discount.toFixed(2);
                document.getElementById('due-total-billed').textContent = data.totals.total_due.toFixed(2);

                if (!data.items.length) {
                    rows.innerHTML = '<tr><td colspan="5">No dues found for this batch.</td></tr>';
                    return;
                }

                rows.innerHTML = data.items.map(item => {
                    return `
                        <tr>
                            <td>${item.month_name} ${item.year}</td>
                            <td class="due-status ${item.status}">${item.status}</td>
                            <td>${Number(item.due_amount).toFixed(2)}</td>
                            <td>${Number(item.paid_amount).toFixed(2)}</td>
                            <td>${Number(item.due_remaining).toFixed(2)}</td>
                        </tr>
                    `;
                }).join('');
            })
            .catch(() => {
                rows.innerHTML = '<tr><td colspan="5">Unable to load due details.</td></tr>';
            });
        });
    });
});
</script>
@endsection
