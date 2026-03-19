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
                ajax: "{{ route('admin.batches.index') }}",
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
                        data: 'subject_names',
                        name: 'subject_names',
                        title: 'Subject',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'class_class_name',
                        name: 'class.class_name',
                        title: 'Class'
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
                        data: 'class_days_display',
                        name: 'class_days',
                        title: 'Schedule',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'students_count',
                        name: 'students_count',
                        title: 'Students',
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
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@endsection
