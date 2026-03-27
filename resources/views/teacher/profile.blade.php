@extends('layouts.admin')
@section('content')
    <main class="flex-1 overflow-y-auto flex flex-col">
        <div class="p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Teacher Profile</h1>
                <p class="text-slate-500 mt-1">View your personal information and details.</p>
            </div>

            @if($teacher)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            @if($teacher->profile_img)
                                <img src="{{ $teacher->profile_img->getUrl('preview') }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-slate-400">
                                    {{ substr($teacher->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ $teacher->name }}</h2>
                            <p class="text-slate-500">{{ $teacher->emloyee_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-slate-50 mb-4">Personal Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Phone</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gender</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->gender ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Joining Date</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->joining_date ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-slate-50 mb-4">Other Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Address</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->address ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Salary Type</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ $teacher->salary_type ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Salary Amount</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ number_format($teacher->salary_amount ?? 0, 2) }} BDT</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        {{ $teacher->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($teacher->subjects->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-slate-50 mb-4">Subjects</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($teacher->subjects as $subject)
                                <span class="px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full">
                                    {{ $subject->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex gap-4">
                        <a href="{{ route('admin.teacher.myIdCard') }}" target="_blank"
                            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-sm">badge</span>
                            View ID Card
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-slate-900 rounded-xl p-8 border border-slate-200 dark:border-slate-800 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl text-slate-400">person_off</span>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white">Teacher Profile Not Found</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Your user account is not linked to a teacher profile.</p>
            </div>
            @endif
        </div>
    </main>
@endsection
