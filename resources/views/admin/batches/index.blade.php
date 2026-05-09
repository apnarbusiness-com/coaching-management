@extends('layouts.admin')
@section('content')
    @php
        $selectedMonth = $month ?? now()->month;
        $selectedYear = $year ?? now()->year;
        $monthName = \Carbon\Carbon::createFromDate(null, $selectedMonth, 1)->format('F');
    @endphp
    @can('batch_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.batches.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.batch.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    @if (session('status'))
        <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm mb-4">
            {{ session('status') }}
        </div>
    @endif
    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 md:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month</label>
                <select id="filter-month" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Year</label>
                <select id="filter-year" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3">
                    @for ($y = $selectedYear - 3; $y <= $selectedYear + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="apply-filters">
                    Filter
                </button>
                <button type="button" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 border border-transparent rounded-lg hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="reset-filters">
                    Reset
                </button>
                <form method="POST" action="{{ route('admin.batches.assignStudents.copyPreviousAll') }}" id="copy-last-month-form" class="inline-flex">
                    @csrf
                    <input type="hidden" name="month" id="copy-month" value="{{ $selectedMonth }}">
                    <input type="hidden" name="year" id="copy-year" value="{{ $selectedYear }}">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-amber-500 border border-transparent rounded-lg shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                        onclick="return confirm('Copy last month\'s enrolled students into this month for all batches? This will replace the current selection for this month across all batches.')">
                        Enroll Last Month Students (All Batches)
                    </button>
                </form>
                <button type="button"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-teal-500 border border-transparent rounded-lg shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        id="open-teacher-modal-btn">
                        Assign Last Month Teachers (All Batches)
                    </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" id="batch-summary-cards">
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 text-center">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Expected Earnings</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-2" id="summary-expected">
                {{ number_format($summary['total_expected'] ?? 0, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">For <span id="summary-period">{{ $monthName }} {{ $selectedYear }}</span></div>
        </div>
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 text-center">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Earnings</div>
            <div class="text-2xl font-bold text-emerald-600 mt-2" id="summary-earned">
                {{ number_format($summary['total_earned'] ?? 0, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Collected in selected month</div>
        </div>
        <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 text-center">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Dues / Remaining</div>
            <div class="text-2xl font-bold text-rose-600 mt-2" id="summary-remaining">
                {{ number_format($summary['total_remaining'] ?? 0, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Outstanding for selected month</div>
        </div>
    </div>

    <div class="teacher-modal-overlay" id="teacherModalOverlay">
        <div class="teacher-modal">
            <div class="teacher-modal-header">
                <h3 class="text-lg font-semibold" id="teacherModalTitle">Assign Last Month Teachers</h3>
                <p class="text-teal-100 text-sm mt-1" id="teacherModalSubtitle">Review and confirm teacher assignments</p>
            </div>
            <div class="teacher-modal-body" id="teacherModalBody">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-500 mx-auto mb-4"></div>
                    <p class="text-slate-500">Loading preview data...</p>
                </div>
            </div>
            <div class="teacher-modal-footer">
                <button type="button" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200" id="teacherModalClose">Cancel</button>
                <button type="button" class="px-5 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed" id="teacherModalSubmit" disabled>
                    Assign Teachers
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.batch.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Batch">
            </table>
        </div>
    </div>

    <!-- Batch Delete Dependency Modal -->
    <div id="batchDeleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeBatchDeleteModal()"></div>
            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 transform transition-all">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200 dark:border-slate-700">
                    <span class="material-symbols-outlined text-3xl text-red-500">delete_forever</span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Delete Batch</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400" id="modal-batch-name"></p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Resolve all dependencies before deletion:</p>
                    <div id="dependencies-list" class="space-y-2"></div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="closeBatchDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancel</button>
                    <button type="button" id="confirm-batch-delete-btn" disabled
                        class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg opacity-50 cursor-not-allowed transition-all flex items-center gap-2"
                        onclick="submitBatchDelete()">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        Delete Batch
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="batch-delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
@section('scripts')
    @parent
    <style>
        .teacher-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .teacher-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .teacher-modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            max-height: 85vh;
            overflow: hidden;
            transform: scale(0.95) translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .teacher-modal-overlay.show .teacher-modal {
            transform: scale(1) translateY(0);
        }
        .teacher-modal-header {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            padding: 20px 24px;
            color: white;
        }
        .teacher-modal-body {
            padding: 24px;
            max-height: 60vh;
            overflow-y: auto;
        }
        .teacher-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .batch-info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .batch-info-card h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .batch-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
        }
        .batch-stat {
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
        }
        .batch-stat-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
        }
        .batch-stat-value {
            font-weight: 600;
            color: #1e293b;
        }
        .teacher-list {
            margin-top: 12px;
        }
        .teacher-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: white;
            border-radius: 6px;
            margin-bottom: 6px;
            font-size: 14px;
        }
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: #f1f5f9;
            border-radius: 8px;
            margin-bottom: 16px;
            cursor: pointer;
        }
        .checkbox-wrapper input {
            width: 18px;
            height: 18px;
            accent-color: #14b8a6;
        }
        .checkbox-wrapper label {
            font-size: 14px;
            color: #334155;
            cursor: pointer;
        }
        .batch-accordion-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .batch-accordion-item {
            border-bottom: 1px solid #e2e8f0;
        }
        .batch-accordion-item:last-child {
            border-bottom: none;
        }
        .batch-accordion-header {
            padding: 14px 16px;
            background: #f8fafc;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        .batch-accordion-header:hover {
            background: #f1f5f9;
        }
        .batch-accordion-header h4 {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        .batch-accordion-arrow {
            transition: transform 0.2s;
            color: #64748b;
        }
        .batch-accordion-item.active .batch-accordion-arrow {
            transform: rotate(180deg);
        }
        .batch-accordion-content {
            display: none;
            padding: 16px;
            background: white;
        }
        .batch-accordion-item.active .batch-accordion-content {
            display: block;
        }
        .status-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .status-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }
        .status-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .status-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #e2e8f0;
            border-radius: 999px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        .status-slider::before {
            content: '';
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background: #ffffff;
            border-radius: 999px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(0,0,0,0.15), 0 1px 2px rgba(0,0,0,0.1);
        }
        .status-switch input:checked + .status-slider {
            background: linear-gradient(135deg, #16a34a, #15803d);
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
        }
        .status-switch input:checked + .status-slider::before {
            transform: translateX(20px);
        }
        .status-switch input:focus-visible + .status-slider {
            outline: 2px solid #137fec;
            outline-offset: 2px;
        }
        .status-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            transition: color 0.2s ease;
        }
        .status-label.is-active {
            color: #16a34a;
        }
    </style>
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.batches.index') }}",
                    data: function(d) {
                        d.month = $('#filter-month').val();
                        d.year = $('#filter-year').val();
                    },
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder',
                        title: '',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id',
                        title: 'ID'
                    },
                    {
                        data: 'batch_name',
                        name: 'batch_name',
                        title: 'Batch Name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        title: 'Status'
                    },
                    {
                        data: 'fee_type',
                        name: 'fee_type',
                        title: 'Fee Type'
                    },
                    {
                        data: 'fee_amount',
                        name: 'fee_amount',
                        title: 'Fee Amount'
                    },
                    {
                        data: 'duration_in_months',
                        name: 'duration_in_months',
                        title: 'Duration (Months)'
                    },
                    {
                        data: 'expected_income',
                        name: 'expected_income',
                        title: 'Total Expected Income',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_due_remaining',
                        name: 'total_due_remaining',
                        title: 'Total Due/Remaining',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'class_days_display',
                        name: 'class_days',
                        title: 'Schedule',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'students_count',
                        name: 'students_count',
                        title: 'Students (Filtered)',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: '',
                        orderable: false,
                        searchable: false
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
            };
            let table = $('.datatable-Batch').DataTable(dtOverrideGlobals);
            $('.datatable-Batch').on('xhr.dt', function(e, settings, json) {
                if (!json || !json.summary) {
                    return;
                }

                $('#summary-expected').text(formatNumber(json.summary.total_expected));
                $('#summary-earned').text(formatNumber(json.summary.total_earned));
                $('#summary-remaining').text(formatNumber(json.summary.total_remaining));
                $('#summary-period').text(getMonthName($('#filter-month').val()) + ' ' + $('#filter-year').val());
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            function getMonthName(monthNumber) {
                const names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const index = parseInt(monthNumber, 10) - 1;
                return names[index] || '';
            }

            function formatNumber(value) {
                const number = parseFloat(value || 0);
                return number.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            $('#apply-filters').on('click', function() {
                table.ajax.reload();
            });

            $('#reset-filters').on('click', function() {
                $('#filter-month').val('{{ now()->month }}');
                $('#filter-year').val('{{ now()->year }}');
                table.ajax.reload();
            });

            $('#copy-last-month-form').on('submit', function() {
                $('#copy-month').val($('#filter-month').val());
                $('#copy-year').val($('#filter-year').val());
            });

            // Teacher Modal Logic
            const modalOverlay = document.getElementById('teacherModalOverlay');
            const modalClose = document.getElementById('teacherModalClose');
            const modalSubmit = document.getElementById('teacherModalSubmit');
            const openModalBtn = document.getElementById('open-teacher-modal-btn');
            let modalData = null;

            if (openModalBtn && modalOverlay) {
                openModalBtn.addEventListener('click', function() {
                    const month = $('#filter-month').val();
                    const year = $('#filter-year').val();

                    document.getElementById('teacherModalBody').innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-500 mx-auto mb-4"></div><p class="text-slate-500">Loading preview data...</p></div>';
                    modalOverlay.classList.add('show');
                    modalSubmit.disabled = true;

                    fetch("{{ route('admin.batches.assignTeachers.previewCopyPreviousAll') }}?month=" + month + "&year=" + year)
                        .then(response => response.json())
                        .then(data => {
                            modalData = data;
                            if (!data.success) {
                                document.getElementById('teacherModalBody').innerHTML = '<div class="text-center py-8 text-red-500">' + data.message + '</div>';
                                return;
                            }
                            renderTeacherModalContent(data);
                        })
                        .catch(error => {
                            document.getElementById('teacherModalBody').innerHTML = '<div class="text-center py-8 text-red-500">Error loading data</div>';
                        });
                });

                function renderTeacherModalContent(data) {
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    document.getElementById('teacherModalTitle').textContent = 'Assign Last Month Teachers';
                    document.getElementById('teacherModalSubtitle').textContent = 'Copy teachers from ' + monthNames[data.prev_month - 1] + ' ' + data.prev_year + ' to ' + monthNames[data.current_month - 1] + ' ' + data.current_year;

                    let html = '';
                    html += '<div class="checkbox-wrapper">';
                    html += '<input type="checkbox" id="check-students-enrolled">';
                    html += '<label for="check-students-enrolled">This month students enrolled in each batch</label>';
                    html += '</div>';
                    html += '<div class="checkbox-wrapper">';
                    html += '<input type="checkbox" id="check-last-month-teachers">';
                    html += '<label for="check-last-month-teachers">Last month teachers assigned</label>';
                    html += '</div>';
                    html += '<div class="checkbox-wrapper">';
                    html += '<input type="checkbox" id="check-teacher-changes">';
                    html += '<label for="check-teacher-changes">Teacher changes (new/removed)</label>';
                    html += '</div>';

                    html += '<div class="batch-accordion-container">';
                    data.batches.forEach(function(batch, index) {
                        html += '<div class="batch-accordion-item" data-index="' + index + '">';
                        html += '<div class="batch-accordion-header" onclick="toggleBatchAccordion(this)">';
                        html += '<h4>' + batch.batch_name + '</h4>';
                        html += '<span class="batch-accordion-arrow">▼</span>';
                        html += '</div>';
                        html += '<div class="batch-accordion-content">';
                        html += '<div class="batch-stats">';
                        html += '<div class="batch-stat"><div class="batch-stat-label">Students This Month</div><div class="batch-stat-value">' + batch.current_students_count + '</div></div>';
                        html += '<div class="batch-stat"><div class="batch-stat-label">Last Month</div><div class="batch-stat-value">' + batch.prev_students_count + '</div></div>';
                        html += '<div class="batch-stat"><div class="batch-stat-label">Teachers</div><div class="batch-stat-value">' + (batch.teachers ? batch.teachers.length : 0) + '</div></div>';
                        html += '</div>';

                        if (batch.teachers && batch.teachers.length > 0) {
                            html += '<div class="teacher-list">';
                            html += '<div class="text-xs font-semibold text-slate-500 mb-2">Teachers from last month:</div>';
                            batch.teachers.forEach(function(teacher) {
                                html += '<div class="teacher-item"><span class="text-slate-500">👤</span> ' + teacher.name + (teacher.phone ? ' - ' + teacher.phone : '') + '</div>';
                            });
                            html += '</div>';
                        }

                        if (batch.newly_added_teachers && batch.newly_added_teachers.length > 0) {
                            html += '<div class="teacher-list mt-2">';
                            html += '<div class="text-xs font-semibold text-amber-600 mb-2">⚠️ Newly added teachers:</div>';
                            batch.newly_added_teachers.forEach(function(teacher) {
                                html += '<div class="teacher-item bg-amber-50"><span class="text-amber-500">➕</span> ' + teacher.name + '</div>';
                            });
                            html += '</div>';
                        }

                        // if (batch.removed_teachers && batch.removed_teachers.length > 0) {
                        //     html += '<div class="teacher-list mt-2">';
                        //     html += '<div class="text-xs font-semibold text-red-600 mb-2">⚠️ Removed teachers:</div>';
                        //     batch.removed_teachers.forEach(function(teacher) {
                        //         html += '<div class="teacher-item bg-red-50"><span class="text-red-500">➖</span> ' + teacher.name + '</div>';
                        //     });
                        //     html += '</div>';
                        // }

                        html += '</div></div>';
                    });

                    html += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">';
                    html += '<p class="text-sm text-blue-700"><strong>Note:</strong> After confirming, teacher salary records will be created for ' + monthNames[data.current_month - 1] + ' ' + data.current_year + '</p>';
                    html += '</div>';
                    html += '</div>';

                    document.getElementById('teacherModalBody').innerHTML = html;

                    function checkAllBoxes() {
                        const c1 = document.getElementById('check-students-enrolled').checked;
                        const c2 = document.getElementById('check-last-month-teachers').checked;
                        const c3 = document.getElementById('check-teacher-changes').checked;
                        const allChecked = c1 && c2 && c3;
                        modalSubmit.disabled = !allChecked;
                        if (allChecked) {
                            modalSubmit.classList.remove('bg-red-500', 'hover:bg-red-600');
                            modalSubmit.classList.add('bg-teal-500', 'hover:bg-teal-600');
                        } else {
                            modalSubmit.classList.remove('bg-teal-500', 'hover:bg-teal-600');
                            modalSubmit.classList.add('bg-red-500', 'hover:bg-red-600');
                        }
                    }

                    document.getElementById('check-students-enrolled').addEventListener('change', checkAllBoxes);
                    document.getElementById('check-last-month-teachers').addEventListener('change', checkAllBoxes);
                    document.getElementById('check-teacher-changes').addEventListener('change', checkAllBoxes);
                }

                if (modalClose) {
                    modalClose.addEventListener('click', function() {
                        modalOverlay.classList.remove('show');
                    });
                }

                if (modalOverlay) {
                    modalOverlay.addEventListener('click', function(e) {
                        if (e.target === modalOverlay) {
                            modalOverlay.classList.remove('show');
                        }
                    });
                }

                if (modalSubmit) {
                    modalSubmit.addEventListener('click', function() {
                        if (!modalData || !modalData.success) return;

                        const month = modalData.current_month;
                        const year = modalData.current_year;

                        fetch("{{ route('admin.batches.assignTeachers.copyPreviousAll') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ month: month, year: year })
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response ok:', response.ok);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            modalOverlay.classList.remove('show');
                            if (data.status) {
                                alert(data.status);
                            } else if (data.message) {
                                alert(data.message);
                            } else if (data.error) {
                                alert('Error: ' + data.error);
                            }
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error processing request: ' + error.message);
                        });
                    });
                }

                window.toggleBatchAccordion = function(element) {
                    const item = element.parentElement;
                    item.classList.toggle('active');
                };

                // Batch Status Toggle
                $(document).on('change', '.batch-status-toggle', function() {
                    const checkbox = $(this);
                    const label = checkbox.closest('.status-toggle').find('.status-label');
                    const nextStatus = checkbox.is(':checked') ? 1 : 0;
                    const url = checkbox.data('url');
                    const rowData = table.row(checkbox.closest('tr')).data();
                    const batchName = rowData ? rowData.batch_name : 'this batch';
                    const actionText = nextStatus ? 'activate' : 'deactivate';

                    checkbox.prop('checked', !nextStatus);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to ' + actionText + ' "' + batchName + '"?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: nextStatus ? '#16a34a' : '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, ' + actionText + '!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            checkbox.prop('disabled', true);

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: { status: nextStatus, _token: '{{ csrf_token() }}' },
                                success: function(res) {
                                    const isActive = !!res.status;
                                    checkbox.prop('checked', isActive);
                                    label.text(isActive ? 'Active' : 'Inactive');
                                    label.toggleClass('is-active', isActive);
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Batch has been ' + (isActive ? 'activated' : 'deactivated') + '.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Status update failed. Please try again.', 'error');
                                },
                                complete: function() {
                                    checkbox.prop('disabled', false);
                                }
                            });
                        }
                    });
                });
            }

            window.batchDeleteId = null;

            window.openBatchDeleteModal = function(batchId, batchName) {
                batchDeleteId = batchId;
                document.getElementById('modal-batch-name').textContent = '"' + (batchName || 'Batch #' + batchId) + '"';

                const list = document.getElementById('dependencies-list');
                list.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div></div>';

                document.getElementById('confirm-batch-delete-btn').disabled = true;
                document.getElementById('confirm-batch-delete-btn').classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('confirm-batch-delete-btn').classList.remove('opacity-100', 'cursor-pointer');

                document.getElementById('batchDeleteModal').classList.remove('hidden');

                fetch('{{ route('admin.batches.dependencies', 'PLACEHOLDER') }}'.replace('PLACEHOLDER', batchId))
                    .then(res => res.json())
                    .then(data => {
                        renderDependencies(data);
                    })
                    .catch(() => {
                        list.innerHTML = '<p class="text-sm text-red-500 text-center py-4">Failed to load dependencies.</p>';
                    });
            };

            window.closeBatchDeleteModal = function() {
                document.getElementById('batchDeleteModal').classList.add('hidden');
                batchDeleteId = null;
            };

            window.renderDependencies = function(data) {
                const items = [
                    { key: 'enrollments', label: 'Student Enrollments', icon: 'group', warning: false },
                    { key: 'dues', label: 'Student Monthly Dues', icon: 'receipt_long', warning: false },
                    { key: 'teachers', label: 'Teacher Assignments', icon: 'school', warning: false },
                    { key: 'subjects', label: 'Batch Subjects', icon: 'menu_book', warning: false },
                    { key: 'payments', label: 'Teacher Payments', icon: 'payments', warning: false },
                    { key: 'attendances', label: 'Batch Attendances', icon: 'calendar_month', warning: false },
                    { key: 'earnings', label: 'Student Earnings', icon: 'account_balance', warning: true },
                    { key: 'expenses', label: 'Expenses', icon: 'receipt', warning: true },
                ];

                const list = document.getElementById('dependencies-list');
                list.innerHTML = '';

                let allResolved = true;

                items.forEach(item => {
                    const count = data[item.key] || 0;
                    const resolved = count === 0;
                    if (!resolved) allResolved = false;

                    const row = document.createElement('div');
                    row.id = 'dep-row-' + item.key;
                    row.className = 'flex items-center justify-between p-3 rounded-lg border ' + (resolved ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50');

                    const checkbox = document.createElement('span');
                    checkbox.className = 'material-symbols-outlined ' + (resolved ? 'text-green-600' : 'text-slate-300 dark:text-slate-600');
                    checkbox.textContent = resolved ? 'check_circle' : 'radio_button_unchecked';

                    const labelWrap = document.createElement('div');
                    labelWrap.className = 'flex-1 ml-3';
                    labelWrap.innerHTML = '<div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-slate-500">' + item.icon + '</span><span class="text-sm font-medium text-slate-700 dark:text-slate-300">' + item.label + '</span>' + (item.warning ? '<span class="material-symbols-outlined text-[16px] text-amber-500" title="Financial record - permanent deletion">warning</span>' : '') + '</div><div class="text-xs ' + (resolved ? 'text-green-600' : 'text-slate-500') + ' mt-0.5 ml-6">' + (resolved ? 'Cleared' : count + ' record' + (count > 1 ? 's' : '') + ' found') + '</div>';

                    const actionBtn = document.createElement('button');
                    if (resolved) {
                        actionBtn.className = 'px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/40 rounded-lg cursor-default';
                        actionBtn.textContent = 'Done';
                        actionBtn.disabled = true;
                    } else {
                        actionBtn.className = 'px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors flex items-center gap-1';
                        actionBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">delete</span> Delete ' + count;
                        actionBtn.onclick = function() { deleteDependency(item.key, actionBtn, row); };
                    }

                    row.appendChild(checkbox);
                    row.appendChild(labelWrap);
                    row.appendChild(actionBtn);
                    list.appendChild(row);
                });

                if (allResolved) {
                    const btn = document.getElementById('confirm-batch-delete-btn');
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    btn.classList.add('opacity-100', 'cursor-pointer');
                }
            };

            window.deleteDependency = function(type, btn, row) {
                btn.disabled = true;
                btn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>';

                fetch('{{ route('admin.batches.dependencies.delete', ['batch' => 'BATCH_ID', 'type' => 'TYPE_ID']) }}'.replace('BATCH_ID', batchDeleteId).replace('TYPE_ID', type), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const checkbox = row.querySelector('.material-symbols-outlined');
                        checkbox.textContent = 'check_circle';
                        checkbox.className = 'material-symbols-outlined text-green-600';
                        row.className = 'flex items-center justify-between p-3 rounded-lg border border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20';
                        const labelDiv = row.querySelector('.text-xs');
                        labelDiv.textContent = 'Cleared';
                        labelDiv.className = 'text-xs text-green-600 mt-0.5 ml-6';
                        btn.className = 'px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/40 rounded-lg cursor-default';
                        btn.textContent = 'Done';
                        btn.disabled = true;

                        const remaining = document.querySelectorAll('#dependencies-list .flex.items-center.justify-between .bg-red-600');
                        if (remaining.length === 0) {
                            const deleteBtn = document.getElementById('confirm-batch-delete-btn');
                            deleteBtn.disabled = false;
                            deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            deleteBtn.classList.add('opacity-100', 'cursor-pointer');
                        }
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">delete</span> Retry';
                });
            };

            window.submitBatchDelete = function() {
                if (!batchDeleteId) return;
                const form = document.getElementById('batch-delete-form');
                form.action = '{{ route('admin.batches.destroy', 'REPLACE_ID') }}'.replace('REPLACE_ID', batchDeleteId);
                form.submit();
            };
        });
    </script>
@endsection
