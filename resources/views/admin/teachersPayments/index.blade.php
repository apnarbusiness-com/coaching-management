@extends('layouts.admin')
@section('content')
@php
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    $currentMonth = request('month', now()->month);
    $currentYear = request('year', now()->year);
    $years = range(now()->year - 1, now()->year + 1);
@endphp

<!-- Generate Monthly Salaries Form -->
<div class="mb-6 p-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">payments</span>
        Generate Monthly Salaries
    </h3>
    <form method="POST" action="{{ route('admin.teachers-payments.generate') }}" class="flex flex-wrap items-end gap-4">
        @csrf
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-500 dark:text-slate-400">Month</label>
            <select name="month" class="px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-500 dark:text-slate-400">Year</label>
            <select name="year" class="px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ $currentYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">auto_awesome</span>
            Generate Salaries
        </button>
    </form>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-3">
        This will create salary payment records for all active teachers based on their batch assignments for the selected month.
    </p>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm border border-green-200">
        {{ session('status') }}
    </div>
@endif

@can('teachers_payment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.teachers-payments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.teachersPayment.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                {{ trans('cruds.teachersPayment.title_singular') }} {{ trans('global.list') }}
            </div>
            <!-- Filter by Month/Year -->
            <form method="GET" action="{{ route('admin.teachers-payments.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="teacher_id" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $id => $name)
                        <option value="{{ $id }}" {{ request('teacher_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="batch_id" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                    <option value="">All Batches</option>
                    @foreach($batches as $id => $name)
                        <option value="{{ $id }}" {{ request('batch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="month" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                    <option value="">All Months</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                    <option value="">All Years</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $currentYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 dark:hover:bg-slate-600">
                    Filter
                </button>
                @if(request('month') || request('year') || request('teacher_id') || request('batch_id'))
                    <a href="{{ route('admin.teachers-payments.index') }}" class="px-3 py-1.5 text-red-600 hover:underline text-sm">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-TeachersPayment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.teachersPayment.fields.id') }}
                        </th>
                        <th>
                            Teacher
                        </th>
                        <th>
                            Batch
                        </th>
                        <th>
                            Salary Type
                        </th>
                        <th>
                            Month/Year
                        </th>
                        <th>
                            Amount
                        </th>
                        <th>
                            Paid
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachersPayments as $key => $teachersPayment)
                        @php
                            $totalAmount = $teachersPayment->calculated_amount;
                            $paidAmount = $teachersPayment->paid_amount;
                            $remaining = $totalAmount - $paidAmount;
                            $status = $teachersPayment->payment_status;
                            $paymentDetails = is_string($teachersPayment->payment_details) ? json_decode($teachersPayment->payment_details, true) : $teachersPayment->payment_details;
                            $salaryType = $paymentDetails['salary_type'] ?? 'fixed';
                            $salaryAmount = $paymentDetails['salary_amount'] ?? 0;
                        @endphp
                        <tr data-entry-id="{{ $teachersPayment->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $teachersPayment->id ?? '' }}
                            </td>
                            <td>
                                {{ $teachersPayment->teacher->name ?? '' }}
                            </td>
                            <td>
                                {{ $teachersPayment->batch->batch_name ?? '-' }}
                            </td>
                            <td>
                                {{ ucfirst($salaryType) }} ({{ $salaryAmount }})
                            </td>
                            <td>
                                {{ $teachersPayment->month_year_name ?? '' }}
                            </td>
                            <td>
                                {{ number_format($totalAmount, 2) }}
                            </td>
                            <td>
                                {{ number_format($paidAmount, 2) }}
                            </td>
                            <td>
                                @if($status === 'paid')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Paid</span>
                                @elseif($status === 'partial')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Partial</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Due</span>
                                @endif
                            </td>
                            <td>
                                @can('teachers_payment_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.teachers-payments.show', $teachersPayment->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @if($remaining > 0)
                                    <button type="button" 
                                        onclick="openPayModal({{ $teachersPayment->id }}, '{{ $teachersPayment->teacher->name ?? '' }}', {{ $totalAmount }}, {{ $paidAmount }}, {{ $remaining }})"
                                        class="btn btn-xs btn-success">
                                        Pay
                                    </button>
                                @endif

                                @can('teachers_payment_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.teachers-payments.edit', $teachersPayment->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('teachers_payment_delete')
                                    <form action="{{ route('admin.teachers-payments.destroy', $teachersPayment->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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


@include('admin.partials._pay_teacher_modal')

@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('teachers_payment_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.teachers-payments.massDestroy') }}",
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
  let table = $('.datatable-TeachersPayment:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection