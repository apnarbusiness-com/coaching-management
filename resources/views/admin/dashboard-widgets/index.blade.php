@extends('layouts.admin')
@section('content')
    <div class="card bg-white dark:bg-slate-800 dark:text-white transition-colors duration-300">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-semibold">Dashboard Widget Configuration</h4>
                <span class="text-sm text-slate-500 dark:text-[#9da6b9]">Configure which widgets each role can see on their
                    dashboard</span>
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($roles as $role)
                    <div
                        class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <span class="material-symbols-outlined text-primary">group</span>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-slate-900 dark:text-white">{{ $role->title }}</h5>
                                    {{-- <span class="text-xs text-slate-500 dark:text-[#9da6b9]">{{ $role->users_count ?? 0 }} users</span> --}}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.dashboard-widgets.edit', $role->id) }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <span class="material-symbols-outlined text-sm">settings</span>
                            Configure Widgets
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
