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
            /* outline-variant */
        }

        .excel-table {
            border-collapse: collapse;
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Main Content Canvas -->
        <main class="flex-1 overflow-auto bg-surface-container-low p-4">

            <!-- Dashboard Content Container -->
            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Top Bento Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                        <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Portfolio Value</span>
                        <span class="text-2xl font-bold text-primary">$39,925.00</span>
                        <div class="text-[10px] text-green-600 font-bold mt-1">▲ +4.2% from last month</div>
                    </div>
                    <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                        <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Active Accounts</span>
                        <span class="text-2xl font-bold text-primary">14</span>
                        <div class="text-[10px] text-slate-400 font-normal mt-1">Operational status: Stable</div>
                    </div>
                    <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                        <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Risk Exposure</span>
                        <span class="text-2xl font-bold text-error">Low</span>
                        <div class="text-[10px] text-slate-400 font-normal mt-1">Diversification: High</div>
                    </div>
                    <div class="bg-white border border-outline-variant p-4 flex flex-col justify-center">
                        <span class="text-label-sm text-secondary uppercase tracking-wider mb-1">Fiscal Cycle</span>
                        <span class="text-2xl font-bold text-primary">Q1 - Jun</span>
                        <div class="text-[10px] text-slate-400 font-normal mt-1">Next report in 12 days</div>
                    </div>
                </div>
                <!-- MAIN CORE TABLE: EXCEL RECREATION -->
                <div class="bg-white border-2 border-primary-container shadow-lg overflow-x-auto">
                    <table class="excel-table min-w-full" id="financialLedgerTable">
                        <thead>
                            <tr class="bg-[#1F4E79] text-white">
                                <th
                                    class="grid-cell px-4 py-2 text-left font-header-primary text-header-primary border-r-sky-800">
                                    Batch
                                </th>
                                {{-- <th
                                    class="grid-cell px-4 py-2 text-left font-header-primary text-header-primary border-r-sky-800">
                                    Teacher
                                </th> --}}
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    Jan
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    Feb
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    March
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    April
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    May
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    June
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    July
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    August
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    September
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    October
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    November
                                </th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    December
                                </th>
                                <th class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary">
                                    Total Earning
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-cell-data font-cell-data text-on-surface">
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Ict</td>
                                {{-- <td class="grid-cell px-4 py-1.5">Shawon</td> --}}
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2500</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">English-1 &amp; 2</td>
                                {{-- <td class="grid-cell px-4 py-1.5">Sumon</td> --}}
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    4500</td>
                            </tr>


                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary-container text-sky-900 font-bold border-t-2 border-primary">
                                <td class="grid-cell px-4 py-3 text-right text-header-primary font-black uppercase tracking-widest"
                                    colspan="">Total Profit</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-3 text-right bg-primary text-white text-lg">39925</td>
                            </tr>
                        </tfoot>
                    </table>
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
    </div>
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
