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
                <button type="button" class="px-5 py-2.5 text-sm font-semibold text-white bg-teal-500 rounded-lg hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed" id="teacherModalSubmit" disabled>
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
    </style>
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('batch_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.batches.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).data(), function(entry) {
                            return entry.id
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

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
                    html += '<input type="checkbox" id="check-students-enrolled" checked>';
                    html += '<label for="check-students-enrolled">This month students enrolled in each batch</label>';
                    html += '</div>';
                    html += '<div class="checkbox-wrapper">';
                    html += '<input type="checkbox" id="check-last-month-teachers" checked>';
                    html += '<label for="check-last-month-teachers">Last month teachers assigned</label>';
                    html += '</div>';
                    html += '<div class="checkbox-wrapper">';
                    html += '<input type="checkbox" id="check-teacher-changes" checked>';
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
                    modalSubmit.disabled = false;
                }

                $('#check-students-enrolled, #check-last-month-teachers, #check-teacher-changes').on('change', function() {
                    const studentsEnrolled = $('#check-students-enrolled').is(':checked');
                    const lastMonthTeachers = $('#check-last-month-teachers').is(':checked');
                    const teacherChanges = $('#check-teacher-changes').is(':checked');
                    modalSubmit.disabled = !(studentsEnrolled && lastMonthTeachers && teacherChanges);
                });

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
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ month: month, year: year })
                        })
                        .then(response => response.json())
                        .then(data => {
                            modalOverlay.classList.remove('show');
                            if (data.status) {
                                alert(data.status);
                            } else if (data.message) {
                                alert(data.message);
                            }
                            location.reload();
                        })
                        .catch(error => {
                            alert('Error processing request');
                        });
                    });
                }

                window.toggleBatchAccordion = function(element) {
                    const item = element.parentElement;
                    item.classList.toggle('active');
                };
            }
        });
    </script>
@endsection
