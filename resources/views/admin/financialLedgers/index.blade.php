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

        .fixed-col {
            position: sticky;
            z-index: 10;
            background: #fff;
        }

        .fixed-left {
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .fixed-right {
            right: 0;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        }

        thead .fixed-col {
            background: #1F4E79;
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

    <!-- Main Content Canvas -->
    <main class="flex-1 overflow-auto bg-surface-container-low p-4">

        <!-- Dashboard Content Container -->
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Top Bento Row -->
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
                    <span class="text-2xl font-bold text-danger">{{ number_format($totalExpense) }} BDT</span>
                    <div class="text-[10px] text-slate-400 font-normal mt-1">Year: {{ $year }}</div>
                </div>
                <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                    <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Net Profit</span>
                    <span class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-success' : 'text-warning' }}">{{ number_format($netProfit) }} BDT</span>
                    <div class="text-[10px] text-slate-400 font-normal mt-1">{{ $profitMargin }}% profit margin</div>
                </div>
            </div>
            <!-- MAIN CORE TABLE: EXCEL RECREATION -->
            <div class="bg-white border-2 border-primary-container shadow-lg">
                <div class="p-4 bg-[#1F4E79] flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Financial Ledger - {{ $year }}</h2>
                    <form action="{{ route('admin.financial-ledgers.index') }}" method="GET"
                        class="flex items-center gap-2 text-sky-900">
                        <select name="year" class="form-select text-sm" onchange="this.form.submit()">
                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="excel-table min-w-full" id="financialLedgerTable">
                        <thead>
                            <tr class="bg-[#1F4E79]">
                                <th
                                    class="grid-cell px-4 py-2 text-left font-header-primary text-white border-r-sky-800 fixed-col fixed-left">
                                    Batch
                                </th>
                                @foreach ($months as $month)
                                    <th
                                        class="grid-cell px-4 py-2 text-center font-header-primary text-white border-r-sky-800">
                                        {{ $month }}
                                    </th>
                                @endforeach
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-white fixed-col fixed-right">
                                    Total Earning
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-cell-data font-cell-data text-on-surface">
                            @forelse($batchEarnings as $batch)
                                <tr class="bg-white hover:bg-slate-50 text-sky-900">
                                    <td class="grid-cell px-4 py-1.5 fixed-col fixed-left">{{ $batch['batch_name'] }}</td>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <td class="grid-cell px-4 py-1.5 text-right">
                                            {{ number_format($batch['monthly'][$m] ?? 0) }}</td>
                                    @endfor
                                    <td style="background: #00FF00;"
                                        class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black fixed-col fixed-right">
                                        {{ number_format($batch['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4">No batches found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary-container text-sky-900 font-bold border-t-2 border-primary">
                                <td
                                    class="grid-cell px-4 py-3 text-left text-header-primary font-black uppercase tracking-widest fixed-col fixed-left">
                                    Total</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td class="grid-cell px-4 py-1.5 text-right">
                                        {{ number_format($totalPerMonth[$m] ?? 0) }}</td>
                                @endfor
                                <td
                                    class="grid-cell px-4 py-3 text-right bg-primary text-white text-lg fixed-col fixed-right">
                                    {{ number_format($grandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Secondary Data Row (Visual Density Improvement) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white border border-outline-variant p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-primary flex items-center gap-2">
                            <span class="material-symbols-outlined" data-icon="analytics">analytics</span>
                            Trend Analysis
                        </h3>
                        <span class="text-[10px] bg-sky-100 text-sky-800 px-2 py-0.5 rounded-full font-bold">LIVE
                            DATA</span>
                    </div>
                    <div class="h-32 bg-slate-50 flex items-end justify-between px-6 pb-2 gap-2">
                        <div class="w-full bg-sky-300 h-[40%]" title="Jan"></div>
                        <div class="w-full bg-sky-400 h-[50%]" title="Feb"></div>
                        <div class="w-full bg-sky-500 h-[60%]" title="Mar"></div>
                        <div class="w-full bg-sky-600 h-[70%]" title="Apr"></div>
                        <div class="w-full bg-sky-700 h-[85%]" title="May"></div>
                        <div class="w-full bg-primary h-[100%]" title="Jun"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-[10px] font-bold text-slate-400 px-2">
                        <span>JAN</span><span>FEB</span><span>MAR</span><span>APR</span><span>MAY</span><span>JUN</span>
                    </div>
                </div>
                <div class="bg-white border border-outline-variant p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-primary flex items-center gap-2">
                            <span class="material-symbols-outlined" data-icon="description">description</span>
                            Key Documents
                        </h3>
                        <button class="text-xs text-primary underline">View All</button>
                    </div>
                    <div class="space-y-2">
                        <div
                            class="flex items-center justify-between p-2 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-red-500"
                                    data-icon="picture_as_pdf">picture_as_pdf</span>
                                <span class="text-cell-data">Q2_Forecast_Summary.pdf</span>
                            </div>
                            <span class="text-label-sm text-slate-400">1.2 MB</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-2 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600"
                                    data-icon="table_view">table_view</span>
                                <span class="text-cell-data">Raw_Export_June_2024.csv</span>
                            </div>
                            <span class="text-label-sm text-slate-400">452 KB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

{{-- @push('scripts')
    @parent
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-variant": "#e2e2e2",
                        "surface-tint": "#35618d",
                        "on-secondary-fixed-variant": "#3f4755",
                        "surface-container": "#eeeeee",
                        "secondary-container": "#d8e0f1",
                        "on-secondary-fixed": "#141c28",
                        "inverse-surface": "#2f3131",
                        "primary-container": "#1f4e79",
                        "surface-dim": "#dadada",
                        "primary-fixed-dim": "#a0cafc",
                        "on-tertiary-fixed-variant": "#2b4e36",
                        "on-background": "#1a1c1c",
                        "primary-fixed": "#d1e4ff",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-container": "#2f533b",
                        "on-surface": "#1a1c1c",
                        "tertiary-fixed-dim": "#a8d0b1",
                        "error": "#ba1a1a",
                        "surface": "#faf9f9",
                        "tertiary-fixed": "#c4edcc",
                        "tertiary": "#183c25",
                        "on-primary-fixed": "#001d35",
                        "outline": "#72777f",
                        "outline-variant": "#c2c7d0",
                        "on-tertiary-fixed": "#00210e",
                        "on-secondary-container": "#5b6371",
                        "surface-container-highest": "#e2e2e2",
                        "on-tertiary-container": "#9ec5a6",
                        "background": "#faf9f9",
                        "surface-container-high": "#e8e8e8",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#f4f3f3",
                        "on-tertiary": "#ffffff",
                        "on-error-container": "#93000a",
                        "secondary-fixed-dim": "#bfc7d7",
                        "secondary": "#575f6d",
                        "inverse-on-surface": "#f1f1f0",
                        "secondary-fixed": "#dbe3f4",
                        "on-primary-container": "#95bff1",
                        "on-surface-variant": "#42474f",
                        "on-error": "#ffffff",
                        "on-secondary": "#ffffff",
                        "primary": "#00375e",
                        "on-primary-fixed-variant": "#184974",
                        "on-primary": "#ffffff",
                        "surface-bright": "#faf9f9",
                        "inverse-primary": "#a0cafc"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "header-height": "32px",
                        "cell-padding-y": "4px",
                        "sub-header-height": "28px",
                        "grid-border-width": "1px",
                        "cell-padding-x": "8px"
                    },
                    "fontFamily": {
                        "cell-numeric": ["Inter"],
                        "cell-data": ["Inter"],
                        "label-sm": ["Inter"],
                        "header-secondary": ["Inter"],
                        "header-primary": ["Inter"]
                    },
                    "fontSize": {
                        "cell-numeric": ["13px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.02em",
                            "fontWeight": "400"
                        }],
                        "cell-data": ["13px", {
                            "lineHeight": "16px",
                            "fontWeight": "400"
                        }],
                        "label-sm": ["11px", {
                            "lineHeight": "14px",
                            "fontWeight": "500"
                        }],
                        "header-secondary": ["13px", {
                            "lineHeight": "18px",
                            "fontWeight": "600"
                        }],
                        "header-primary": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "600"
                        }]
                    }
                }
            }
        }
    </script>
@endpush --}}
