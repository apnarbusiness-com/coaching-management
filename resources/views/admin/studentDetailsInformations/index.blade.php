@extends('layouts.admin')
@section('title', 'Student Details — List')
@section('content')
@can('student_details_information_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.student-details-informations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.studentDetailsInformation.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'StudentDetailsInformation', 'route' => 'admin.student-details-informations.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.studentDetailsInformation.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StudentDetailsInformation">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.fathers_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.mothers_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.fathers_nid') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.mothers_nid') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.guardian_relation') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.student_birth_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentDetailsInformation.fields.student') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentBasicInfo.fields.first_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.studentBasicInfo.fields.last_name') }}
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('student_details_information_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.student-details-informations.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.student-details-informations.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'fathers_name', name: 'fathers_name' },
{ data: 'mothers_name', name: 'mothers_name' },
{ data: 'fathers_nid', name: 'fathers_nid' },
{ data: 'mothers_nid', name: 'mothers_nid' },
{ data: 'guardian_name', name: 'guardian_name' },
{ data: 'guardian_relation', name: 'guardian_relation' },
{ data: 'guardian_contact_number', name: 'guardian_contact_number' },
{ data: 'student_birth_no', name: 'student_birth_no' },
{ data: 'student_id_no', name: 'student.id_no' },
{ data: 'student.first_name', name: 'student.first_name' },
{ data: 'student.last_name', name: 'student.last_name' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-StudentDetailsInformation').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection