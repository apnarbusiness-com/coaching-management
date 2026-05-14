@extends('layouts.admin')
@section('title', 'Teachers — Details')
@section('content')

    <!-- Page Scroll Container -->
    <div
        class="flex-1 overflow-y-auto p-6 lg:px-10 lg:py-8 bg-background-light dark:bg-background-dark transition-colors duration-200">
        <div class="max-w-[1280px] mx-auto flex flex-col gap-8">
            <!-- Breadcrumbs & Quick Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <nav class="flex items-center gap-2 text-sm">
                    <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                        href="{{ route('admin.home') }}">Dashboard</a>
                    <span class="text-text-secondary dark:text-gray-500">/</span>
                    <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors" 
                        href="{{ route('admin.teachers.index') }}">Teachers</a>
                    <span class="text-text-secondary dark:text-gray-500">/</span>
                    <span class="text-text-main dark:text-white font-medium">Teacher Profile</span>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.teachers.edit', [$teacher->id]) }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit Profile
                    </a>
                    <a href="{{ route('admin.teachers.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-text-secondary dark:text-gray-300 bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark hover:bg-white dark:hover:bg-white/5 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Profile Header Card -->
            <div class="bg-card-light dark:bg-card-dark rounded-2xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-primary/20 via-primary/10 to-transparent"></div>
                <div class="px-6 lg:px-8 pb-8 -mt-12">
                    <div class="flex flex-col md:flex-row items-end gap-6">
                        <div class="relative">
                            <div class="size-32 rounded-2xl bg-white dark:bg-card-dark p-1 shadow-xl">
                                @if($teacher->profile_img)
                                    <img src="{{ $teacher->profile_img->getUrl() }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover rounded-xl">
                                @else
                                    <div class="w-full h-full bg-background-light dark:bg-background-dark rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl text-text-secondary">person</span>
                                    </div>
                                @endif
                            </div>
                            @if($teacher->status)
                                <span class="absolute -top-2 -right-2 px-2.5 py-1 bg-green-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-lg border-2 border-white dark:border-card-dark">Active</span>
                            @else
                                <span class="absolute -top-2 -right-2 px-2.5 py-1 bg-red-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-lg border-2 border-white dark:border-card-dark">Inactive</span>
                            @endif
                        </div>
                        <div class="flex-1 mb-2">
                            <h1 class="text-3xl font-bold text-text-main dark:text-white tracking-tight">{{ $teacher->name }}</h1>
                            <div class="flex flex-wrap items-center gap-4 mt-2">
                                <span class="flex items-center gap-1.5 text-sm text-text-secondary dark:text-gray-400">
                                    <span class="material-symbols-outlined text-[18px] text-primary">badge</span>
                                    {{ $teacher->emloyee_code }}
                                </span>
                                <span class="flex items-center gap-1.5 text-sm text-text-secondary dark:text-gray-400">
                                    <span class="material-symbols-outlined text-[18px] text-primary">mail</span>
                                    {{ $teacher->email }}
                                </span>
                                <span class="flex items-center gap-1.5 text-sm text-text-secondary dark:text-gray-400">
                                    <span class="material-symbols-outlined text-[18px] text-primary">call</span>
                                    {{ $teacher->phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details -->
                <div class="lg:col-span-1 flex flex-col gap-8">
                    <!-- Personal Info Card -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                        <div class="p-5 border-b border-border-light dark:border-border-dark bg-background-light/50 dark:bg-black/10">
                            <h2 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">person</span>
                                General Information
                            </h2>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Father's Name</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5">{{ $teacher->father_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Mother's Name</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5">{{ $teacher->mother_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Date of Birth</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5">{{ $teacher->dob ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Gender</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5">{{ App\Models\Teacher::GENDER_SELECT[$teacher->gender] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Joining Date</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5">{{ $teacher->joining_date ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Address</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5 leading-relaxed">{{ $teacher->address ?? 'No address provided' }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary dark:text-gray-500 uppercase tracking-wider">Linked User Account</label>
                                <p class="text-sm text-text-main dark:text-white mt-0.5 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-text-secondary">account_circle</span>
                                    {{ $teacher->user->name ?? 'None linked' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Info Card -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                        <div class="p-5 border-b border-border-light dark:border-border-dark bg-background-light/50 dark:bg-black/10">
                            <h2 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">payments</span>
                                Remuneration
                            </h2>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div class="flex justify-between items-center py-2 border-b border-border-light dark:border-border-dark/50 last:border-0">
                                <span class="text-sm text-text-secondary dark:text-gray-400">Salary Type</span>
                                <span class="text-sm font-medium text-text-main dark:text-white">{{ App\Models\Teacher::SALARY_TYPE_SELECT[$teacher->salary_type] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border-light dark:border-border-dark/50 last:border-0">
                                <span class="text-sm text-text-secondary dark:text-gray-400">Base Amount</span>
                                <span class="text-lg font-bold text-primary">${{ number_format($teacher->salary_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Qualifications Card -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                        <div class="p-5 border-b border-border-light dark:border-border-dark bg-background-light/50 dark:bg-black/10">
                            <h2 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">school</span>
                                Educational Qualifications
                            </h2>
                        </div>
                        <div class="p-5">
                            @forelse($teacher->qualifications as $qual)
                                <div class="mb-4 pb-4 border-b border-border-light dark:border-border-dark/50 last:border-0 last:mb-0 last:pb-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-text-main dark:text-white">{{ $qual->university }}</p>
                                            <p class="text-xs text-text-secondary dark:text-gray-400 mt-0.5">{{ $qual->department }}</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded-full uppercase tracking-wider">{{ $qual->session }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-text-secondary dark:text-gray-500 italic text-center w-full py-4">No qualifications recorded</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Subjects Card -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                        <div class="p-5 border-b border-border-light dark:border-border-dark bg-background-light/50 dark:bg-black/10">
                            <h2 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">menu_book</span>
                                Assigned Subjects
                            </h2>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                @forelse($teacher->subjects as $subject)
                                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full border border-primary/20">
                                        {{ $subject->name }}
                                    </span>
                                @empty
                                    <p class="text-sm text-text-secondary dark:text-gray-500 italic text-center w-full py-4">No subjects assigned yet</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Related Data Tabs -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200 min-h-[500px]">
                        <!-- Custom Tab Header -->
                        <div class="flex border-b border-border-light dark:border-border-dark bg-background-light/30 dark:bg-black/5">
                            <button onclick="switchTab('expenses')" id="tab-expenses-btn" class="tab-btn active px-8 py-4 text-sm font-bold uppercase tracking-wider border-b-2 border-primary text-primary transition-all">
                                Expenses / Salary History
                            </button>
                            <button onclick="switchTab('payments')" id="tab-payments-btn" class="tab-btn px-8 py-4 text-sm font-bold uppercase tracking-wider border-b-2 border-transparent text-text-secondary hover:text-text-main transition-all">
                                Payments Record
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6">
                            <div id="tab-expenses" class="tab-content-item">
                                @includeIf('admin.teachers.relationships.teacherExpenses', ['expenses' => $teacher->teacherExpenses])
                            </div>
                            <div id="tab-payments" class="tab-content-item hidden">
                                @includeIf('admin.teachers.relationships.teacherTeachersPayments', ['teachersPayments' => $teacher->teacherTeachersPayments])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function switchTab(tabName) {
        // Toggle Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary', 'active');
            btn.classList.add('border-transparent', 'text-text-secondary');
        });
        
        const activeBtn = document.getElementById(`tab-${tabName}-btn`);
        activeBtn.classList.remove('border-transparent', 'text-text-secondary');
        activeBtn.classList.add('border-primary', 'text-primary', 'active');

        // Toggle Content
        document.querySelectorAll('.tab-content-item').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    }
</script>
<style>
    .tab-btn.active {
        background: linear-gradient(to top, var(--primary-color-10, rgba(var(--primary-rgb), 0.05)), transparent);
    }
</style>
@endsection