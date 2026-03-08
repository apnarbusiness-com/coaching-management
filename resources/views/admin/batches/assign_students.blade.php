@extends('layouts.admin')
@section('content')
    <div class="max-w-6xl mx-auto p-6 flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Assign Students to Batch
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">Enroll students into the <strong>Evening Advanced
                        Mathematics</strong> batch.</p>
            </div>
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-primary/10 dark:bg-primary/20 rounded-lg text-primary text-sm font-medium">
                    <span class="material-symbols-outlined text-sm">group</span>
                    <span>Capacity: 22/30</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500"
                    placeholder="Search students by name, ID or current grade..." type="text" />
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-sm">filter_list</span>
                    <span>Filter</span>
                </button>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-bottom border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 w-12 text-center">
                                <input
                                    class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                    type="checkbox" />
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Student
                                Details</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Student ID
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Current Class
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Fee Status
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input checked=""
                                    class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                    type="checkbox" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs uppercase">
                                        JD</div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">John Doe</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">john.doe@email.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">STU-2024-001</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">Grade 12 (A)</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-[11px] font-bold uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input checked=""
                                    class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                    type="checkbox" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs uppercase">
                                        SS</div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">Sarah Smith</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">sarah.s@email.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">STU-2024-042</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">Grade 11 (B)</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-[11px] font-bold uppercase bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input
                                    class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                    type="checkbox" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs uppercase">
                                        MW</div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">Michael Wong</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">m.wong@email.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">STU-2023-118</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">Grade 12 (A)</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-[11px] font-bold uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input
                                    class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-primary focus:ring-primary"
                                    type="checkbox" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs uppercase">
                                        EJ</div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">Emily Johnson</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">emily.j@email.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">STU-2024-089</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">Grade 11 (C)</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-[11px] font-bold uppercase bg-slate-100 text-slate-600 dark:bg-slate-900/40 dark:text-slate-400">Not
                                    Paid</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div
            class="mt-auto flex flex-col md:flex-row items-center justify-between gap-4 p-4 md:p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg sticky bottom-6">
            <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                        <span class="material-symbols-outlined">person_add</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">12 Students Selected</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Out of 154 total available</p>
                    </div>
                </div>
                <div class="h-10 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>
                <div class="flex flex-col">
                    <p class="text-xs uppercase font-bold text-slate-400 tracking-wider">Estimated Revenue</p>
                    <p class="text-lg font-bold text-primary">$1,200.00 <span
                            class="text-sm font-normal text-slate-500">/mo</span></p>
                </div>
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <button
                    class="flex-1 md:flex-none px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Cancel
                </button>
                <button
                    class="flex-1 md:flex-none px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all">
                    Confirm Enrollment
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script></script>
@endsection
