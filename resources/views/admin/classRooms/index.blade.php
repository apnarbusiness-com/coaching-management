@extends('layouts.admin')
@section('content')
@can('class_room_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.class-rooms.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.classRoom.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.classRoom.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ClassRoom">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.classRoom.fields.id') }}</th>
                        <th>{{ trans('cruds.classRoom.fields.name') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classRooms as $key => $classRoom)
                        <tr data-entry-id="{{ $classRoom->id }}">
                            <td></td>
                            <td>{{ $classRoom->id ?? '' }}</td>
                            <td>{{ $classRoom->name ?? '' }}</td>
                            <td>
                                @can('class_room_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.class-rooms.show', $classRoom->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('class_room_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.class-rooms.edit', $classRoom->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('class_room_delete')
                                    <form action="{{ route('admin.class-rooms.destroy', $classRoom->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@endsection

@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('class_room_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.class-rooms.massDestroy') }}",
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
  let table = $('.datatable-ClassRoom:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
})
</script>
@endsection
