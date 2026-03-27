@extends('layouts.admin')

@section('style')
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Manrope', sans-serif;
        }

        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
@endsection
@section('content')
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-container": "#949eb8",
                        "secondary-fixed-dim": "#ffb77b",
                        "secondary": "#904d00",
                        "on-error": "#ffffff",
                        "background": "#f8f9fa",
                        "on-tertiary": "#ffffff",
                        "on-secondary-container": "#683700",
                        "on-primary-fixed": "#13183f",
                        "on-primary-fixed-variant": "#3f446d",
                        "on-primary": "#ffffff",
                        "outline-variant": "#c7c5cf",
                        "on-secondary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#77767f",
                        "inverse-surface": "#2e3132",
                        "on-error-container": "#93000a",
                        "on-background": "#191c1d",
                        "inverse-primary": "#bfc3f4",
                        "secondary-fixed": "#ffdcc2",
                        "on-tertiary-fixed": "#111b30",
                        "surface-container": "#edeeef",
                        "tertiary-fixed": "#d8e2ff",
                        "inverse-on-surface": "#f0f1f2",
                        "primary-fixed-dim": "#bfc3f4",
                        "primary-container": "#2d325a",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#969bc9",
                        "surface-container-highest": "#e1e3e4",
                        "on-secondary-fixed": "#2e1500",
                        "secondary-container": "#fd9837",
                        "surface-variant": "#e1e3e4",
                        "surface-dim": "#d9dadb",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#46464e",
                        "surface-container-low": "#f3f4f5",
                        "primary": "#171c43",
                        "surface-container-high": "#e7e8e9",
                        "surface": "#f8f9fa",
                        "primary-fixed": "#dfe0ff",
                        "tertiary": "#152035",
                        "on-tertiary-fixed-variant": "#3d475e",
                        "tertiary-fixed-dim": "#bcc6e2",
                        "on-secondary-fixed-variant": "#6d3900",
                        "tertiary-container": "#2b354b",
                        "surface-tint": "#565b86",
                        "surface-bright": "#f8f9fa",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Manrope"],
                        "label": ["Manrope"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>


    <div class="bg-background text-on-background min-h-screen">

        <!-- Main Content Canvas -->
        <main class=" p-6 lg:p-10">
            <!-- Dashboard Header & Welcome -->
            <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-secondary font-bold text-sm tracking-widest uppercase font-label">Dashboard
                        Overview</span>
                    <h2 class="text-4xl font-extrabold text-primary font-headline tracking-tight mt-1">Welcome back, Prof.
                        Alistair
                    </h2>
                    <p class="text-on-surface-variant mt-2 text-lg">Your academic architect dashboard for today's session
                        management.</p>
                </div>
                <div
                    class="flex items-center gap-3 bg-surface-container-low p-2 rounded-full border border-outline-variant/10">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-primary-container">
                        <img class="w-full h-full object-cover"
                            data-alt="professional portrait of a middle-aged male teacher with glasses in a formal academic setting with soft library background"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkr_tX-lFf5x9AUJCh-GucC3waz4N4xG5ypjgZCU3ePFCYgv1FRpS1ae5YXSbWioIf7CZFEDTpzYEUihfd4y9kcqvadDqX0l5BG__PIB35z_pvIhZKjtG4mk8KstnrJ5WW8S8sSWMlOJBYDbxVyemsJqHnijDK2yXNvBWA2lf1zdL-D2FwCOpyQFHY9pJ2DTYwCUxEbkz_nBkCBNxE5dFESLI17fCj7m5NhzmQlakBFW0ZY0rIUYKVp8NX5IuSrXzcdGJQxRMeujZ9" />
                    </div>
                    <div class="pr-4">
                        <p class="text-sm font-bold text-primary">Teacher ID: #AA-9421</p>
                        <p class="text-xs text-secondary font-semibold">Senior Faculty</p>
                    </div>
                </div>
            </header>
            <!-- Bento Grid: Financials and Key Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                <!-- Financial Summary Card (Wide) -->
                <div
                    class="lg:col-span-8 bg-surface-container-lowest rounded-xl p-8 shadow-[0_20px_40px_-10px_rgba(25,28,29,0.06)] relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full -mr-20 -mt-20 pointer-events-none">
                    </div>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 relative z-10">
                        <h3 class="text-xl font-bold text-primary font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
                            Financial Summary
                        </h3>
                        <button
                            class="text-primary text-sm font-bold border-b-2 border-secondary/30 hover:border-secondary transition-all">View
                            Full Ledger</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                        <div class="space-y-1">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Latest Payment</p>
                            <p class="text-3xl font-extrabold text-primary tracking-tight">$3,240.00</p>
                            <p class="text-xs text-emerald-600 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                Settled Aug 14
                            </p>
                        </div>
                        <div class="space-y-1 md:border-l md:border-outline-variant/20 md:pl-8">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Pending Earnings</p>
                            <p class="text-3xl font-extrabold text-secondary tracking-tight">$1,850.50</p>
                            <p class="text-xs text-on-surface-variant font-bold">Estimated Aug 30</p>
                        </div>
                        <div class="space-y-1 md:border-l md:border-outline-variant/20 md:pl-8">
                            <p class="text-on-surface-variant text-sm font-label font-medium">Annual Revenue</p>
                            <p class="text-3xl font-extrabold text-primary tracking-tight">$42,900</p>
                            <div class="h-1.5 w-full bg-surface-container-highest rounded-full mt-2 overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-secondary to-secondary-container w-3/4 rounded-full">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Stats Card -->
                <div
                    class="lg:col-span-4 bg-primary rounded-xl p-8 text-white flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 pointer-events-none"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold font-headline mb-4">Class Productivity</h3>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">Active Batches</span>
                                <span class="text-xl font-bold">08</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">Total Students</span>
                                <span class="text-xl font-bold">142</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium opacity-80">Hours This Week</span>
                                <span class="text-xl font-bold">34h</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
                        <button
                            class="w-full bg-white text-primary py-2.5 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-opacity-90 transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Export Report
                        </button>
                    </div>
                </div>
            </div>
            <!-- Two Column Layout: Batches & History -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <!-- Active Batches Column -->
                <div class="xl:col-span-4 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-bold text-primary font-headline">Active Batches</h3>
                        <span class="px-3 py-1 bg-secondary/10 text-secondary text-xs font-bold rounded-full">Weekly
                            Schedule</span>
                    </div>
                    <!-- Batch Card 1 -->
                    <div
                        class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-secondary shadow-sm hover:translate-x-1 transition-transform cursor-pointer">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-xs font-bold text-on-surface-variant font-label">BATCH #ID-882</p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest bg-primary/5 px-2 py-0.5 rounded text-primary">Advanced
                                Physics</span>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">Quantum Mechanics A</h4>
                        <div class="flex flex-wrap gap-y-2 gap-x-4">
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                <span class="text-xs font-semibold">Mon, Wed, Fri</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                <span class="text-xs font-semibold">09:00 - 11:30 AM</span>
                            </div>
                        </div>
                    </div>
                    <!-- Batch Card 2 -->
                    <div
                        class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-primary shadow-sm hover:translate-x-1 transition-transform cursor-pointer">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-xs font-bold text-on-surface-variant font-label">BATCH #ID-441</p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest bg-primary/5 px-2 py-0.5 rounded text-primary">Mathematics</span>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">Calculus III: Integration</h4>
                        <div class="flex flex-wrap gap-y-2 gap-x-4">
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                <span class="text-xs font-semibold">Tue, Thu</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                <span class="text-xs font-semibold">02:00 - 04:30 PM</span>
                            </div>
                        </div>
                    </div>
                    <!-- Batch Card 3 -->
                    <div
                        class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-primary shadow-sm hover:translate-x-1 transition-transform cursor-pointer">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-xs font-bold text-on-surface-variant font-label">BATCH #ID-109</p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest bg-primary/5 px-2 py-0.5 rounded text-primary">Computer
                                Science</span>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">Intro to Algorithms</h4>
                        <div class="flex flex-wrap gap-y-2 gap-x-4">
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                <span class="text-xs font-semibold">Saturday</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                <span class="text-xs font-semibold">10:00 AM - 01:00 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Payment History Column -->
                <div class="xl:col-span-8">
                    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                        <div class="p-6 border-b border-surface-container-high flex justify-between items-center">
                            <h3 class="text-xl font-bold text-primary font-headline">Payment History</h3>
                            <div class="flex gap-2">
                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                                    <input
                                        class="bg-surface-container-low border-none rounded-full pl-9 pr-4 py-1.5 text-xs focus:ring-2 focus:ring-primary/20 w-48 font-medium"
                                        placeholder="Search transactions..." type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-surface-container-low">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Reference
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Batch Detail
                                        </th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Date</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Amount</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-primary uppercase tracking-wider font-label">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container-high">
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-5 font-bold text-sm text-primary">#TRX-99201</td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-bold text-primary">Advance Physics July-Aug</p>
                                            <p class="text-xs text-on-surface-variant">32 Students Enrolled</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-medium text-on-surface-variant">Aug 14, 2023</td>
                                        <td class="px-6 py-5 text-sm font-bold text-primary">$1,240.00</td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full">PAID</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-5 font-bold text-sm text-primary">#TRX-99185</td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-bold text-primary">Mathematics Intensive</p>
                                            <p class="text-xs text-on-surface-variant">15 Students Enrolled</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-medium text-on-surface-variant">Aug 02, 2023</td>
                                        <td class="px-6 py-5 text-sm font-bold text-primary">$2,000.00</td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full">PAID</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-5 font-bold text-sm text-primary">#TRX-99150</td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-bold text-primary">Algorithm Workshop</p>
                                            <p class="text-xs text-on-surface-variant">40 Students Enrolled</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-medium text-on-surface-variant">Jul 28, 2023</td>
                                        <td class="px-6 py-5 text-sm font-bold text-primary">$850.50</td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="px-3 py-1 bg-secondary/10 text-secondary text-[10px] font-extrabold rounded-full">PENDING</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-5 font-bold text-sm text-primary">#TRX-99042</td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-bold text-primary">Mechanics Lab Fees</p>
                                            <p class="text-xs text-on-surface-variant">Equipment Overhead</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-medium text-on-surface-variant">Jul 15, 2023</td>
                                        <td class="px-6 py-5 text-sm font-bold text-primary">$1,100.00</td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full">PAID</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-surface-container-high flex items-center justify-between">
                            <p class="text-xs font-bold text-on-surface-variant font-label">Showing 4 of 12 transactions
                            </p>
                            <div class="flex gap-2">
                                <button
                                    class="p-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-low transition-colors">
                                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                                </button>
                                <button
                                    class="p-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-low transition-colors">
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Accent -->
            <footer class="mt-16 pt-8 border-t border-outline-variant/10 text-center">
                <div class="inline-block bg-white px-8 py-3 rounded-full border border-outline-variant/5 shadow-sm">
                    <p class="text-xs font-bold text-on-surface-variant flex items-center gap-4">
                        <span>Next Batch Starts: <span class="text-secondary">Sept 01, 2023</span></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span>System Status: <span class="text-emerald-500">Operational</span></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span>Support: support@academicarchitect.com</span>
                    </p>
                </div>
            </footer>
        </main>
    </div>
@endsection
