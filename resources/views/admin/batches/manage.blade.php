@extends('layouts.admin')
@section('content')
    <!-- Main Container -->
    <div class="max-w-6xl mx-auto p-6 md:p-10">
        <!-- Page Header Component -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Batch Details: Advanced
                    Physics 2024</h1>
                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    <p class="text-base font-normal">Grade 12 • Evening Session • Mon, Wed, Fri</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button
                    class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-sm transition-colors hover:bg-slate-300 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined text-lg">edit</span>
                    <span>Edit Batch</span>
                </button>
                <button
                    class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-lg">share</span>
                    <span>Export Data</span>
                </button>
            </div>
        </div>
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">payments</span>
                    <p class="text-sm font-medium">Monthly Fee</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">$150</p>
            </div>
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">meeting_room</span>
                    <p class="text-sm font-medium">Classroom</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">Lab 04</p>
            </div>
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">schedule</span>
                    <p class="text-sm font-medium">Timing</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">04:00 - 06:00 PM</p>
            </div>
            <div
                class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">group</span>
                    <p class="text-sm font-medium">Capacity</p>
                </div>
                <p class="text-2xl font-bold tracking-tight">24/30 Users</p>
            </div>
        </div>
        <!-- Navigation Tabs -->
        <div class="mb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex gap-8 overflow-x-auto scrollbar-hide">
                <a class="flex flex-col items-center justify-center border-b-2 border-primary text-primary pb-4 px-1 whitespace-nowrap"
                    href="#">
                    <p class="text-sm font-bold">Overview</p>
                </a>
                <a class="flex flex-col items-center justify-center border-b-2 border-transparent text-slate-500 dark:text-slate-400 pb-4 px-1 whitespace-nowrap hover:text-primary transition-colors"
                    href="#">
                    <p class="text-sm font-bold">Schedule</p>
                </a>
                <a class="flex flex-col items-center justify-center border-b-2 border-transparent text-slate-500 dark:text-slate-400 pb-4 px-1 whitespace-nowrap hover:text-primary transition-colors"
                    href="#">
                    <p class="text-sm font-bold">Fee Tracking</p>
                </a>
                <a class="flex flex-col items-center justify-center border-b-2 border-transparent text-slate-500 dark:text-slate-400 pb-4 px-1 whitespace-nowrap hover:text-primary transition-colors"
                    href="#">
                    <p class="text-sm font-bold">Materials</p>
                </a>
            </div>
        </div>
        <!-- Management Section -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold tracking-tight">Management Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assigned Teachers Card -->
                <div
                    class="flex flex-col gap-5 p-6 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">school</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Assigned Teachers</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">4 Faculty members assigned</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-wider">Active</span>
                    </div>
                    <div class="flex -space-x-3 overflow-hidden py-2">
                        <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white dark:ring-slate-800"
                            data-alt="Portrait of a male teacher"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYgSkIACkVn9SiU-n351RjPmUedUur90Bgiw8iXT6Dq-D6hI-UvW3XNGISDSZP5o5wLBBz_4psJt8_olbzSDvXQ-fof7-EV4qvEeBB4tb-4AdYFGA38fK3TGn3mmNoZZE8G_fniH4-diYHF6wtTToiNOHISoTkqoY7m2ik9UYZGhcWzPVNbslm-HgXG1OxKKlfWLrO0pthQG42_Ac4ViNIlv-rCpnLchA5nY_hKXuSPXt7YIfRuT62WxG77ty7XUkBnJV3LE-sbEKD" />
                        <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white dark:ring-slate-800"
                            data-alt="Portrait of a female teacher"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuB8ggIQqCJm-Jwl2uqaRmKD3wD4gTNoglBw--K6jcDVfQlXPwU6sNDfgykT3gJosV1SEwEQY326N7czK2zeH8jUxlA0IebrQK4qrJx7vr99Tc_YLV9sj9aE2BYN3Q4DT1MFgPBmSOwEmF8BPk-G5rSun_oxZUsM5BO-6_ZbUpGnEkjVbhaBLJ3azSHcDr7hGYArddIPj7ZIxeZmlxf8A3lb6tR4-qkOkj1xPgRvrSMVq_WUS7kq6t7HIJX-gUdGdkf0brkc8jto-jnm" />
                        <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white dark:ring-slate-800"
                            data-alt="Portrait of a science professor"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBSUF3V20whzwEADb9Wa6TzJPVe4oJ9iACDL409OZJyn5y2uX97hnig7sADqdMr6T1GuGe2eCfE5F4WMQ_j_wIvbPosrh73qIfoWfpcMEm83OrVrGtjJhAjnV8p8pUUuc2fRqsQqRvNLsYdqvOnH-wStDO84h0Wn23x-p1k3-kr5JwbtxynfvZwD6aa1_bSyII5VZaweoL2llOAfveIDIv9sbsd3BdSCVdY-fgAGs-lhPR33AD0pGEtZi_SBB8ksObny8FFCrJSy_C7" />
                        <div
                            class="flex items-center justify-center h-10 w-10 rounded-full ring-2 ring-white dark:ring-slate-800 bg-slate-200 dark:bg-slate-700 text-xs font-bold">
                            +1</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-lg bg-primary text-white font-bold text-sm shadow-md hover:opacity-90 transition-opacity">
                            <span class="material-symbols-outlined text-lg">person_add</span>
                            <span>Assign New Teacher</span>
                        </button>
                    </div>
                </div>
                <!-- Enrolled Students Card -->
                <div
                    class="flex flex-col gap-5 p-6 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">group</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Enrolled Students</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">24 Students currently enrolled</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">80%
                            Full</span>
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <div class="flex-1 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full w-[80%]"></div>
                        </div>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-400">24/30</p>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-lg bg-primary text-white font-bold text-sm shadow-md hover:opacity-90 transition-opacity">
                            <span class="material-symbols-outlined text-lg">person_add_alt</span>
                            <span>Enroll New Student</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Summary Table -->
        <div
            class="mt-10 rounded-xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold">Recent Fee Activity</h3>
                <a class="text-primary text-sm font-bold hover:underline" href="#">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr>
                            <td class="px-6 py-4 font-medium">Alex Morgan</td>
                            <td class="px-6 py-4">Oct 24, 2023</td>
                            <td class="px-6 py-4">$150.00</td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    Paid
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Sarah Jenkins</td>
                            <td class="px-6 py-4">Oct 22, 2023</td>
                            <td class="px-6 py-4">$150.00</td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-amber-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    Pending
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">James Wilson</td>
                            <td class="px-6 py-4">Oct 20, 2023</td>
                            <td class="px-6 py-4">$150.00</td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    Paid
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script></script>
@endsection
