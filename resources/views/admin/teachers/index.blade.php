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
@can('teacher_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.teachers.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.teacher.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.teacher.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Teacher">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.emloyee_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.phone') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.joining_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.teacher.fields.subject') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $key => $teacher)
                        <tr data-entry-id="{{ $teacher->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $teacher->id ?? '' }}
                            </td>
                            <td>
                                {{ $teacher->emloyee_code ?? '' }}
                            </td>
                            <td>
                                {{ $teacher->name ?? '' }}
                            </td>
                            <td>
                                {{ $teacher->phone ?? '' }}
                            </td>
                            <td>
                                {{ $teacher->email ?? '' }}
                            </td>
                            <td>
                                {{ $teacher->joining_date ?? '' }}
                            </td>
                            <td>
                                <span style="display:none">{{ $teacher->status ? 1 : 0 }}</span>
                                <div class="status-toggle">
                                    <label class="status-switch">
                                        <input
                                            type="checkbox"
                                            class="teacher-status-toggle"
                                            data-url="{{ route('admin.teachers.toggleStatus', $teacher->id) }}"
                                            {{ $teacher->status ? 'checked' : '' }}
                                        >
                                        <span class="status-slider"></span>
                                    </label>
                                    <span class="status-label {{ $teacher->status ? 'is-active' : '' }}">
                                        {{ $teacher->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @foreach($teacher->subjects as $key => $item)
                                    <span class="badge badge-info">{{ $item->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('teacher_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.teachers.show', $teacher->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('teacher_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.teachers.edit', $teacher->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('teacher_delete')
                                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('teacher_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.teachers.massDestroy') }}",
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
  let table = $('.datatable-Teacher:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

  $('.teacher-status-toggle').on('change', function () {
      const checkbox = $(this);
      const url = checkbox.data('url');
      const nextStatus = checkbox.is(':checked') ? 1 : 0;
      const label = checkbox.closest('.status-toggle').find('.status-label');

      checkbox.prop('disabled', true);

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          method: 'POST',
          url: url,
          data: { status: nextStatus }
      })
      .done(function (res) {
          const isActive = !!res.status;
          checkbox.prop('checked', isActive);
          label.text(isActive ? 'Active' : 'Inactive');
          label.toggleClass('is-active', isActive);
      })
      .fail(function () {
          checkbox.prop('checked', !nextStatus);
          alert('Status update failed. Please try again.');
      })
      .always(function () {
          checkbox.prop('disabled', false);
      });
  });
  
})

</script>
@endsection
