@extends('layouts.admin')
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
                {{-- <a class="btn btn-info" href="{{ route('admin.student-basic-infos.demoCsv') }}">
                    Demo Excel/CSV
                </a> --}}
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
                                        <label for="duplicate_mode" class="col-md-4 control-label">Duplicate Action</label>
                                        <div class="col-md-7">
                                            <select name="duplicate_mode" id="duplicate_mode" class="form-control" required>
                                                <option value="skip">Skip existing</option>
                                                <option value="update">Update existing</option>
                                                <option value="duplicate">Create duplicate</option>
                                            </select>
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
                                        <label for="step2_duplicate_mode" class="col-md-4 control-label">Duplicate Action</label>
                                        <div class="col-md-7">
                                            <select name="duplicate_mode" id="step2_duplicate_mode" class="form-control" required>
                                                <option value="skip">Skip existing</option>
                                                <option value="update">Update existing</option>
                                                <option value="duplicate">Create duplicate</option>
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
                        name: 'status'
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

        });
    </script>
@endsection
