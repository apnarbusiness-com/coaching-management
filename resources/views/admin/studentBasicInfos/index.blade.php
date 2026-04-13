@extends('layouts.admin')
@section('styles')
<style>
    .status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .status-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 28px;
    }
    .status-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .status-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        border-radius: 999px;
        transition: all 0.2s ease;
        box-shadow: inset 0 0 0 1px #b6c2d1;
    }
    .status-slider::before {
        content: '';
        position: absolute;
        height: 22px;
        width: 22px;
        left: 3px;
        top: 3px;
        background: #ffffff;
        border-radius: 999px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }
    .status-slider::after {
        content: 'OFF';
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.4px;
    }
    .status-switch input:checked + .status-slider {
        background: #16a34a;
        box-shadow: inset 0 0 0 1px #15803d;
    }
    .status-switch input:checked + .status-slider::before {
        transform: translateX(32px);
    }
    .status-switch input:checked + .status-slider::after {
        content: 'ON';
        left: 8px;
        right: auto;
        color: #dcfce7;
    }
    .status-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
    }
    .status-label.is-active {
        color: #16a34a;
    }
</style>
@endsection

@section('content')
    @can('student_basic_info_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.student-basic-infos.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.studentBasicInfo.title_singular') }}
                </a>
                {{-- <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    Excel/CSV Import
                </button> --}}
                <button class="btn btn-dark" data-toggle="modal" data-target="#rawImportModal">
                    Step-1: Raw Excel/CSV Import
                </button>
                {{-- <button class="btn btn-secondary" data-toggle="modal" data-target="#rawProcessModal">
                    Step-2 Process Raw
                </button> --}}
                <a class="btn btn-outline-secondary" href="{{ route('admin.student-basic-infos.rawImports') }}">
                    Raw Rows View
                </a>
                <a class="btn btn-info" href="{{ route('admin.student-basic-infos.demoCsv') }}">
                    Demo Excel/CSV
                </a>
                <div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Student Excel/CSV Import</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">This import creates login users automatically.</p>
                                <form class="form-horizontal" method="POST"
                                    action="{{ route('admin.student-basic-infos.parseStudentImport') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                        <label for="csv_file"
                                            class="col-md-4 control-label">@lang('global.app_csv_file_to_import')</label>

                                        <div class="col-md-7">
                                            <input id="csv_file" type="file" class="form-control-file" name="csv_file"
                                                required>
                                            <small class="form-text text-muted">Supported: .csv, .txt, .xls, .xlsx</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Parse Import File
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="rawImportModal" tabindex="-1" role="dialog" aria-labelledby="rawImportLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="rawImportLabel">Step-1 Raw Excel/CSV Import</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">
                                    This uploads all non-empty rows directly to raw table (<code>student_import_raws</code>) without any student logic.
                                </p>
                                <form class="form-horizontal" method="POST"
                                    action="{{ route('admin.student-basic-infos.importRawToTable') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group{{ $errors->has('excel_file') ? ' has-error' : '' }}">
                                        <label for="excel_file" class="col-md-4 control-label">Excel/CSV File</label>
                                        <div class="col-md-7">
                                            <input id="excel_file" type="file" class="form-control-file" name="excel_file"
                                                required>
                                            <small class="form-text text-muted">Supported: .xlsx, .xls, .csv, .txt</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">Import To Raw Table</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="rawProcessModal" tabindex="-1" role="dialog" aria-labelledby="rawProcessLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="rawProcessLabel">Step-2 Process Raw To Student Tables</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">
                                    This will read rows from <code>student_import_raws</code> and process them one-by-one into student/user/details tables.
                                </p>
                                <form class="form-horizontal" method="POST"
                                    action="{{ route('admin.student-basic-infos.processRawToStudents') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="source_file" class="col-md-4 control-label">Source File</label>
                                        <div class="col-md-7">
                                            <select name="source_file" id="source_file" class="form-control" required>
                                                <option value="">Select source file</option>
                                                @foreach (($rawSourceFiles ?? collect()) as $sourceFile)
                                                    <option value="{{ $sourceFile }}">{{ $sourceFile }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">Run Step-2 Processing</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <div class="card">
        @if (session('import_errors'))
            <div class="alert alert-warning m-3">
                <strong>Some rows failed during import:</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            {{ trans('cruds.studentBasicInfo.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StudentBasicInfo">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.id_no') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.roll') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.first_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.joining_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.studentBasicInfo.fields.subject') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('student_basic_info_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.student-basic-infos.massDestroy') }}",
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
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.student-basic-infos.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id_no',
                        name: 'id'
                    },
                    {
                        data: 'roll',
                        name: 'roll'
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const urlTemplate = "{{ route('admin.student-basic-infos.toggleStatus', ':id') }}";
                                const toggleUrl = urlTemplate.replace(':id', row.id);
                                // Return the toggle HTML
                                var isActive = !!data; // Assuming data is 1/0 or true/false
                                return `
                                    <span style="display:none">${isActive ? 1 : 0}</span>
                                    <div class="status-toggle">
                                        <label class="status-switch">
                                            <input type="checkbox" class="student-status-toggle" data-url="${toggleUrl}" ${isActive ? 'checked' : ''}>
                                            <span class="status-slider"></span>
                                        </label>
                                        <span class="status-label ${isActive ? 'is-active' : ''}">${isActive ? 'Active' : 'Inactive'}</span>
                                    </div>
                                `;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'joining_date',
                        name: 'joining_date',
                        render: function(data, type, row) {
                            if (!data) {
                                return '';
                            }

                            if (type !== 'display' && type !== 'filter') {
                                return data;
                            }

                            let parsed = new Date(data.replace(' ', 'T'));
                            if (isNaN(parsed.getTime())) {
                                return data;
                            }

                            let day = String(parsed.getDate()).padStart(2, '0');
                            let month = parsed.toLocaleString('en-US', {
                                month: 'short'
                            });
                            let year = parsed.getFullYear();

                            return `${day} ${month}, ${year}`;
                        }
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'subject',
                        name: 'subjects.name'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
            };
            let table = $('.datatable-StudentBasicInfo').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            $(document).on('change', '.student-status-toggle', function() {
                const checkbox = $(this);
                const url = checkbox.data('url');
                const nextStatus = checkbox.is(':checked') ? 1 : 0;
                const label = checkbox.closest('.status-toggle').find('.status-label');

                checkbox.prop('disabled', true);

                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'POST',
                        url: url,
                        data: {
                            status: nextStatus
                        }
                    })
                    .done(function(res) {
                        const isActive = !!res.status;
                        checkbox.prop('checked', isActive);
                        label.text(isActive ? 'Active' : 'Inactive');
                        label.toggleClass('is-active', isActive);
                    })
                    .fail(function() {
                        checkbox.prop('checked', !nextStatus);
                        alert('Status update failed. Please try again.');
                    })
                    .always(function() {
                        checkbox.prop('disabled', false);
                    });
            });

        });
    </script>
@endsection
