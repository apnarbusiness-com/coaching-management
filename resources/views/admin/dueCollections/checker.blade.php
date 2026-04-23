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

        .search-box input {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            width: 100%;
        }

        .search-box input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .search-result-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .search-result-item:hover {
            background: #f8fafc;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item .main-text {
            font-weight: 600;
            color: #1e293b;
        }

        .search-result-item .sub-text {
            font-size: 12px;
            color: #64748b;
        }

        .search-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            position: relative;
        }

        .year-filter {
            background: white;
            border-radius: 12px;
            padding: 12px 16px;
            display: inline-flex;
            align-items: center;
            margin-left: 15px;
            margin-bottom: 15px;
            gap: 8px;
        }

        .year-filter label {
            margin: 0;
            font-size: 14px;
            color: #64748b;
            white-space: nowrap;
        }

        .year-filter select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 14px;
            background: white;
            cursor: pointer;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

        .summary-card.due .value {
            color: #dc2626;
        }

        .summary-card.paid .value {
            color: #16a34a;
        }

        .summary-card.discount .value {
            color: #f59e0b;
        }

        .summary-card.remaining {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #f87171;
        }

        .summary-card.remaining .value {
            color: #dc2626;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            overflow: hidden;
        }

        @keyframes heartbit {
            0% {
                box-shadow: 0 0 0 0 var(--flag-color);
            }
            70% {
                box-shadow: 0 0 0 15px transparent;
            }
            100% {
                box-shadow: 0 0 0 0 transparent;
            }
        }

        .info-card.flagged {
            border: 2px solid var(--flag-color);
            animation: heartbit 2s infinite;
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

        .badge-success {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-warning {
            background: #fef9c3;
            color: #ca8a04;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-info {
            background: #dbeafe;
            color: #2563eb;
        }

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

        .attendance-stats .stat.present .num {
            color: #16a34a;
        }

        .attendance-stats .stat.absent .num {
            color: #dc2626;
        }

        .attendance-stats .stat.late .num {
            color: #f59e0b;
        }

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

        .percentage-bar .fill.high {
            background: #16a34a;
        }

        .percentage-bar .fill.medium {
            background: #f59e0b;
        }

        .percentage-bar .fill.low {
            background: #dc2626;
        }

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
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
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

        .paid-row-animation {
            animation: pulseGreen 0.5s ease-in-out;
        }

        @keyframes pulseGreen {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: #dcfce7;
                transform: scale(1.02);
            }

            100% {
                background-color: #dcfce7;
            }
        }

        .paid-check-icon {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <div class="checker-container">
        {{-- <div id="flagOverlay"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;">
        </div> --}}
        <div class="search-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h3><i class="fa fa-search-dollar mr-2"></i>Due Checker</h3>
                <div class="year-filter">
                    <label for="yearFilter">Year:</label>
                    <select id="yearFilter" class="form-control" onchange="loadStudentData()">
                        <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                        <option value="all">All Years</option>
                        @foreach ($years as $year)
                            @if ($year != $currentYear)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="studentSearch" class="form-control" placeholder="Search by Name, ID No, Admission ID, Father's Name or Mother's Name..." autofocus>
                <div id="searchResults" class="search-results"></div>
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
                    <button type="button" class="btn btn-xs btn-primary ml-auto" onclick="openFlagModal()">
                        <i class="fa fa-flag"></i> Manage Flags
                    </button>
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
                            <div id="flagBadges" class="mt-2"></div>
                            <div id="flagCommentsDisplay" class="mt-2 text-muted"
                                style="font-style: italic; font-size: 13px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-cards">
                <div class="summary-card due">
                    <div class="label">Total Amount</div>
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
                    <button type="button" class="btn btn-success btn-sm mt-2" id="payAllDueBtn" style="display: none;"
                        onclick="openPayAllModal()">
                        <i class="fa fa-credit-card"></i> Pay Now
                    </button>
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
                                            <th>Perm. Disc.</th>
                                            <th>One-Time</th>
                                            <th>Rem.</th>
                                            <th>Status</th>
                                            <th>Action</th>
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

        <!-- Payment Modal -->
        <div class="modal fade" id="payDueModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pay Due</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form id="pay-due-form">
                        <div class="modal-body">
                            <input type="hidden" id="pay-due-id">
                            <div class="form-group">
                                <label>Due Amount</label>
                                <input type="text" class="form-control" id="pay-due-amount" readonly>
                            </div>
                            <div class="form-group">
                                <label>Current One-Time Discount</label>
                                <input type="text" class="form-control" id="pay-due-one-time" readonly>
                            </div>
                            <div class="form-group">
                                <label>Add/Update One-Time Discount (Optional)</label>
                                <input type="number" class="form-control" id="pay-one-time-discount" step="0.01"
                                    min="0" value="0">
                                <small class="form-text text-muted">This discount applies only to this month</small>
                            </div>
                            <div class="form-group">
                                <label>Remaining After Discount</label>
                                <input type="text" class="form-control" id="pay-due-remaining" readonly>
                            </div>
                            <div class="form-group">
                                <label>Pay Amount</label>
                                <input type="number" class="form-control" id="pay-amount" step="0.01"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pay All Dues Modal -->
        <div class="modal fade" id="payAllDueModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pay All Dues</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form id="pay-all-due-form">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Total Due:</strong> <span id="payAllTotalDue">0.00</span>
                            </div>

                            <div class="due-list mb-3" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Batch</th>
                                            <th>Due</th>
                                            <th>Paid</th>
                                            <th>Remaining</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payAllDueList"></tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <label>Enter Payment Amount</label>
                                <input type="number" class="form-control" id="payAllAmount" step="0.01"
                                    min="0" required>
                                <small class="form-text text-muted">System will pay dues in order (oldest first) and apply
                                    partial payment to the last one if needed.</small>
                            </div>
                            {{-- <div class="form-group">
                                <label>One-Time Discount for a Specific Due (Optional)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="payAllOneTimeDiscount" step="0.01"
                                        min="0" placeholder="Amount" value="0">
                                    <select class="form-control" id="payAllDiscountBatch" style="max-width: 150px;">
                                        <option value="">Select Batch</option>
                                    </select>
                                    <input type="number" class="form-control" id="payAllDiscountMonth" placeholder="Month" min="1" max="12" style="max-width: 80px;">
                                    <input type="number" class="form-control" id="payAllDiscountYear" placeholder="Year" min="2000" max="2100" style="max-width: 100px;">
                                </div>
                                <small class="form-text text-muted">Select batch and month to apply one-time discount</small>
                            </div> --}}
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flag Assignment Modal -->
    <div class="modal fade" id="flagModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Student Flags</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Flag</label>
                        <select class="form-control" id="selectedFlag">
                            <option value="">Select a flag...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment</label>
                        <textarea class="form-control" id="flagComment" rows="3" placeholder="Add a comment (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" onclick="assignFlag()">Assign Flag</button>
                    </div>
                    <hr>
                    <h6>Current Flags</h6>
                    <div id="currentFlagsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        let searchTimeout = null;
        let selectedStudentId = null;

        $(function() {
            $('#studentSearch').on('input', function() {
                const query = $(this).val();
                
                if (searchTimeout) clearTimeout(searchTimeout);
                
                if (query.length < 1) {
                    $('#searchResults').hide();
                    selectedStudentId = null;
                    $('#emptyState').show();
                    $('#studentData').hide();
                    return;
                }
                
                searchTimeout = setTimeout(function() {
                    $.get("{{ route('admin.due-collections.checker.search') }}", { term: query }, function(data) {
                        const resultsContainer = $('#searchResults');
                        resultsContainer.empty();
                        
                        if (data.length === 0) {
                            resultsContainer.html('<div class="p-3 text-muted">No results found</div>');
                            resultsContainer.show();
                            return;
                        }
                        
                        data.forEach(function(item) {
                            resultsContainer.append(`
                                <div class="search-result-item" data-id="${item.id}">
                                    <div class="main-text">${item.first_name} ${item.last_name || ''}</div>
                                    <div class="sub-text">ID: ${item.id_no || item.admission_id || 'N/A'} | Father: ${item.fathers_name || 'N/A'} | Mother: ${item.mothers_name || 'N/A'}</div>
                                </div>
                            `);
                        });
                        
                        resultsContainer.show();
                    });
                }, 300);
            });

            $(document).on('click', '.search-result-item', function() {
                const studentId = $(this).data('id');
                selectedStudentId = studentId;
                $('#studentSearch').val($(this).find('.main-text').text());
                $('#searchResults').hide();
                loadStudentData();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-box').length) {
                    $('#searchResults').hide();
                }
            });

            $('#studentSearch').on('change', function() {
                const query = $(this).val();
                if (!query || query.length < 1) {
                    selectedStudentId = null;
                    $('#emptyState').show();
                    $('#studentData').hide();
                    return;
                }
                if (!selectedStudentId) {
                    $.get("{{ route('admin.due-collections.checker.search') }}", { term: query }, function(data) {
                        if (data.length > 0) {
                            selectedStudentId = data[0].id;
                            loadStudentData();
                        } else {
                            alert('No student found with this search term');
                        }
                    });
                }
            });

            setTimeout(function() {
                $('#studentSearch').focus();
            }, 100);
        });

        function loadStudentData() {
            
            const studentId = selectedStudentId;
            const year = $('#yearFilter').val();

            if (!studentId) {
                $('#emptyState').show();
                $('#studentData').hide();
                return;
            }

            $('#loader').show();
            $('#studentData').hide();
            $('#emptyState').hide();

            $.get("{{ route('admin.due-collections.checker.student', ':id') }}".replace(':id', studentId), {
                year: year
            }, function(response) {
                $('#loader').hide();
                $('#studentData').show();
                $('#emptyState').hide();

                const flags = response.flags || [];
                if (flags.length > 0) {
                    const bgColor = flags[0].color;
                    $('#flagOverlay').css('background-color', bgColor + '4D').show();
                    $('.info-card').first().css('--flag-color', bgColor).addClass('flagged');
                } else {
                    $('#flagOverlay').hide();
                    $('.info-card').first().removeClass('flagged');
                }

                const s = response.student;
                $('#studentName').text(s.name);
                $('#studentImage').attr("src", s.image || "{{ asset('img/avatar.png') }}");
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

                showPayAllButton(parseFloat(summary.total_remaining));

                const dueHistoryBody = $('#dueHistoryTable tbody');
                dueHistoryBody.empty();
                if (response.due_history.length === 0) {
                    dueHistoryBody.html(
                        '<tr><td colspan="9" class="text-center text-muted">No due records found</td></tr>');
                } else {
                    // console.log("response.due_history: ");
                    // console.log(response.due_history);
                    
                    response.due_history.forEach(function(due) {
                        let badgeClass = due.status === 'paid' ? 'badge-success' : (due.status ===
                            'partial' ? 'badge-warning' : 'badge-danger');
                        let permDisc = due.pivot_permanent_discount || 0;
                        let oneTimeDisc = due.pivot_one_time_discount || 0;
                        let payButton = due.due_remaining > 0 ?
                            `<button type="button" class="btn btn-xs btn-primary pay-btn" data-id="${due.id}" data-due-amount="${due.due_amount}" data-remaining="${due.due_remaining}" data-one-time="${oneTimeDisc}">Pay Now</button>` :
                            '-';
                        dueHistoryBody.append(`
                    <tr>
                        <td>${due.month_name} ${due.year}</td>
                        <td>${due.batch_name}</td>
                        <td>${parseFloat(due.due_amount).toFixed(2)}</td>
                        <td>${parseFloat(due.paid_amount).toFixed(2)}</td>
                        <td><span class="text-amber-600">${parseFloat(permDisc).toFixed(2)}</span></td>
                        <td><span class="text-purple-600">${parseFloat(oneTimeDisc).toFixed(2)}</span></td>
                        <td>${parseFloat(due.due_remaining).toFixed(2)}</td>
                        <td><span class="badge ${badgeClass} text-capitalize">
                            ${due.status}
                        </span></td>
                        <td>${payButton}</td>
                    </tr>
                `);
                    });
                    storeCurrentDues(response.due_history);
                }

                const paymentHistoryBody = $('#paymentHistoryTable tbody');
                paymentHistoryBody.empty();
                if (response.payment_history.length === 0) {
                    paymentHistoryBody.html(
                        '<tr><td colspan="5" class="text-center text-muted">No payment records found</td></tr>');
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
                        let barClass = att.percentage >= 80 ? 'high' : (att.percentage >= 50 ? 'medium' :
                            'low');
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

                currentStudentFlags = response.flags || [];
                renderCurrentFlags(currentStudentFlags);

            }).fail(function() {
                $('#loader').hide();
                alert('Failed to load student data');
            });
        }

        $(document).on('click', '.pay-btn', function() {
            let dueId = $(this).data('id');
            let dueAmount = $(this).data('due-amount');
            let remaining = $(this).data('remaining');
            let oneTimeDisc = $(this).data('one-time') || 0;

            $('#pay-due-id').val(dueId);
            $('#pay-due-amount').val(dueAmount);
            $('#pay-due-one-time').val(oneTimeDisc);
            $('#pay-one-time-discount').val(oneTimeDisc);

            let newDiscount = parseFloat(oneTimeDisc);
            let remainingAfterDiscount = Math.max(0, remaining - newDiscount);
            $('#pay-due-remaining').val(remainingAfterDiscount.toFixed(2));
            $('#pay-amount').attr('max', remainingAfterDiscount);
            $('#pay-amount').val(remainingAfterDiscount.toFixed(2));
            $('#payDueModal').modal('show');
        });

        $('#pay-one-time-discount').on('input', function() {
            let oneTimeDisc = parseFloat($(this).val()) || 0;
            let originalDue = parseFloat($('#pay-due-amount').val()) || 0;
            let remainingAfterDiscount = Math.max(0, originalDue - oneTimeDisc);
            $('#pay-due-remaining').val(remainingAfterDiscount.toFixed(2));
            $('#pay-amount').attr('max', remainingAfterDiscount);
            $('#pay-amount').val(remainingAfterDiscount.toFixed(2));
        });

        $('#pay-due-form').on('submit', function(e) {
            e.preventDefault();
            let dueId = $('#pay-due-id').val();
            let amount = $('#pay-amount').val();
            let oneTimeDiscount = $('#pay-one-time-discount').val() || 0;

            $.post("{{ route('admin.due-collections.pay') }}", {
                _token: '{{ csrf_token() }}',
                due_id: dueId,
                amount: amount,
                one_time_discount: oneTimeDiscount
            }, function(response) {
                $('#payDueModal').modal('hide');
                loadStudentData();
                alert(response.message || 'Payment recorded successfully!');
            }).fail(function(xhr) {
                let msg = 'Payment failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    msg = 'You do not have permission to make payments.';
                }
                alert(msg);
            });
        });

        let currentDues = [];

        function openPayAllModal() {
            const totalRemaining = parseFloat($('#totalRemaining').text().replace(/,/g, '')) || 0;

            $('#payAllTotalDue').text(totalRemaining.toFixed(2));
            $('#payAllAmount').attr('max', totalRemaining);
            $('#payAllAmount').val(totalRemaining);

            let batchSelect = $('#payAllDiscountBatch');
            batchSelect.empty().append('<option value="">Select Batch</option>');

            currentDues.forEach(function(due) {
                let selected = '';
                batchSelect.append(`<option value="${due.batch_id}" ${selected}>${due.batch_name}</option>`);
            });

            const dueListBody = $('#payAllDueList');
            dueListBody.empty();
            currentDues.forEach(function(due) {
                let badgeClass = due.status === 'paid' ? 'badge-success' : (due.status === 'partial' ?
                    'badge-warning' : 'badge-danger');
                dueListBody.append(`
            <tr>
                <td>${due.month_name} ${due.year}</td>
                <td>${due.batch_name}</td>
                <td>${parseFloat(due.due_amount).toFixed(2)}</td>
                <td>${parseFloat(due.paid_amount).toFixed(2)}</td>
                <td>${parseFloat(due.due_remaining).toFixed(2)}</td>
                <td><span class="badge ${badgeClass}">${due.status}</span></td>
            </tr>
        `);
            });

            $('#payAllDueModal').modal('show');
        }

        $('#pay-all-due-form').on('submit', function(e) {
            e.preventDefault();
            let studentId = selectedStudentId;
            let amount = $('#payAllAmount').val();
            let oneTimeDiscount = $('#payAllOneTimeDiscount').val() || 0;
            let discountBatchId = $('#payAllDiscountBatch').val();
            let discountMonth = $('#payAllDiscountMonth').val();
            let discountYear = $('#payAllDiscountYear').val();

            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Processing...');

            let postData = {
                _token: '{{ csrf_token() }}',
                student_id: studentId,
                amount: amount
            };

            if (oneTimeDiscount > 0 && discountBatchId && discountMonth && discountYear) {
                postData.one_time_discount = oneTimeDiscount;
                postData.one_time_discount_batch_id = discountBatchId;
                postData.one_time_discount_month = discountMonth;
                postData.one_time_discount_year = discountYear;
            }

            $.post("{{ route('admin.due-collections.payAll') }}", postData, function(response) {
                let paidDues = response.paid_dues;
                let currentIndex = 0;

                function processNextDue() {
                    if (currentIndex >= paidDues.length) {
                        setTimeout(function() {
                            $('#payAllDueModal').modal('hide');
                            submitBtn.prop('disabled', false).text(originalText);
                            loadStudentData();
                        }, 500);
                        return;
                    }

                    let due = paidDues[currentIndex];
                    let row = $('#payAllDueList').find('tr').eq(currentIndex);

                    row.addClass('bg-success');
                    row.find('td:last').html(
                        '<span class="badge badge-success"><i class="fa fa-check"></i> Paid</span>');

                    if (due.status === 'partial') {
                        row.find('td').eq(4).text(parseFloat(due.remaining).toFixed(2));
                        row.find('td').eq(5).html('<span class="badge badge-warning">Partial</span>');
                    }

                    currentIndex++;
                    setTimeout(processNextDue, 600);
                }

                processNextDue();

            }).fail(function(xhr) {
                let msg = 'Payment failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
                submitBtn.prop('disabled', false).text(originalText);
            });
        });

        function showPayAllButton(totalRemaining) {
            if (totalRemaining > 0) {
                $('#payAllDueBtn').show();
            } else {
                $('#payAllDueBtn').hide();
            }
        }

        function storeCurrentDues(dues) {
            currentDues = dues.filter(function(due) {
                return due.due_remaining > 0;
            });
        }

        let availableFlags = [];
        let currentStudentFlags = [];

        function openFlagModal() {
            const studentId = selectedStudentId;
            if (!studentId) {
                alert('Please select a student first');
                return;
            }

            $.get("{{ route('admin.student-flags.getFlags') }}", function(flags) {
                availableFlags = flags;
                const select = $('#selectedFlag');
                select.empty().append('<option value="">Select a flag...</option>');
                flags.forEach(function(flag) {
                    select.append('<option value="' + flag.id + '">' + flag.name + '</option>');
                });
            });

            renderCurrentFlags(currentStudentFlags);
            $('#flagModal').modal('show');
        }

        function renderCurrentFlags(flags) {
            const container = $('#currentFlagsList');
            container.empty();

            const badgeContainer = $('#flagBadges');
            const commentContainer = $('#flagCommentsDisplay');
            badgeContainer.empty();
            commentContainer.empty();

            if (flags.length === 0) {
                container.html('<p class="text-muted">No flags assigned</p>');
            } else {
                flags.forEach(function(flag) {
                    container.append(`
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: ${flag.color}20; border-radius: 6px; border-left: 4px solid ${flag.color};">
                    <div>
                        <strong>${flag.name}</strong>
                        ${flag.comment ? '<br><small class="text-muted">' + flag.comment + '</small>' : ''}
                    </div>
                    <button type="button" class="btn btn-xs btn-danger" onclick="removeFlag(${flag.id})">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `);

                    badgeContainer.append(`
                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; background: ${flag.color}; color: white;">${flag.name}</span>
            `);

                    if (flag.comment) {
                        commentContainer.append('<div style="background: ' + flag.color +
                            '20; border-left: 3px solid ' + flag.color +
                            '; padding: 8px 12px; border-radius: 4px; margin-top: 4px;"><strong>' + flag.name +
                            ':</strong> <em>"' + flag.comment + '"</em></div>');
                    }
                });
            }
        }

        function assignFlag() {
            const studentId = selectedStudentId;
            const flagId = $('#selectedFlag').val();
            const comment = $('#flagComment').val();

            if (!flagId) {
                alert('Please select a flag');
                return;
            }

            $.post("{{ route('admin.student-flags.assign') }}", {
                _token: '{{ csrf_token() }}',
                student_id: studentId,
                flag_id: flagId,
                comment: comment
            }, function(response) {
                $('#flagComment').val('');
                loadStudentData();
                alert(response.message);
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Failed to assign flag');
            });
        }

        function removeFlag(flagId) {
            const studentId = selectedStudentId;

            if (!confirm('Are you sure you want to remove this flag?')) {
                return;
            }

            $.post("{{ route('admin.student-flags.remove') }}", {
                _token: '{{ csrf_token() }}',
                student_id: studentId,
                flag_id: flagId
            }, function(response) {
                loadStudentData();
                alert(response.message);
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Failed to remove flag');
            });
        }
    </script>
@endsection
