@extends('layouts.admin')
@section('content')
    <!-- Modal Overlay -->
    <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm z-0"></div>
    <!-- Modal Content -->
    <div
        class="relative z-10 w-full max-w-lg bg-white dark:bg-surface-dark rounded-xl shadow-2xl border border-slate-200 dark:border-border-dark overflow-hidden">
        <!-- Header -->
        <div class="px-8 pt-8 pb-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Assign Teacher</h2>
                <button
                    class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                Add a teacher to the <span class="text-primary font-medium">Advanced Physics - Batch A</span> and set
                their compensation.
            </p>
        </div>
        <!-- Form Content -->
        <div class="px-8 py-4 space-y-6">
            <!-- Teacher Selection Section -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">person_search</span>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Teacher Selection</h3>
                </div>
                <div class="relative">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 ml-1">Select
                        Teacher</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <select
                            class="custom-select-arrow w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                            <option disabled="" selected="" value="">Search by name or specialty</option>
                            <option value="1">Dr. Robert Chen (Physics)</option>
                            <option value="2">Sarah Jenkins (Mathematics)</option>
                            <option value="3">Michael Vance (Chemistry)</option>
                            <option value="4">Elena Rodriguez (Biology)</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Divider -->
            <div class="h-px bg-slate-200 dark:bg-border-dark"></div>
            <!-- Assignment Details Section -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">assignment_ind</span>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Assignment Details</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Fee Input -->
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Fee / Amount
                            ($)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">$</span>
                            <input
                                class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                placeholder="0.00" type="number" value="1200.00" />
                        </div>
                        <p class="text-[10px] text-slate-400 ml-1 italic">Default batch rate applied</p>
                    </div>
                    <!-- Role Selector -->
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Role</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input checked="" class="peer hidden" name="role" type="radio" value="primary" />
                                <div
                                    class="flex items-center justify-center py-3 px-2 border border-slate-200 dark:border-border-dark rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all">
                                    Primary
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input class="peer hidden" name="role" type="radio" value="assistant" />
                                <div
                                    class="flex items-center justify-center py-3 px-2 border border-slate-200 dark:border-border-dark rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all">
                                    Assistant
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Actions -->
        <div
            class="px-8 py-6 bg-slate-50 dark:bg-background-dark/50 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <button
                class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-surface-dark transition-colors">
                Cancel
            </button>
            <button
                class="w-full sm:w-auto px-8 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                Confirm Assignment
            </button>
        </div>
    </div>
    <!-- Background Decoration -->
    <div class="fixed top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -z-10 -translate-y-1/2 translate-x-1/2">
    </div>
    <div class="fixed bottom-0 left-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -z-10 translate-y-1/2 -translate-x-1/2">
    </div>
@endsection

@section('scripts')
    @parent
    <script></script>
@endsection
