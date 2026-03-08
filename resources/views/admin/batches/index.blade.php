@extends('layouts.admin')
@section('content')
    @can('batch_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.batches.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.batch.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.batch.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Batch">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.batch_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.subject') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.class') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.fee_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.fee_amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.duration_in_months') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.class_days') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.class_time') }}
                        </th>
                        <th>
                            {{ trans('cruds.batch.fields.students') }}
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
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.batches.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'batch_name',
                        name: 'batch_name'
                    },
                    {
                        data: 'subject_names',
                        name: 'subject_names',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'class_class_name',
                        name: 'class.class_name'
                    },
                    {
                        data: 'fee_type',
                        name: 'fee_type'
                    },
                    {
                        data: 'fee_amount',
                        name: 'fee_amount'
                    },
                    {
                        data: 'duration_in_months',
                        name: 'duration_in_months'
                    },
                    {
                        data: 'class_days_display',
                        name: 'class_days'
                    },
                    {
                        data: 'class_time',
                        name: 'class_time'
                    },
                    {
                        data: 'students_count',
                        name: 'students_count',
                        searchable: false,
                        sortable: false
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
            let table = $('.datatable-Batch').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@endsection
