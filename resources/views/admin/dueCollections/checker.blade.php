@extends('layouts.admin')
@section('content')
<style>
    .checker-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    .search-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 24px;
        color: white;
    }
    .search-section h3 {
        margin: 0 0 20px 0;
        font-weight: 600;
    }
    .search-box {
        background: white;
        border-radius: 12px;
        padding: 20px;
    }
    .search-box select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        width: 100%;
    }
    .search-box select:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .year-filter {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        display: inline-block;
        margin-left: 15px;
    }
    .year-filter select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 14px;
    }
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .summary-card .label {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .summary-card .value {
        font-size: 28px;
        font-weight: 700;
    }
    .summary-card.due .value { color: #dc2626; }
    .summary-card.paid .value { color: #16a34a; }
    .summary-card.discount .value { color: #f59e0b; }
    .summary-card.remaining { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-color: #f87171; }
    .summary-card.remaining .value { color: #dc2626; }
    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .info-card .card-header {
        background: #f8fafc;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 16px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .info-card .card-header i {
        color: #667eea;
    }
    .info-card .card-body {
        padding: 20px;
    }
    .student-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .student-info .avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid #e2e8f0;
    }
    .student-info .details h4 {
        margin: 0 0 5px 0;
        font-size: 20px;
        color: #1e293b;
    }
    .student-info .details p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    .student-info .details .badge {
        margin-right: 8px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: #1e293b;
    }
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .badge-danger { background: #fee2e2; color: #dc2626; }
    .badge-info { background: #dbeafe; color: #2563eb; }
    .batch-list {
        display: grid;
        gap: 12px;
    }
    .batch-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .batch-item .batch-info h5 {
        margin: 0 0 4px 0;
        font-size: 15px;
        color: #1e293b;
    }
    .batch-item .batch-info p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }
    .batch-item .batch-meta {
        text-align: right;
    }
    .batch-item .batch-meta .fee {
        font-weight: 600;
        color: #1e293b;
    }
    .batch-item .batch-meta .enrolled {
        font-size: 12px;
        color: #64748b;
    }
    .attendance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    .attendance-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }
    .attendance-card h5 {
        margin: 0 0 12px 0;
        font-size: 14px;
        color: #1e293b;
    }
    .attendance-stats {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }
    .attendance-stats .stat {
        flex: 1;
        text-align: center;
        padding: 8px;
        background: white;
        border-radius: 8px;
    }
    .attendance-stats .stat .num {
        font-size: 18px;
        font-weight: 700;
    }
    .attendance-stats .stat .label {
        font-size: 11px;
        color: #64748b;
    }
    .attendance-stats .stat.present .num { color: #16a34a; }
    .attendance-stats .stat.absent .num { color: #dc2626; }
    .attendance-stats .stat.late .num { color: #f59e0b; }
    .percentage-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }
    .percentage-bar .fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s;
    }
    .percentage-bar .fill.high { background: #16a34a; }
    .percentage-bar .fill.medium { background: #f59e0b; }
    .percentage-bar .fill.low { background: #dc2626; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    .empty-state h4 {
        margin: 0 0 8px 0;
        color: #1e293b;
    }
    .empty-state p {
        margin: 0;
        font-size: 14px;
    }
    .loader {
        display: none;
        text-align: center;
        padding: 40px;
    }
    .loader i {
        font-size: 32px;
        color: #667eea;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .select2-container--default .select2-selection--single {
        height: 50px;
        padding: 10px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        font-size: 15px;
    }
    .student-select-result {
        display: flex;
        flex-direction: column;
    }
    .student-select-result .main-text {
        font-weight: 600;
        color: #1e293b;
    }
    .student-select-result .sub-text {
        font-size: 12px;
        color: #64748b;
    }
</style>

<div class="checker-container">
    <div class="search-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h3><i class="fa fa-search-dollar mr-2"></i>Due Checker</h3>
            <div class="year-filter">
                <select id="yearFilter" class="form-control" onchange="loadStudentData()">
                    <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                    <option value="all">All Years</option>
                    @foreach($years as $year)
                        @if($year != $currentYear)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="search-box">
            <select id="studentSearch" class="form-control" style="width: 100%">
                <option value="">Search by Name, ID No, Admission ID, Father's Name or Mother's Name...</option>
            </select>
        </div>
    </div>

    <div id="loader" class="loader">
        <i class="fa fa-spinner"></i>
        <p>Loading student data...</p>
    </div>

    <div id="studentData" style="display: none;">
        <div class="info-card">
            <div class="card-header">
                <i class="fa fa-user"></i> Student Information
            </div>
            <div class="card-body">
                <div class="student-info">
                    <img id="studentImage" src="" alt="Student" class="avatar">
                    <div class="details">
                        <h4 id="studentName">-</h4>
                        <p>
                            <span class="badge badge-info" id="studentIdNo">-</span>
                            <span class="badge badge-info" id="studentAdmissionId">-</span>
                            <span class="badge badge-secondary" id="studentClass">-</span>
                        </p>
                        <p>
                            <strong>Father:</strong> <span id="studentFather">-</span> | 
                            <strong>Mother:</strong> <span id="studentMother">-</span> | 
                            <strong>Contact:</strong> <span id="studentContact">-</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card due">
                <div class="label">Total Due</div>
                <div class="value" id="totalDue">0.00</div>
            </div>
            <div class="summary-card paid">
                <div class="label">Total Paid</div>
                <div class="value" id="totalPaid">0.00</div>
            </div>
            <div class="summary-card discount">
                <div class="label">Total Discount</div>
                <div class="value" id="totalDiscount">0.00</div>
            </div>
            <div class="summary-card remaining">
                <div class="label">Total Remaining/Unpaid</div>
                <div class="value" id="totalRemaining">0.00</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="card-header">
                        <i class="fa fa-list-alt"></i> Due History by Month
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="data-table" id="dueHistoryTable">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Batch</th>
                                        <th>Due</th>
                                        <th>Paid</th>
                                        <th>Disc.</th>
                                        <th>Rem.</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="card-header">
                        <i class="fa fa-money-bill-wave"></i> Payment History
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="data-table" id="paymentHistoryTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Batch</th>
                                        <th>Amount</th>
                                        <th>Ref No.</th>
                                        <th>Received By</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="card-header">
                        <i class="fa fa-users"></i> Active Batches
                    </div>
                    <div class="card-body">
                        <div class="batch-list" id="activeBatchesList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="card-header">
                        <i class="fa fa-clipboard-check"></i> Attendance Analysis
                    </div>
                    <div class="card-body">
                        <div class="attendance-grid" id="attendanceAnalysis"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="emptyState" class="empty-state">
        <i class="fa fa-user-search"></i>
        <h4>Search for a Student</h4>
        <p>Enter name, ID, admission number, father's name or mother's name to view due history</p>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
$(function() {
    $('#studentSearch').select2({
        ajax: {
            url: "{{ route('admin.due-collections.checker.search') }}",
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.first_name + ' ' + (item.last_name || '') + ' - ' + (item.id_no || item.admission_id || 'N/A'),
                            html: '<div class="student-select-result">' +
                                '<div class="main-text">' + item.first_name + ' ' + (item.last_name || '') + '</div>' +
                                '<div class="sub-text">ID: ' + (item.id_no || item.admission_id || 'N/A') + 
                                ' | Father: ' + (item.fathers_name || 'N/A') + 
                                ' | Mother: ' + (item.mothers_name || 'N/A') + '</div>' +
                                '</div>',
                            first_name: item.first_name,
                            last_name: item.last_name,
                            id_no: item.id_no,
                            admission_id: item.admission_id
                        };
                    })
                };
            },
            templateResult: function(data) {
                if (!data.id) return data.text;
                return $(data.html);
            },
            templateSelection: function(data) {
                return data.text || data.first_name + ' ' + (data.last_name || '');
            }
        },
        minimumInputLength: 1,
        placeholder: 'Search by Name, ID No, Admission ID, Father\'s Name or Mother\'s Name...'
    });

    $('#studentSearch').on('change', function() {
        if ($(this).val()) {
            loadStudentData();
        }
    });
});

function loadStudentData() {
    const studentId = $('#studentSearch').val();
    const year = $('#yearFilter').val();

    if (!studentId) {
        $('#emptyState').show();
        $('#studentData').hide();
        return;
    }

    $('#loader').show();
    $('#studentData').hide();
    $('#emptyState').hide();

    $.get("{{ route('admin.due-collections.checker.student', ':id') }}".replace(':id', studentId), { year: year }, function(response) {
        $('#loader').hide();
        $('#studentData').show();
        $('#emptyState').hide();

        const s = response.student;
        $('#studentName').text(s.name);
        $('#studentImage').attr('src', s.image || '{{ asset("img/avatar.png") }}');
        $('#studentIdNo').text('ID: ' + (s.id_no || 'N/A'));
        $('#studentAdmissionId').text('Adm: ' + (s.admission_id || 'N/A'));
        $('#studentClass').text(s.class_name);
        $('#studentFather').text(s.fathers_name);
        $('#studentMother').text(s.mothers_name);
        $('#studentContact').text(s.contact_number);

        const summary = response.due_summary;
        $('#totalDue').text(parseFloat(summary.total_due).toFixed(2));
        $('#totalPaid').text(parseFloat(summary.total_paid).toFixed(2));
        $('#totalDiscount').text(parseFloat(summary.total_discount).toFixed(2));
        $('#totalRemaining').text(parseFloat(summary.total_remaining).toFixed(2));

        const dueHistoryBody = $('#dueHistoryTable tbody');
        dueHistoryBody.empty();
        if (response.due_history.length === 0) {
            dueHistoryBody.html('<tr><td colspan="7" class="text-center text-muted">No due records found</td></tr>');
        } else {
            response.due_history.forEach(function(due) {
                let badgeClass = due.status === 'paid' ? 'badge-success' : (due.status === 'partial' ? 'badge-warning' : 'badge-danger');
                dueHistoryBody.append(`
                    <tr>
                        <td>${due.month_name} ${due.year}</td>
                        <td>${due.batch_name}</td>
                        <td>${parseFloat(due.due_amount).toFixed(2)}</td>
                        <td>${parseFloat(due.paid_amount).toFixed(2)}</td>
                        <td>${parseFloat(due.discount_amount).toFixed(2)}</td>
                        <td>${parseFloat(due.due_remaining).toFixed(2)}</td>
                        <td><span class="badge ${badgeClass}">${due.status}</span></td>
                    </tr>
                `);
            });
        }

        const paymentHistoryBody = $('#paymentHistoryTable tbody');
        paymentHistoryBody.empty();
        if (response.payment_history.length === 0) {
            paymentHistoryBody.html('<tr><td colspan="5" class="text-center text-muted">No payment records found</td></tr>');
        } else {
            response.payment_history.forEach(function(payment) {
                paymentHistoryBody.append(`
                    <tr>
                        <td>${payment.date ? new Date(payment.date).toLocaleDateString() : 'N/A'}</td>
                        <td>${payment.batch_name}</td>
                        <td>${parseFloat(payment.amount).toFixed(2)}</td>
                        <td>${payment.reference || 'N/A'}</td>
                        <td>${payment.received_by || 'N/A'}</td>
                    </tr>
                `);
            });
        }

        const activeBatchesList = $('#activeBatchesList');
        activeBatchesList.empty();
        if (response.active_batches.length === 0) {
            activeBatchesList.html('<p class="text-muted">No active batches</p>');
        } else {
            response.active_batches.forEach(function(batch) {
                activeBatchesList.append(`
                    <div class="batch-item">
                        <div class="batch-info">
                            <h5>${batch.batch_name}</h5>
                            <p>${batch.subject_name} | ${batch.class_name}</p>
                        </div>
                        <div class="batch-meta">
                            <div class="fee">${batch.fee_type}: ${parseFloat(batch.fee_amount).toFixed(2)}</div>
                            <div class="enrolled">Enrolled: ${batch.enrolled_at ? new Date(batch.enrolled_at).toLocaleDateString() : 'N/A'}</div>
                        </div>
                    </div>
                `);
            });
        }

        const attendanceAnalysis = $('#attendanceAnalysis');
        attendanceAnalysis.empty();
        if (response.attendance_analysis.length === 0) {
            attendanceAnalysis.html('<p class="text-muted">No attendance records found</p>');
        } else {
            response.attendance_analysis.forEach(function(att) {
                let barClass = att.percentage >= 80 ? 'high' : (att.percentage >= 50 ? 'medium' : 'low');
                attendanceAnalysis.append(`
                    <div class="attendance-card">
                        <h5>${att.batch_name}</h5>
                        <div class="attendance-stats">
                            <div class="stat present">
                                <div class="num">${att.present}</div>
                                <div class="label">Present</div>
                            </div>
                            <div class="stat absent">
                                <div class="num">${att.absent}</div>
                                <div class="label">Absent</div>
                            </div>
                            <div class="stat late">
                                <div class="num">${att.late}</div>
                                <div class="label">Late</div>
                            </div>
                        </div>
                        <div class="percentage-bar">
                            <div class="fill ${barClass}" style="width: ${att.percentage}%"></div>
                        </div>
                        <div style="text-align: center; margin-top: 8px; font-weight: 600; color: #1e293b;">
                            ${att.percentage}% (${att.total_days} days)
                        </div>
                    </div>
                `);
            });
        }

    }).fail(function() {
        $('#loader').hide();
        alert('Failed to load student data');
    });
}
</script>
@endsection
