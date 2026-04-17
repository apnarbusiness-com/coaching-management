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
                <form method="POST" action="{{ route('admin.batches.assignTeachers.copyPreviousAll') }}" id="copy-last-month-teachers-form" class="inline-flex">
                    @csrf
                    <input type="hidden" name="month" id="copy-teachers-month" value="{{ $selectedMonth }}">
                    <input type="hidden" name="year" id="copy-teachers-year" value="{{ $selectedYear }}">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-teal-500 border border-transparent rounded-lg shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        onclick="return confirm('Copy last month\'s assigned teachers into this month for all batches? This will create salary records for this month.')">
                        Assign Last Month Teachers (All Batches)
                    </button>
                </form>
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

            $('#copy-last-month-teachers-form').on('submit', function() {
                $('#copy-teachers-month').val($('#filter-month').val());
                $('#copy-teachers-year').val($('#filter-year').val());
            });
        });
    </script>
@endsection
