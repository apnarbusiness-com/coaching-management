{{-- Financial Ledger Index Page --}}
@extends('layouts.admin')
@section('title', 'Financial Ledger')

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .grid-cell {
        border: 1px solid #c2c7d0;
    }

    .excel-table {
        border-collapse: collapse;
        width: 100%;
    }

    .table-wrapper {
        overflow-x: auto;
        max-width: 100%;
        position: relative;
    }

    .table-wrapper table {
        min-width: 1200px;
    }

    .expense-info-btn {
        cursor: pointer;
        color: #fff;
        background: #b91c1c;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 8px;
    }

    .expense-info-btn:hover {
        background: #991b1b;
    }

    .expense-drawer {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.2);
        z-index: 9999;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .expense-drawer.open {
        right: 0;
    }

    .expense-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        display: none;
    }

    .expense-drawer-overlay.open {
        display: block;
    }

    .drawer-close {
        cursor: pointer;
        font-size: 24px;
    }

    .fixed-col {
        position: sticky;
        z-index: 10;
        background: #fff;
    }

    .fixed-left {
        left: 0;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .fixed-right {
        right: 0;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
    }

    thead .fixed-col {
        background: #1F4E79;
    }

    .expense-table thead .fixed-col {
        background: #b91c1c;
    }

    tbody .fixed-col {
        background: #fff;
    }

    tfoot .fixed-col {
        background: #d8e0f1;
    }
</style>
@endpush

@section('content')
    <main class="flex-1 overflow-auto bg-surface-container-low p-4">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Year Filter -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Financial Ledger</h1>
                <form action="{{ route('admin.financial-ledgers.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="year" class="form-select text-sm border rounded px-2 py-1" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                    <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Total Earning</span>
                    <span class="text-2xl font-bold text-primary">{{ number_format($grandTotal) }} BDT</span>
                    @if($percentChange != 0)
                        <div class="text-[10px] {{ $percentChange >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold mt-1">
                            {{ $percentChange >= 0 ? '▲' : '▼' }} {{ abs($percentChange) }}% from last year
                        </div>
                    @else
                        <div class="text-[10px] text-slate-400 font-normal mt-1">No previous data</div>
                    @endif
                </div>
                <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                    <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Active Batches</span>
                    <span class="text-2xl font-bold text-primary">{{ $activeBatches }}</span>
                    <div class="text-[10px] text-slate-400 font-normal mt-1">Operational status: Stable</div>
                </div>
                <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                    <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Total Expense</span>
                    <span class="text-2xl font-bold text-danger">{{ number_format($grandTotalExpense) }} BDT</span>
                    <div class="text-[10px] text-slate-400 font-normal mt-1">Year: {{ $year }}</div>
                </div>
                <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                    <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Net Profit</span>
                    <span class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($netProfit) }} BDT</span>
                    <div class="text-[10px] text-slate-400 font-normal mt-1">{{ $profitMargin }}% profit margin</div>
                </div>
            </div>

            <!-- Earnings Table -->
            <div class="bg-white border-2 border-primary-container shadow-lg">
                <div class="p-4 bg-[#1F4E79] flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Batch Earnings - {{ $year }}</h2>
                </div>
                <div class="table-wrapper">
                    <table class="excel-table min-w-full" id="financialLedgerTable">
                        <thead>
                            <tr class="bg-[#1F4E79]">
                                <th class="grid-cell px-4 py-2 text-left font-header-primary text-white border-r-sky-800 fixed-col fixed-left">
                                    Batch
                                </th>
                                @foreach($months as $month)
                                    <th class="grid-cell px-4 py-2 text-center font-header-primary text-white border-r-sky-800">
                                        {{ $month }}
                                    </th>
                                @endforeach
                                <th class="grid-cell px-4 py-2 text-center font-header-primary text-white fixed-col fixed-right">
                                    Total Earning
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-cell-data font-cell-data text-on-surface">
                            @forelse($batchEarnings as $batch)
                                <tr class="bg-white hover:bg-slate-50 text-sky-900">
                                    <td class="grid-cell px-4 py-1.5 fixed-col fixed-left">{{ $batch['batch_name'] }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td class="grid-cell px-4 py-1.5 text-right">{{ number_format($batch['monthly'][$m] ?? 0) }}</td>
                                    @endfor
                                    <td class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black fixed-col fixed-right">
                                        {{ number_format($batch['total']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4">No earnings found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary-container text-sky-900 font-bold border-t-2 border-primary">
                                <td class="grid-cell px-4 py-3 text-left text-header-primary font-black uppercase tracking-widest fixed-col fixed-left">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td class="grid-cell px-4 py-1.5 text-right">{{ number_format($totalPerMonth[$m] ?? 0) }}</td>
                                @endfor
                                <td class="grid-cell px-4 py-3 text-right bg-primary text-white text-lg fixed-col fixed-right">{{ number_format($grandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="bg-white border-2 border-red-800 shadow-lg mt-6">
                <div class="p-4 bg-red-800 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Batch Expenses - {{ $year }}</h2>
                </div>
                <div class="table-wrapper">
                    <table class="excel-table min-w-full expense-table" id="financialExpenseTable">
                        <thead>
                            <tr class="bg-red-800">
                                <th class="grid-cell px-4 py-2 text-left font-header-primary text-white border-r-red-900 fixed-col fixed-left">
                                    Batch
                                </th>
                                @foreach($months as $month)
                                    <th class="grid-cell px-4 py-2 text-center font-header-primary text-white border-r-red-900">
                                        {{ $month }}
                                    </th>
                                @endforeach
                                <th class="grid-cell px-4 py-2 text-center font-header-primary text-white fixed-col fixed-right">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-cell-data font-cell-data text-on-surface">
                            @forelse($batchExpenses as $batch)
                                <tr class="bg-white hover:bg-slate-50 text-red-900">
                                    <td class="grid-cell px-4 py-1.5 fixed-col fixed-left">
                                        <span class="font-bold">{{ $batch['batch_name'] }}</span>
                                        <button type="button" class="expense-info-btn" onclick="openExpenseDrawer('{{ $batch['batch_id'] }}', '{{ $batch['batch_name'] }}')">
                                            <i class="fas fa-info-circle"></i> Info
                                        </button>
                                    </td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td class="grid-cell px-4 py-1.5 text-right">{{ number_format($batch['monthly'][$m] ?? 0) }}</td>
                                    @endfor
                                    <td class="grid-cell px-4 py-1.5 text-right bg-red-200 font-bold text-black border-l-2 border-red-900 fixed-col fixed-right">
                                        {{ number_format($batch['total']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4">No expenses found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-red-100 text-red-900 font-bold border-t-2 border-red-800">
                                <td class="grid-cell px-4 py-3 text-left text-header-primary font-black uppercase tracking-widest fixed-col fixed-left">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td class="grid-cell px-4 py-1.5 text-right">{{ number_format($totalExpensePerMonth[$m] ?? 0) }}</td>
                                @endfor
                                <td class="grid-cell px-4 py-3 text-right bg-red-600 text-white text-lg fixed-col fixed-right">{{ number_format($grandTotalExpense) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Expense Drawer -->
            <div class="expense-drawer-overlay" id="expenseDrawerOverlay" onclick="closeExpenseDrawer()"></div>
            <div class="expense-drawer" id="expenseDrawer">
                <div class="p-4 bg-red-800 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold" id="drawerTitle">Batch Expenses</h3>
                    <span class="drawer-close" onclick="closeExpenseDrawer()">&times;</span>
                </div>
                <div class="p-4" id="drawerContent">
                    <p class="text-gray-500">Loading...</p>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    const batchExpensesData = @json($batchExpenses);

    function openExpenseDrawer(batchId, batchName) {
        const batch = batchExpensesData.find(b => b.batch_id == batchId);
        const drawer = document.getElementById('expenseDrawer');
        const overlay = document.getElementById('expenseDrawerOverlay');
        const title = document.getElementById('drawerTitle');
        const content = document.getElementById('drawerContent');

        title.textContent = batchName + ' - Expense Details';

        let html = '<div class="space-y-4">';

        if (batch && batch.teachers && batch.teachers.length > 0) {
            batch.teachers.forEach(teacher => {
                html += '<div class="border rounded-lg p-3">';
                html += '<h4 class="font-bold text-red-800 mb-2">' + teacher.teacher_name + '</h4>';
                html += '<table class="w-full text-sm">';
                html += '<thead><tr class="bg-red-50"><th class="text-left p-1">Month</th><th class="text-right p-1">Amount</th></tr></thead>';
                html += '<tbody>';

                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                let teacherTotal = 0;

                for (let i = 1; i <= 12; i++) {
                    const amount = teacher.monthly[i] || 0;
                    if (amount > 0) {
                        teacherTotal += amount;
                        html += '<tr><td class="p-1">' + monthNames[i-1] + '</td><td class="text-right p-1">' + amount.toLocaleString() + ' BDT</td></tr>';
                    }
                }

                html += '<tr class="font-bold bg-red-50"><td class="p-1">Total</td><td class="text-right p-1">' + teacherTotal.toLocaleString() + ' BDT</td></tr>';
                html += '</tbody></table></div>';
            });
        } else {
            html += '<p class="text-gray-500">No teacher expenses found for this batch.</p>';
        }

        html += '</div>';
        content.innerHTML = html;

        drawer.classList.add('open');
        overlay.classList.add('open');
    }

    function closeExpenseDrawer() {
        document.getElementById('expenseDrawer').classList.remove('open');
        document.getElementById('expenseDrawerOverlay').classList.remove('open');
    }
</script>
@endpush