@extends('layouts.admin')
@section('content')
    {{-- <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        Dashboard
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto flex flex-col">

        <div class="p-8 space-y-8">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50">
                    Welcome back, <span class="text-primary"> {{ auth()->user()->student->first_name ?? 'Student' }}</span>
                    👋</h1>
                <p class="text-slate-500 mt-1">Here is a quick look at your academic progress and schedule.</p>
            </div>
            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Next Class Card (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    {{-- <div
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="p-6 flex flex-col md:flex-row gap-6">
                            <div class="w-full md:w-48 h-32 bg-slate-200 dark:bg-slate-800 rounded-lg bg-cover bg-center"
                                data-alt="Abstract algorithmic data visualization pattern"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAtmB0WHopcW1_xelNXbB-xPpTsMIF9hsbI5uZTziCQ4MepJtIo2auXqN0xh92EGFDnXbxQIimtp54xh79gl4aomdXE_WvdeEw7QwOu24PD-dCzz2C2OrhEi2pHQRM9yXXM70KrCVyPPqJ8yRYklrbQDpGeq1iEDVZywuUgFVczrpu3xtaGQzBZm5lX642pmNaG-BNCuHlorLISRd8ZXSlFdpenUbsFpPKkgczGPzE9g8sH60dL9jdlU_N8bxjPGZ61cMrkBnR311e5");'>
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="px-2 py-0.5 bg-primary/10 text-primary text-xs font-bold rounded uppercase">Upcoming</span>
                                        <span class="text-xs text-slate-400">• Room 402</span>
                                    </div>
                                    <h3 class="text-xl font-bold">Advanced Algorithms</h3>
                                    <p class="text-slate-500 text-sm">Prof. Sarah Smith • Computer Science Dept.</p>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex gap-4">
                                        <div class="text-center">
                                            <p class="text-xl font-bold leading-none">01</p>
                                            <p class="text-[10px] text-slate-400 uppercase font-bold">Hours</p>
                                        </div>
                                        <div class="text-xl font-bold">:</div>
                                        <div class="text-center">
                                            <p class="text-xl font-bold leading-none">45</p>
                                            <p class="text-[10px] text-slate-400 uppercase font-bold">Mins</p>
                                        </div>
                                    </div>
                                    <button
                                        class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg text-sm font-semibold transition-all">Join
                                        Online</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Quick Links -->
                    <div class="grid grid-cols-3 gap-4">
                        <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                            href="#">
                            <span class="material-symbols-outlined text-primary mb-2">person</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Profile</p>
                        </a>
                        <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                            href="#">
                            <span class="material-symbols-outlined text-primary mb-2">layers</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Batches</p>
                        </a>
                        <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                            href="#">
                            <span class="material-symbols-outlined text-primary mb-2">fact_check</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Attendance</p>
                        </a>
                    </div>

                    <!-- Active Batches -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100">My Active Batches</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead
                                    class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">Batch Name</th>
                                        <th class="px-6 py-4">Instructor</th>
                                        <th class="px-6 py-4">Schedule</th>
                                        <th class="px-6 py-4">Attendance</th>
                                        <th class="px-6 py-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr class="text-sm text-slate-900 dark:text-slate-100">
                                        <td class="px-6 py-4 font-semibold">CS202: Advanced Algorithms</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Dr. Sarah Smith</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Mon, Wed (2:00 PM)</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-24 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-green-500 w-[92%]"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-900 dark:text-slate-100">92%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button class="text-primary font-bold hover:underline">View</button>
                                        </td>
                                    </tr>
                                    <tr class="text-sm text-slate-900 dark:text-slate-100">
                                        <td class="px-6 py-4 font-semibold">CS205: Database Systems</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Prof. Michael Chen</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Tue, Thu (10:00 AM)</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-24 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-primary w-[85%]"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-900 dark:text-slate-100">85%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button class="text-primary font-bold hover:underline">View</button>
                                        </td>
                                    </tr>
                                    <tr class="text-sm text-slate-900 dark:text-slate-100">
                                        <td class="px-6 py-4 font-semibold">MA101: Discrete Mathematics</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Dr. Emily Watson</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">Fri (1:00 PM)</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-24 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-yellow-500 w-[74%]"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-900 dark:text-slate-100">74%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button class="text-primary font-bold hover:underline">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-6">

                    <!-- Due Balance Summary -->
                    <div class="bg-slate-900 text-white rounded-xl p-6 relative overflow-hidden">
                        <div class="relative z-10">
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Dues</p>
                            {{-- <h3 class="text-3xl font-bold">$1,240.50</h3> --}}
                            <h3 class="text-3xl font-bold">$0.00</h3>
                            <p class="text-slate-400 text-xs mt-4">Next payment due: Oct 15, 2023</p>
                            <button
                                class="mt-6 w-full py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">Pay
                                Now</button>
                        </div>
                        <div class="absolute -right-4 -bottom-4 opacity-10">
                            <span class="material-symbols-outlined text-9xl">account_balance_wallet</span>
                        </div>
                    </div>


                    <!-- Recent Notifications -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100">Recent Alerts</h4>
                            <a class="text-xs text-primary font-bold" href="#">View All</a>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex gap-3 items-start p-3 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
                                <span class="material-symbols-outlined text-red-500 text-xl">report</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                        Exam Registration Closing</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Finish your
                                        registration by tomorrow 5PM.</p>
                                </div>
                            </div>
                            <div
                                class="flex gap-3 items-start p-3 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-primary rounded">
                                <span class="material-symbols-outlined text-primary text-xl">update</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                        Lecture Rescheduled</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">CS101 moved to
                                        Friday, 10:00 AM.</p>
                                </div>
                            </div>
                            <div
                                class="flex gap-3 items-start p-3 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded">
                                <span class="material-symbols-outlined text-green-500 text-xl">check_circle</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                        Payment Received</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Receipt #9912
                                        generated successfully.</p>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </main>
@endsection
@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {});
    </script>
@endsection
