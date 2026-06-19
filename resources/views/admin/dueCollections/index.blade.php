@extends('layouts.admin')
@section('title', 'Due Collections — List')
@section('content')
<style>
    .due-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .due-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .due-stat-card .stat-label {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    .due-stat-card .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }
    .due-stat-card .stat-value.danger { color: #dc2626; }
    .due-stat-card .stat-value.success { color: #16a34a; }
    .due-stat-card .stat-value.warning { color: #ca8a04; }
    .due-stat-card .stat-value.info { color: #2563eb; }
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        align-items: end;
    }
    .filter-bar .form-group { margin-bottom: 0; min-width: 150px; }
    .pay-modal-body { max-height: 400px; overflow-y: auto; }
    .payment-card-input:checked + .payment-card {
        border-color: #2563EB !important;
        background-color: rgba(239, 246, 255, 0.5) !important;
        color: #2563EB !important;
    }
    .due-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #f8fafc;
    }
    .due-item.paid { background: #dcfce7; border-color: #86efac; }
    .due-item.partial { background: #fef9c3; border-color: #fde047; }
</style>

<div class="due-stats-grid">
    <div class="due-stat-card">
        <div class="stat-label">Total Due</div>
        <div class="stat-value danger">{{ number_format($stats['total_due'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Collected</div>
        <div class="stat-value success">{{ number_format($stats['total_collected'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Remaining</div>
        <div class="stat-value warning">{{ number_format($stats['total_remaining'], 2) }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Paid Students</div>
        <div class="stat-value success">{{ $stats['paid_count'] }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Partial</div>
        <div class="stat-value info">{{ $stats['partial_count'] }}</div>
    </div>
    <div class="due-stat-card">
        <div class="stat-label">Unpaid</div>
        <div class="stat-value danger">{{ $stats['unpaid_count'] }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3>Monthly Due Collection - {{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }} {{ $year }}</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('admin.due-collections.summary', request()->query()) }}" class="btn btn-info">
                    <i class="fa fa-users"></i> Summary View
                </a>
                <form action="{{ route('admin.due-collections.generate') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Generate dues for this month?')">
                        Generate Dues
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="filter-bar">
            <div class="form-group">
                <label>Month</label>
                <select class="form-control filter-select" id="filter-month">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Year</label>
                <select class="form-control filter-select" id="filter-year">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Batch</label>
                <select class="form-control filter-select" id="filter-batch">
                    <option value="">All Batches</option>
                    @foreach($batches as $id => $name)
                        <option value="{{ $id }}" {{ $id == $batchId ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Class</label>
                <select class="form-control filter-select" id="filter-class">
                    <option value="">All Classes</option>
                    @foreach($classes as $id => $name)
                        <option value="{{ $id }}" {{ $id == $classId ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control filter-select" id="filter-status">
                    <option value="">All</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Filter</button>
            </div>
        </div>

        <table id="due-collections-table" class="table table-bordered table-striped table-hover ajaxTable datatable datatable-dueCollections">
            <thead>
                <tr>
                    <th width="10"><input type="checkbox" class="mt-1"></th>
                    <th>Student Name</th>
                    <th>ID No</th>
                    <th>Batch</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Month</th>
                    <th>Due Amount</th>
                    <th>Paid</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Quick Pay Modal -->
<div class="modal fade" id="quickPayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Payment</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Student</label>
                    <select class="form-control" id="quick-pay-student" style="width:100%"></select>
                </div>
                <div id="quick-pay-dues" class="pay-modal-body"></div>
            </div>
        </div>
    </div>
</div>

<!-- Individual Pay Modal -->
<div class="modal fade" id="payDueModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Due</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="pay-due-form">
                <div class="modal-body">
                    <input type="hidden" id="pay-due-id">
                    <div class="form-group">
                        <label>Due Amount</label>
                        <input type="text" class="form-control" id="pay-due-amount" readonly>
                    </div>
                    <div class="form-group">
                        <label>Remaining</label>
                        <input type="text" class="form-control" id="pay-due-remaining" readonly>
                    </div>
                    <div class="form-group">
                        <label>Pay Amount</label>
                        <input type="number" class="form-control" id="pay-amount" step="0.01" min="1" required>
                    </div>
                    @if ($cashBooks->isNotEmpty())
                    <div class="form-group">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        @if (setting('cashbook_display_type') == 'card')
                            @php $icons = ['wallet'=>'💰','money'=>'💵','bank'=>'🏦','mobile'=>'📱','card'=>'💳','gift'=>'🎁','gold'=>'🪙','dollar'=>'💲']; @endphp
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($cashBooks as $cb)
                                    <label class="relative cursor-pointer">
                                        <input class="payment-card-input sr-only" type="radio" name="pay_cash_book_id"
                                            value="{{ $cb->id }}"
                                            {{ $loop->first ? 'checked' : '' }}>
                                        <div class="payment-card flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 bg-white transition-all hover:bg-slate-50 text-slate-500 text-center" style="border:2px solid #e2e8f0;">
                                            @if ($cb->image)
                                                <img src="{{ Storage::url($cb->image) }}" class="w-6 h-6 mb-1 object-contain">
                                            @elseif ($cb->icon && isset($icons[$cb->icon]))
                                                <span class="text-xl mb-1">{{ $icons[$cb->icon] }}</span>
                                            @else
                                                <span class="material-symbols-outlined mb-1 text-lg">account_balance</span>
                                            @endif
                                            <span class="text-xs font-medium">{{ $cb->title }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <select name="pay_cash_book_id" id="pay-cash-book-id" class="form-control select2-cashbook-pay" required style="width: 100%;">
                                <option value="">— Select Account —</option>
                                @foreach ($cashBooks as $cb)
                                    <option value="{{ $cb->id }}"
                                        data-image="{{ $cb->image ? Storage::url($cb->image) : '' }}"
                                        data-icon="{{ $cb->icon ?? '' }}"
                                        {{ $loop->first ? 'selected' : '' }}>{{ $cb->title }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
$(function() {
    window.applyFilters = function() {
        let month = $('#filter-month').val();
        let year = $('#filter-year').val();
        let batch = $('#filter-batch').val();
        let classId = $('#filter-class').val();
        let status = $('#filter-status').val();
        
        window.location.href = `{{ route('admin.due-collections.index') }}?month=${month}&year=${year}&batch_id=${batch}&class_id=${classId}&status=${status}`;
    };

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    let table = $('#due-collections-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.due-collections.index') }}",
            data: function(d) {
                d.month = $('#filter-month').val();
                d.year = $('#filter-year').val();
                d.batch_id = $('#filter-batch').val();
                d.class_id = $('#filter-class').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'student_name', name: 'student_name' },
            { data: 'student_id_no', name: 'student_id_no' },
            { data: 'batch_name', name: 'batch_name' },
            { data: 'class_name', name: 'class_name' },
            { data: 'section_name', name: 'section_name' },
            { data: 'month_year', name: 'month_year' },
            { data: 'due_amount', name: 'due_amount' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'due_remaining', name: 'due_remaining' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions' }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        buttons: dtButtons
    });

    $('#quickPayModal').on('show.bs.modal', function() {
        if (!$('#quick-pay-student').hasClass('select2-loaded')) {
            $('#quick-pay-student').select2({
                ajax: {
                    url: "{{ route('admin.due-collections.students') }}",
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text + ' - Due: ' + item.total_due
                            }))
                        };
                    }
                }
            }).addClass('select2-loaded');
        }
    });

    $('#quick-pay-student').on('change', function() {
        let studentId = $(this).val();
        if (studentId) {
            $.get("{{ route('admin.due-collections.student-dues', ':id') }}".replace(':id', studentId), function(dues) {
                let html = '';
                dues.forEach(due => {
                    let badgeClass = due.status === 'paid' || due.status === 'free' ? 'paid' : (due.status === 'partial' ? 'partial' : '');
                    html += `
                        <div class="due-item ${badgeClass}">
                            <div>
                                <strong>${due.batch.batch_name}</strong><br>
                                <small>${due.month_name} ${due.year} | Due: ${due.due_amount} | Paid: ${due.paid_amount}</small>
                            </div>
                            <button class="btn btn-sm btn-success pay-single-btn" data-id="${due.id}" data-remaining="${due.due_remaining}">Pay</button>
                        </div>
                    `;
                });
                $('#quick-pay-dues').html(html || '<p class="text-muted">No dues found</p>');
            });
        }
    });

    $(document).on('click', '.pay-btn, .pay-single-btn', function() {
        let dueId = $(this).data('id');
        let dueAmount = $(this).data('due-amount');
        let remaining = $(this).data('remaining');
        
        $('#pay-due-id').val(dueId);
        $('#pay-due-amount').val(dueAmount);
        $('#pay-due-remaining').val(remaining);
        $('#pay-amount').attr('max', remaining);
        $('#payDueModal').modal('show');
    });

    // Cash Book Select2 initialization
    function formatCashBookOption(option) {
        if (!option.id) return option.text;
        const img = $(option.element).data('image');
        const icon = $(option.element).data('icon');
        const iconMap = {wallet:'💰',money:'💵',bank:'🏦',mobile:'📱',card:'💳',gift:'🎁',gold:'🪙',dollar:'💲'};
        let thumbHtml = '';
        if (img) {
            thumbHtml = '<img src="' + img + '" style="width:22px;height:22px;border-radius:50%;object-fit:cover;">';
        } else if (icon && iconMap[icon]) {
            thumbHtml = iconMap[icon];
        } else {
            thumbHtml = '🏦';
        }
        return $('<span><span style="display:inline-flex;align-items:center;gap:8px;padding:2px 0;">' +
            '<span style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;overflow:hidden;">' + thumbHtml + '</span>' +
            '<span style="font-weight:600;font-size:14px;">' + option.text + '</span></span></span>');
    }
    $('.select2-cashbook-pay').select2({
        width: '100%',
        placeholder: '— Select Account —',
        templateResult: formatCashBookOption,
        templateSelection: function(option) {
            if (!option.id) return option.text;
            return formatCashBookOption(option);
        }
    });

    $('#pay-due-form').on('submit', function(e) {
        e.preventDefault();
        let dueId = $('#pay-due-id').val();
        let amount = $('#pay-amount').val();
        let cashBookId = $('input[name="pay_cash_book_id"]:checked').val() || $('#pay-cash-book-id').val();

        if (!cashBookId) {
            alert('Please select a payment method.');
            return;
        }

        $.post("{{ route('admin.due-collections.pay') }}", {
            _token: '{{ csrf_token() }}',
            due_id: dueId,
            amount: amount,
            cash_book_id: cashBookId
        }, function(response) {
            $('#payDueModal').modal('hide');
            table.ajax.reload();
            alert('Payment recorded successfully!');
        }).fail(function() {
            alert('Payment failed. Please try again.');
        });
    });
});
</script>
@endsection
