@extends('layouts.admin')
@section('content')
@can('earning_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.earnings.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.earning.title_singular') }}
            </a>
            {{-- <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#earningImportModal">
                Import Excel
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.earnings.demoCsv') }}">
                Demo Excel/CSV
            </a> --}}
        </div>
    </div>
@endcan
<style>
    .summary-wrap {
        background: linear-gradient(135deg, #ecfeff 0%, #eef2ff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .summary-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
    }
    .summary-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 12px;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
    }
    .summary-card .month {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 6px;
    }
    .summary-card .amount {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
        align-items: end;
    }
    .filter-bar .form-group {
        margin-bottom: 0;
        min-width: 180px;
    }
</style>

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

<div class="summary-wrap">
    <div class="summary-title">This Year Monthly Earnings</div>
    <div class="summary-grid" id="earning-monthly-summary">
        <!-- JS injects cards -->
    </div>
</div>

<div class="filter-bar">
    <div class="form-group">
        <label for="earning-filter-category">Category</label>
        <select class="form-control" id="earning-filter-category">
            <option value="">All</option>
            @foreach ($earning_categories as $id => $entry)
                <option value="{{ $id }}">{{ $entry }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="earning-filter-month">Month</label>
        <select class="form-control" id="earning-filter-month">
            <option value="">All</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
    <div class="form-group">
        <label for="earning-filter-year">Year</label>
        <select class="form-control" id="earning-filter-year">
            <option value="">All</option>
            @php $currentYear = now()->year; @endphp
            @for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++)
                <option value="{{ $y }}" {{ $y === $currentYear ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="form-group">
        <button class="btn btn-outline-secondary" type="button" id="earning-filter-reset">Reset</button>
    </div>
</div>

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
  let table = $('.datatable-Earning:not(.ajaxTable)').DataTable({
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('admin.earnings.index') }}',
      data: function (d) {
        d.category_id = $('#earning-filter-category').val();
        d.month = $('#earning-filter-month').val();
        d.year = $('#earning-filter-year').val();
      }
    },
    columns: [
      { data: 'placeholder', name: 'placeholder', searchable: false, sortable: false },
      { data: 'id', name: 'id' },
      { data: 'earning_category_name', name: 'earning_category.name' },
      { data: 'student_id_no', name: 'student.id_no' },
      { data: 'subject_name', name: 'subject.name' },
      { data: 'title', name: 'title' },
      { data: 'exam_year', name: 'exam_year' },
      { data: 'amount', name: 'amount' },
      { data: 'earning_date', name: 'earning_date' },
      { data: 'paid_by', name: 'paid_by' },
      { data: 'recieved_by', name: 'recieved_by' },
      { data: 'actions', name: 'actions', searchable: false, sortable: false }
    ]
  })

  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const summaryEl = $('#earning-monthly-summary');
  const categoryFilter = $('#earning-filter-category');
  const monthFilter = $('#earning-filter-month');
  const yearFilter = $('#earning-filter-year');
  const resetBtn = $('#earning-filter-reset');

  function formatAmount(value) {
    return value.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' BDT';
  }

  function updateSummary() {
    $.get('{{ route('admin.earnings.summary') }}', {
      category_id: categoryFilter.val(),
      month: monthFilter.val(),
      year: yearFilter.val()
    }).done(function (data) {
      const totals = data.totals || [];
      const cards = totals.map((item, idx) => {
        const total = Number(item.total || 0);
        return `
          <div class="summary-card">
            <div class="month">${monthNames[idx]}</div>
            <div class="amount">${formatAmount(total)}</div>
          </div>
        `;
      }).join('');
      summaryEl.html(cards);
    });
  }

  function applyFilters() {
    table.ajax.reload();
    updateSummary();
  }

  categoryFilter.on('change', applyFilters);
  monthFilter.on('change', applyFilters);
  yearFilter.on('change', applyFilters);
  resetBtn.on('click', function() {
    categoryFilter.val('');
    monthFilter.val('');
    yearFilter.val('{{ $currentYear ?? now()->year }}');
    applyFilters();
  });

  updateSummary();

  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
