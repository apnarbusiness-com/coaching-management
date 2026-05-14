@extends('layouts.admin')
@section('title', 'Users — Details')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            @if(session('message'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400" role="alert">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif
            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="flex">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white"
                            href="{{ route('admin.home') }}">
                            <span class="material-symbols-outlined text-[18px] mr-2">home</span>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <a class="ml-1 text-sm font-medium text-slate-500 hover:text-primary md:ml-2 dark:text-slate-400 dark:hover:text-white"
                                href="{{ route('admin.users.index') }}">Users</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">User
                                Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-4xl">account_circle</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $user->name }}
                        </h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400 font-medium">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('admin.users.sendCredentials', $user->id) }}" method="POST" onsubmit="return confirm('This will reset the user\'s password and send the new credentials to their email. Are you sure?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <span class="material-symbols-outlined text-[20px] mr-2">email</span>
                            Send Credentials
                        </button>
                    </form>
                    <a href="{{ route('admin.users.edit', $user->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                        Edit User
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">info</span>
                            <h3 class="text-lg font-bold">Account Details</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-6">
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Full
                                    Name</label>
                                <p class="mt-1 text-lg font-semibold">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email
                                    Address</label>
                                <p class="mt-1 text-lg font-semibold">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email
                                    Verified At</label>
                                <p class="mt-1 text-lg font-semibold">{{ $user->email_verified_at ?? 'Not Verified' }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Roles</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                                            {{ $role->title }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linked Profile Card -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-white">
                        <div
                            class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined font-bold">link</span>
                            <h3 class="text-lg font-bold">Linked Identity</h3>
                        </div>
                        <div class="p-8">
                            @if($user->teacher)
                                <div
                                    class="flex items-start gap-6 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-xl border border-dashed border-slate-300 dark:border-slate-600">
                                    <div
                                        class="h-20 w-20 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                                        <span class="material-symbols-outlined text-4xl font-bold">person_4</span>
                                    </div>
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 rounded bg-blue-600 text-[10px] font-bold text-white uppercase tracking-widest">Teacher</span>
                                            <h4 class="text-xl font-bold tracking-tight">{{ $user->teacher->name }}</h4>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Employee
                                                    Code</label>
                                                <p
                                                    class="font-mono text-blue-600 dark:text-blue-400 font-bold uppercase leading-tight">
                                                    {{ $user->teacher->emloyee_code }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Contact</label>
                                                <p class="leading-tight font-medium">{{ $user->teacher->phone }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.teachers.show', $user->teacher->id) }}"
                                            class="inline-flex items-center text-sm font-bold text-primary hover:underline">
                                            View Teacher Profile <span
                                                class="material-symbols-outlined text-[16px] ml-1">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            @elseif($user->student)
                                <div
                                    class="flex items-start gap-6 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-xl border border-dashed border-slate-300 dark:border-slate-600">
                                    <div
                                        class="h-20 w-20 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                                        <span class="material-symbols-outlined text-4xl font-bold">school</span>
                                    </div>
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 rounded bg-green-600 text-[10px] font-bold text-white uppercase tracking-widest">Student</span>
                                            <h4 class="text-xl font-bold tracking-tight">{{ $user->student->first_name }}
                                                {{ $user->student->last_name }}</h4>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Student
                                                    ID</label>
                                                <p
                                                    class="font-mono text-green-600 dark:text-green-400 font-bold uppercase leading-tight">
                                                    {{ $user->student->id_no }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Roll</label>
                                                <p class="leading-tight font-medium">{{ $user->student->roll }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Contact</label>
                                                <p class="leading-tight font-medium">{{ $user->student->contact_number }}</p>
                                            </div>
                                        </div>
                                        <div
                                            class="grid grid-cols-3 gap-4 border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Class</label>
                                                <p class="leading-tight font-bold">
                                                    {{ $user->student->class->class_name ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Section</label>
                                                <p class="leading-tight font-bold">
                                                    {{ $user->student->section->section_name ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Shift</label>
                                                <p class="leading-tight font-bold">
                                                    {{ $user->student->shift->shift_name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.student-basic-infos.show', $user->student->id) }}"
                                            class="inline-flex items-center text-sm font-bold text-primary hover:underline">
                                            View Student Profile <span
                                                class="material-symbols-outlined text-[16px] ml-1">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="flex flex-col items-center justify-center p-12 text-center bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                                    <span
                                        class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-6xl mb-4">no_accounts</span>
                                    <h4 class="text-xl font-bold text-slate-500">Standalone User</h4>
                                    <p class="text-slate-400 mt-2 max-w-sm">This user account is not currently linked to any
                                        Teacher or Student profile in the system.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection