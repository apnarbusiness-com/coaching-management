@extends('layouts.admin')
@section('title', 'Due Collections — Student Summary')
@section('content')
<style>
    .due-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .due-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .due-stat-card .stat-label {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    .due-stat-card .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }
    .due-stat-card .stat-value.danger { color: #dc2626; }
    .due-stat-card .stat-value.success { color: #16a34a; }
    .due-stat-card .stat-value.warning { color: #ca8a04; }
    .due-stat-card .stat-value.info { color: #2563eb; }
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        align-items: end;
    }
    .filter-bar .form-group { margin-bottom: 0; min-width: 150px; }

    .detail-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: 500px;
        max-width: 90vw;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.15);
        z-index: 1050;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .detail-drawer.open {
        transform: translateX(0);
    }
    .detail-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    .detail-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }
    .drawer-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .drawer-body {
        padding: 1rem 1.5rem;
        overflow-y: auto;
        flex: 1;
    }
    .drawer-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #64748b;
        padding: 0;
        line-height: 1;
    }
    .drawer-close:hover { color: #1e293b; }
</style>

<div class="due-stats-grid">
    <div class="due-stat-card">
        <div class="stat-label">Total Due</div>
        <div class="stat-value danger">{{ number_format($stats['total_due'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Collected</div>
        <div class="stat-value success">{{ number_format($stats['total_collected'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Remaining</div>
        <div class="stat-value warning">{{ number_format($stats['total_remaining'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Paid Students</div>
        <div class="stat-value success">{{ $stats['paid_count'] }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Partial</div>
        <div class="stat-value info">{{ $stats['partial_count'] }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Unpaid</div>
        <div class="stat-value danger">{{ $stats['unpaid_count'] }}</div>
    </div>
</div>

<div class="due-stats-grid">
    <div class="due-stat-card">
        <div class="stat-label">Total Students</div>
        <div class="stat-value">{{ $filteredStats['total_students'] }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Collection Rate</div>
        <div class="stat-value success">{{ $filteredStats['collection_rate'] }}%</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Total Discount</div>
        <div class="stat-value warning">{{ number_format($filteredStats['total_discount'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Total Records</div>
        <div class="stat-value info">{{ $filteredStats['total_records'] }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3>Student Due Summary - {{ $month === 'all' ? 'Full Year' : \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }} {{ $year }}</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('admin.due-collections.index', request()->query()) }}" class="btn btn-info">
                    <i class="fa fa-list"></i> Monthly View
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="filter-bar">
            <div class="form-group">
                <label>Month</label>
                <select class="form-control filter-select" id="filter-month">
                    <option value="all" {{ $month === 'all' ? 'selected' : '' }}>All</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Year</label>
                <select class="form-control filter-select" id="filter-year">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Batch</label>
                <select class="form-control filter-select" id="filter-batch">
                    <option value="">All Batches</option>
                    @foreach($batches as $id => $name)
                        <option value="{{ $id }}" {{ $id == $batchId ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control filter-select" id="filter-status">
                    <option value="">All</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Filter</button>
            </div>
        </div>

        <table id="due-summary-table" class="table table-bordered table-striped table-hover ajaxTable datatable">
            <thead>
                <tr>
                    <th width="10"><input type="checkbox" class="mt-1"></th>
                    <th>Student Name</th>
                    <th>ID No</th>
                    <th>Total Due</th>
                    <th>Total Paid</th>
                    <th>Total Remaining</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Detail Drawer -->
<div class="detail-drawer-overlay" id="detailDrawerOverlay" onclick="closeDetailDrawer()"></div>
<div class="detail-drawer" id="detailDrawer">
    <div class="drawer-header">
        <h5 class="mb-0" id="drawerStudentName">Student Details</h5>
        <button type="button" class="drawer-close" onclick="closeDetailDrawer()">&times;</button>
    </div>
    <div class="drawer-body" id="drawerBody">
        <div id="drawerLoading" class="text-center py-4">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Loading...</p>
        </div>
        <div id="drawerContent" style="display:none;"></div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
$(function() {
    window.applyFilters = function() {
        let month = $('#filter-month').val();
        let year = $('#filter-year').val();
        let batch = $('#filter-batch').val();
        let status = $('#filter-status').val();

        window.location.href = `{{ route('admin.due-collections.summary') }}?month=${month}&year=${year}&batch_id=${batch}&status=${status}`;
    };

    let table = $('#due-summary-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.due-collections.summary') }}",
            data: function(d) {
                d.month = $('#filter-month').val();
                d.year = $('#filter-year').val();
                d.batch_id = $('#filter-batch').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'student_name', name: 'student_name' },
            { data: 'student_id_no', name: 'student_id_no' },
            { data: 'total_due', name: 'total_due' },
            { data: 'total_paid', name: 'total_paid' },
            { data: 'total_remaining', name: 'total_remaining' },
            { data: 'actions', name: 'actions' }
        ],
        order: [[1, 'asc']],
        pageLength: 25
    });

    $(document).on('click', '.view-details-btn', function() {
        let studentId = $(this).data('student-id');
        openDetailDrawer(studentId);
    });

    window.openDetailDrawer = function(studentId) {
        $('#drawerLoading').show();
        $('#drawerContent').hide();
        $('#detailDrawer').addClass('open');
        $('#detailDrawerOverlay').addClass('open');

        $.get("{{ route('admin.due-collections.student-summary', ':id') }}".replace(':id', studentId), {
            month: '{{ $month }}',
            year: '{{ $year }}'
        }, function(response) {
            $('#drawerStudentName').text(response.student_name + ' (' + response.student_id_no + ')');
            let html = '';

            if (response.dues.length === 0) {
                html = '<p class="text-muted">No due records found for this period.</p>';
            } else {
                html += '<table class="table table-bordered table-sm"><thead><tr><th>Batch</th><th>Month</th><th>Due</th><th>Paid</th><th>Remaining</th><th>Status</th></tr></thead><tbody>';
                response.dues.forEach(function(due) {
                    let statusClass = due.status === 'paid' || due.status === 'free' ? 'success' : (due.status === 'partial' ? 'warning' : 'danger');
                    let statusLabel = due.status.charAt(0).toUpperCase() + due.status.slice(1);
                    html += '<tr>' +
                        '<td>' + due.batch_name + '</td>' +
                        '<td>' + due.month_name + ' ' + due.year + '</td>' +
                        '<td>' + due.due_amount.toFixed(2) + '</td>' +
                        '<td>' + due.paid_amount.toFixed(2) + '</td>' +
                        '<td>' + due.due_remaining.toFixed(2) + '</td>' +
                        '<td><span class="badge bg-' + statusClass + '">' + statusLabel + '</span></td>' +
                        '</tr>';
                });
                html += '</tbody></table>';
            }

            $('#drawerContent').html(html);
            $('#drawerLoading').hide();
            $('#drawerContent').show();
        }).fail(function() {
            $('#drawerContent').html('<p class="text-danger">Failed to load student details.</p>');
            $('#drawerLoading').hide();
            $('#drawerContent').show();
        });
    };

    window.closeDetailDrawer = function() {
        $('#detailDrawer').removeClass('open');
        $('#detailDrawerOverlay').removeClass('open');
    };
});
</script>
@endsection
