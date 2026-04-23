<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Financial Ledger Pro - Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
</head>

<body class="bg-background text-on-surface font-header-primary overflow-hidden flex flex-col h-screen">
    <!-- TopNavBar -->
    <header
        class="bg-[#1F4E79] dark:bg-slate-900 text-white font-sans text-sm tracking-tight font-medium uppercase border-b border-sky-800 flex justify-between items-center h-10 w-full px-0">
        <div class="flex items-center">
            <div class="text-lg font-bold text-white tracking-widest px-4">Financial Ledger Pro</div>
            <nav class="hidden md:flex items-center space-x-1">
                <a class="text-sky-200 hover:text-white hover:bg-sky-800 transition-colors px-3 py-2"
                    href="#">File</a>
                <a class="text-sky-200 hover:text-white hover:bg-sky-800 transition-colors px-3 py-2"
                    href="#">Edit</a>
                <a class="text-white border-b-2 border-white pb-1 px-3 py-2 bg-sky-800" href="#">View</a>
                <a class="text-sky-200 hover:text-white hover:bg-sky-800 transition-colors px-3 py-2"
                    href="#">Data</a>
                <a class="text-sky-200 hover:text-white hover:bg-sky-800 transition-colors px-3 py-2"
                    href="#">Analyze</a>
            </nav>
        </div>
        <div class="flex items-center px-4 space-x-4">
            <button class="material-symbols-outlined text-white hover:bg-sky-800 p-1 rounded transition-colors"
                data-icon="settings">settings</button>
            <button class="material-symbols-outlined text-white hover:bg-sky-800 p-1 rounded transition-colors"
                data-icon="help">help</button>
            <div class="w-6 h-6 rounded-full overflow-hidden border border-sky-300">
                <img alt="User Analyst Profile"
                    data-alt="Close-up of a professional business analyst headshot with neutral office lighting and soft blurred background"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBUZKsTjhT9Mn6pzB4WV6aHoOqLA2FTT7gV_Q0wyf-plNJCofEBBaEWwbx4Hxknu6cNHcOKCzL7xCPis7LcgTcVyEfqpsl3tZmed9WmWnf9HvtONQs1wnbHWHq6W22WQLuTn8wPpwjrc1TEPrGo0a56UtMmzB45ymoL6wmwwfcFeUoNSIiigpDapXCysAqbt_6NQY-DcM032IvyMDlbcfCcA-19zQB6mOsrqUiOZ82VSrzdUylI_YdYHckHNp6wbLT1f-L4GEu6ERY" />
            </div>
        </div>
    </header>
    <div class="flex flex-1 overflow-hidden">
        <!-- SideNavBar -->
        <aside
            class="bg-slate-50 dark:bg-slate-950 text-[#1F4E79] dark:text-sky-400 font-mono text-xs font-semibold flex flex-col h-full border-r border-slate-300 dark:border-slate-800 w-64 shrink-0">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center space-x-3 mb-1">
                    <img alt="System Operator" class="rounded"
                        data-alt="Technical dashboard icon with glowing geometric patterns and deep navy background"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBbAMV9ZrmMfpHTULiOSmwgwO0v9Ib62m5rZIkYyIhk6kibPsPq0-3ir1zv8cMP1W11fAR7dc2KMt1qJzNHveBESzXumkawCXnxIWIo4HRdCKFbo2ovLWM3zhUx1GJLza6G2pCft_y-e1FiOpMiA1o7LVlSFGnHTaTe6yEDeC_gLHj3bArJxv93u8gJVjZEnHR3SqamudGnYljxOzmRSek9ET99KfeT5nTle-xCZcAmO5d6vAPRzoCuickFa1WJviPJkQI7UdbewwY" />
                    <div>
                        <div class="text-sky-900 dark:text-sky-100 font-black">Core Terminal</div>
                        <div class="text-[10px] text-slate-500 font-normal">Active Session: 08:42:01</div>
                    </div>
                </div>
            </div>
            <nav class="flex-1 py-2">
                <a class="flex items-center space-x-3 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900"
                    href="#">
                    <span class="material-symbols-outlined" data-icon="grid_view">grid_view</span>
                    <span>Dashboard</span>
                </a>
                <a class="flex items-center space-x-3 px-4 py-2.5 bg-sky-100 dark:bg-sky-900/30 text-sky-900 dark:text-sky-200 border-l-4 border-sky-700"
                    href="#">
                    <span class="material-symbols-outlined"
                        data-icon="account_balance_wallet">account_balance_wallet</span>
                    <span>Portfolio</span>
                </a>
                <a class="flex items-center space-x-3 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900"
                    href="#">
                    <span class="material-symbols-outlined" data-icon="security">security</span>
                    <span>Risk Assessment</span>
                </a>
                <a class="flex items-center space-x-3 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900"
                    href="#">
                    <span class="material-symbols-outlined" data-icon="payments">payments</span>
                    <span>Cash Flow</span>
                </a>
                <a class="flex items-center space-x-3 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900"
                    href="#">
                    <span class="material-symbols-outlined" data-icon="trending_up">trending_up</span>
                    <span>Market Data</span>
                </a>
            </nav>
            <div class="p-2 border-t border-slate-200 dark:border-slate-800">
                <a class="flex items-center space-x-3 px-4 py-2 text-slate-500 hover:bg-slate-200 rounded"
                    href="#">
                    <span class="material-symbols-outlined text-green-600" data-icon="sensors">sensors</span>
                    <span>Connection Status</span>
                </a>
                <a class="flex items-center space-x-3 px-4 py-2 text-slate-500 hover:bg-slate-200 rounded"
                    href="#">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <!-- Main Content Canvas -->
        <main class="flex-1 overflow-auto bg-surface-container-low p-4">
            <!-- Formula Bar Interface -->
            <div class="bg-white border border-outline-variant mb-4 flex items-center h-8 px-2 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 px-2 border-r border-slate-200 mr-2">fx</div>
                <input class="flex-1 border-none bg-transparent text-cell-data focus:ring-0 px-2" type="text"
                    value="=SUM(C2:G15)" />
            </div>
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
                    <table class="excel-table min-w-full">
                        <thead>
                            <tr class="bg-[#1F4E79] text-white">
                                <th
                                    class="grid-cell px-4 py-2 text-left font-header-primary text-header-primary border-r-sky-800">
                                    Subject</th>
                                <th
                                    class="grid-cell px-4 py-2 text-left font-header-primary text-header-primary border-r-sky-800">
                                    Teacher</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    Jan</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    Feb</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    March</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    April</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    May</th>
                                <th
                                    class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary border-r-sky-800">
                                    June</th>
                                <th class="grid-cell px-4 py-2 text-center font-header-primary text-header-primary">
                                    Total Earning</th>
                            </tr>
                        </thead>
                        <tbody class="text-cell-data font-cell-data text-on-surface">
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Ict</td>
                                <td class="grid-cell px-4 py-1.5">Shawon</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2500</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">English-1 &amp; 2</td>
                                <td class="grid-cell px-4 py-1.5">Sumon</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">700</td>
                                <td class="grid-cell px-4 py-1.5 text-right">800</td>
                                <td class="grid-cell px-4 py-1.5 text-right">900</td>
                                <td class="grid-cell px-4 py-1.5 text-right">1000</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    4500</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Class-6</td>
                                <td class="grid-cell px-4 py-1.5">Naima &amp; Shamim</td>
                                <td class="grid-cell px-4 py-1.5 text-right">250</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">350</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2250</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Class-7</td>
                                <td class="grid-cell px-4 py-1.5">Naima &amp; Shamim</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">350</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2550</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Class-8</td>
                                <td class="grid-cell px-4 py-1.5">Naima &amp; Shamim</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    3150</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Math-9</td>
                                <td class="grid-cell px-4 py-1.5">Rashed</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td class="grid-cell px-4 py-1.5 text-right">700</td>
                                <td class="grid-cell px-4 py-1.5 text-right">750</td>
                                <td class="grid-cell px-4 py-1.5 text-right">800</td>
                                <td class="grid-cell px-4 py-1.5 text-right">850</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    4350</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Phy &amp; Chem-9</td>
                                <td class="grid-cell px-4 py-1.5">Mintu</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td class="grid-cell px-4 py-1.5 text-right">700</td>
                                <td class="grid-cell px-4 py-1.5 text-right">750</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    3750</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Phy &amp; Chem-10</td>
                                <td class="grid-cell px-4 py-1.5">Mintu</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td class="grid-cell px-4 py-1.5 text-right">700</td>
                                <td class="grid-cell px-4 py-1.5 text-right">750</td>
                                <td class="grid-cell px-4 py-1.5 text-right">800</td>
                                <td class="grid-cell px-4 py-1.5 text-right">850</td>
                                <td class="grid-cell px-4 py-1.5 text-right">900</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    4650</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Higher Math-9</td>
                                <td class="grid-cell px-4 py-1.5">Tuhin</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">350</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2550</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Higher Math-10</td>
                                <td class="grid-cell px-4 py-1.5">Tuhin</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    3150</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">General Science</td>
                                <td class="grid-cell px-4 py-1.5">Alamin</td>
                                <td class="grid-cell px-4 py-1.5 text-right">300</td>
                                <td class="grid-cell px-4 py-1.5 text-right">350</td>
                                <td class="grid-cell px-4 py-1.5 text-right">400</td>
                                <td class="grid-cell px-4 py-1.5 text-right">450</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    2550</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Arabic</td>
                                <td class="grid-cell px-4 py-1.5">Abu Bakar</td>
                                <td class="grid-cell px-4 py-1.5 text-right">500</td>
                                <td class="grid-cell px-4 py-1.5 text-right">550</td>
                                <td class="grid-cell px-4 py-1.5 text-right">600</td>
                                <td class="grid-cell px-4 py-1.5 text-right">650</td>
                                <td class="grid-cell px-4 py-1.5 text-right">700</td>
                                <td class="grid-cell px-4 py-1.5 text-right">750</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    3750</td>
                            </tr>
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="grid-cell px-4 py-1.5">Bangla</td>
                                <td class="grid-cell px-4 py-1.5">Hasan</td>
                                <td class="grid-cell px-4 py-1.5 text-right">150</td>
                                <td class="grid-cell px-4 py-1.5 text-right">175</td>
                                <td class="grid-cell px-4 py-1.5 text-right">200</td>
                                <td class="grid-cell px-4 py-1.5 text-right">225</td>
                                <td class="grid-cell px-4 py-1.5 text-right">250</td>
                                <td class="grid-cell px-4 py-1.5 text-right">275</td>
                                <td
                                    class="grid-cell px-4 py-1.5 text-right bg-[#00FF00] font-bold text-black border-l-2 border-black">
                                    1275</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary-container text-sky-900 font-bold border-t-2 border-primary">
                                <td class="grid-cell px-4 py-3 text-right text-header-primary font-black uppercase tracking-widest"
                                    colspan="2">Total Profit</td>
                                <td class="grid-cell px-4 py-3 text-right">5000</td>
                                <td class="grid-cell px-4 py-3 text-right">5625</td>
                                <td class="grid-cell px-4 py-3 text-right">6250</td>
                                <td class="grid-cell px-4 py-3 text-right">6875</td>
                                <td class="grid-cell px-4 py-3 text-right">7500</td>
                                <td class="grid-cell px-4 py-3 text-right">8125</td>
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
    <!-- BottomNavBar (Sheet Tabs) -->
    <nav
        class="bg-[#F3F3F3] dark:bg-slate-900 text-[#1F4E79] dark:text-sky-400 font-sans text-[11px] font-bold fixed bottom-0 left-0 w-full z-50 flex items-center h-8 border-t border-slate-300 dark:border-slate-800">
        <div class="flex items-center h-full px-4 border-r border-slate-200 dark:border-slate-800 bg-slate-200">
            <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
        </div>
        <div class="flex items-center h-full overflow-x-auto">
            <a class="bg-white dark:bg-slate-800 text-sky-800 dark:text-sky-200 border-t-2 border-sky-600 px-4 h-full flex items-center justify-center min-w-[100px]"
                href="#">
                <span class="material-symbols-outlined text-sm mr-1" data-icon="table_rows">table_rows</span>
                Sheet1
            </a>
            <a class="text-slate-500 dark:text-slate-400 px-4 h-full flex items-center justify-center border-r border-slate-200 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-colors min-w-[100px]"
                href="#">
                <span class="material-symbols-outlined text-sm mr-1" data-icon="analytics">analytics</span>
                Summary_Analysis
            </a>
            <a class="text-slate-500 dark:text-slate-400 px-4 h-full flex items-center justify-center border-r border-slate-200 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-colors min-w-[100px]"
                href="#">
                <span class="material-symbols-outlined text-sm mr-1"
                    data-icon="pivot_table_chart">pivot_table_chart</span>
                Pivot_Data
            </a>
            <a class="text-slate-500 dark:text-slate-400 px-4 h-full flex items-center justify-center border-r border-slate-200 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-colors min-w-[100px]"
                href="#">
                <span class="material-symbols-outlined text-sm mr-1" data-icon="description">description</span>
                Reports
            </a>
        </div>
        <div class="ml-auto px-4 flex items-center gap-4 text-slate-500">
            <span class="font-normal">Ready</span>
            <span class="material-symbols-outlined text-sm" data-icon="view_compact">view_compact</span>
            <div class="flex items-center gap-1 border-l border-slate-300 pl-4">
                <span class="material-symbols-outlined text-sm" data-icon="remove">remove</span>
                <span>100%</span>
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
            </div>
        </div>
    </nav>
</body>

</html>
