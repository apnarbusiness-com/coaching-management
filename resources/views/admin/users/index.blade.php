@extends('layouts.admin')
@section('title', 'Users — List')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.users.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filter-role" class="form-control">
                    <option value="">-- All Roles --</option>
                    @foreach($roles as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-status" class="form-control">
                    <option value="">-- All Status --</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-type" class="form-control">
                    <option value="">-- All Types --</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="none">None</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="filter-trashed">
                    <label class="form-check-label" for="filter-trashed">Include Deleted</label>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.user.fields.id') }}</th>
                        <th>{{ trans('cruds.user.fields.name') }}</th>
                        <th>{{ trans('cruds.user.fields.email') }}</th>
                        <th>{{ trans('cruds.user.fields.email_verified_at') }}</th>
                        <th>{{ trans('cruds.user.fields.roles') }}</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        @can('user_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.users.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });
                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')
                        return
                    }
                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }
                        }).done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
        @endcan

        let table = $('.datatable-User').DataTable({
            buttons: dtButtons,
            processing: true,
            serverSide: true,
            retrieve: true,
            ajax: {
                url: "{{ route('admin.users.index') }}",
                data: function (d) {
                    d.role_id = $('#filter-role').val();
                    d.status = $('#filter-status').val();
                    d.type = $('#filter-type').val();
                    d.with_trashed = $('#filter-trashed').is(':checked') ? 1 : 0;
                }
            },
            columns: [
                { data: 'placeholder', name: 'placeholder', searchable: false, sortable: false },
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'email_verified_at', name: 'email_verified_at' },
                { data: 'roles', name: 'roles.title', sortable: false },
                { data: 'status', name: 'deleted_at', sortable: true },
                { data: 'type_label', name: 'type_label', searchable: false, sortable: false },
                { data: 'actions', name: 'actions', searchable: false, sortable: false }
            ],
            order: [[ 1, 'desc' ]],
            pageLength: 100,
        });

        $('#filter-role, #filter-status, #filter-type, #filter-trashed').on('change', function () {
            table.ajax.reload();
        });
    })
</script>
@endsection