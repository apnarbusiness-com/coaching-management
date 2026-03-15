@extends('layouts.admin')
@section('content')
@can('earning_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.earnings.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.earning.title_singular') }}
            </a>
            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#earningImportModal">
                Import Excel
            </button>
        </div>
    </div>
@endcan

@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if (session('import_errors'))
    <div class="alert alert-warning">
        <strong>Some rows failed during import:</strong>
        <ul class="mb-0">
            @foreach (session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        {{ trans('cruds.earning.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Earning">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.earning_category') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.student') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.subject') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.exam_year') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.earning_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.paid_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.earning.fields.recieved_by') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($earnings as $key => $earning)
                        <tr data-entry-id="{{ $earning->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $earning->id ?? '' }}
                            </td>
                            <td>
                                {{ $earning->earning_category->name ?? '' }}
                            </td>
                            <td>
                                {{ $earning->student->id_no ?? '' }}
                            </td>
                            <td>
                                {{ $earning->subject->name ?? '' }}
                            </td>
                            <td>
                                {{ $earning->title ?? '' }}
                            </td>
                            <td>
                                {{ $earning->exam_year ?? '' }}
                            </td>
                            <td>
                                {{ $earning->amount ?? '' }}
                            </td>
                            <td>
                                {{ $earning->earning_date ?? '' }}
                            </td>
                            <td>
                                {{ $earning->paid_by ?? '' }}
                            </td>
                            <td>
                                {{ $earning->recieved_by ?? '' }}
                            </td>
                            <td>
                                @can('earning_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.earnings.show', $earning->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('earning_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.earnings.edit', $earning->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('earning_delete')
                                    <form action="{{ route('admin.earnings.destroy', $earning->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('earning_create')
    <div class="modal fade" id="earningImportModal" tabindex="-1" role="dialog" aria-labelledby="earningImportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('admin.earnings.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="earningImportModalLabel">Earnings Import</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="import_file">Excel File</label>
                            <input type="file" class="form-control" name="import_file" id="import_file" required>
                            <small class="form-text text-muted">
                                Columns: Date, Details, Category, Earning, Admission ID
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="default_year">Default Year (if date has no year)</label>
                            <input type="number" class="form-control" name="default_year" id="default_year"
                                value="{{ now()->year }}" min="1900" max="2100">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('earning_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.earnings.massDestroy') }}",
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
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Earning:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
