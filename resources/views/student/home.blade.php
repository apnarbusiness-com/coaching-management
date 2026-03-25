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
            <!-- Payment Spotlight -->
            <div
                class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 p-6 shadow-sm dark:border-emerald-900/40 dark:from-emerald-900/30 dark:via-teal-900/20 dark:to-cyan-900/20">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-600/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Payment Status
                        </div>
                        @if ($latestPayment)
                            <h2 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">
                                Payment Successful
                            </h2>
                            <p class="mt-1 text-slate-600 dark:text-slate-300 text-sm">
                                Your latest payment has been recorded.
                            </p>
                        @else
                            <h2 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">
                                No Payments Found
                            </h2>
                            <p class="mt-1 text-slate-600 dark:text-slate-300 text-sm">
                                Payments linked to your account will appear here.
                            </p>
                        @endif
                    </div>
                    @if ($latestPayment)
                        <div class="rounded-xl bg-white/80 p-4 shadow-sm dark:bg-slate-900/70">
                            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Latest Amount</p>
                            <p class="text-2xl font-extrabold text-emerald-600">
                                {{ number_format($latestPayment->amount ?? 0, 2) }} BDT
                            </p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ $latestPayment->earning_category->name ?? 'Category' }}
                            </p>
                        </div>
                    @endif
                </div>
                @if ($latestPayment)
                    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                                {{ $latestPayment->earning_date ?? '—' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Receipt</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                                {{ $latestPayment->earning_reference ?? '—' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Title</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                                {{ $latestPayment->title ?? '—' }}
                            </p>
                        </div>
                    </div>
                @endif
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
                            href="{{ route('admin.student.profile') }}">
                            <span class="material-symbols-outlined text-primary mb-2">person</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Profile</p>
                        </a>
                        <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                            href="{{ route('admin.student.myBatches') }}">
                            <span class="material-symbols-outlined text-primary mb-2">layers</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">My Batches</p>
                        </a>
                        <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                            href="#">
                            <span class="material-symbols-outlined text-primary mb-2">fact_check</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Attendance</p>
                        </a>
                    </div>

                    <!-- Active Batches -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-xl">school</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100">My Active Batches</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $myBatches->count() }} batch(es) enrolled</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($myBatches->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($myBatches as $batch)
                                        @php
                                            $schedule = $batch->formatted_schedule;
                                            $subjectNames = $batch->subjects->pluck('name')->filter()->unique()->implode(', ');
                                            $teacherNames = $batch->teachers->pluck('name')->implode(', ');
                                            $className = $batch->class->class_name ?? '';
                                            
                                            $dayColors = [
                                                'saturday'  => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-600 dark:text-rose-400', 'border' => 'border-rose-200 dark:border-rose-800'],
                                                'sunday'    => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-600 dark:text-orange-400', 'border' => 'border-orange-200 dark:border-orange-800'],
                                                'monday'    => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-800'],
                                                'tuesday'   => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-600 dark:text-yellow-400', 'border' => 'border-yellow-200 dark:border-yellow-800'],
                                                'wednesday' => ['bg' => 'bg-lime-100 dark:bg-lime-900/30', 'text' => 'text-lime-600 dark:text-lime-400', 'border' => 'border-lime-200 dark:border-lime-800'],
                                                'thursday'  => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400', 'border' => 'border-green-200 dark:border-green-800'],
                                                'friday'    => ['bg' => 'bg-teal-100 dark:bg-teal-900/30', 'text' => 'text-teal-600 dark:text-teal-400', 'border' => 'border-teal-200 dark:border-teal-800'],
                                            ];
                                        @endphp
                                        <div class="group relative bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                                            <!-- Batch Header -->
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                        {{ $batch->batch_name }}
                                                    </h5>
                                                    @if($subjectNames)
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                                            {{ $subjectNames }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-indigo-500 text-lg">groups</span>
                                                </span>
                                            </div>
                                            
                                            <!-- Class Info -->
                                            @if($className)
                                                <div class="flex items-center gap-2 mb-4">
                                                    <span class="material-symbols-outlined text-slate-400 text-sm">meeting_room</span>
                                                    <span class="text-xs text-slate-600 dark:text-slate-300">{{ $className }}</span>
                                                </div>
                                            @endif
                                            
                                            <!-- Schedule Section -->
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    <span class="material-symbols-outlined text-sm">calendar_month</span>
                                                    <span>Class Schedule</span>
                                                    <span class="ml-auto text-indigo-600 dark:text-indigo-400 font-bold">{{ count($schedule) }} day(s)</span>
                                                </div>
                                                
                                                @if(!empty($schedule))
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($schedule as $dayKey => $info)
                                                            <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg {{ $dayColors[$dayKey]['bg'] ?? 'bg-slate-100 dark:bg-slate-800' }} border {{ $dayColors[$dayKey]['border'] ?? 'border-slate-200 dark:border-slate-700' }}">
                                                                <span class="text-[11px] font-semibold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-700 dark:text-slate-300' }}">
                                                                    {{ substr($info['day'], 0, 3) }}
                                                                </span>
                                                                <span class="text-[11px] font-bold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-900 dark:text-white' }}">
                                                                    {{ $info['time'] }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-slate-400 dark:text-slate-500 italic">No schedule set</p>
                                                @endif
                                            </div>
                                            
                                            <!-- Teacher -->
                                            @if($teacherNames)
                                                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                                        <span class="text-white text-[10px] font-bold">{{ substr($teacherNames, 0, 1) }}</span>
                                                    </div>
                                                    <span class="text-xs text-slate-600 dark:text-slate-300 truncate">{{ $teacherNames }}</span>
                                                </div>
                                            @endif
                                            
                                            <!-- Action -->
                                            <a href="{{ route('admin.batches.show', $batch->id) }}" class="absolute inset-0 z-10">
                                                <span class="sr-only">View {{ $batch->batch_name }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl text-slate-400">school</span>
                                    </div>
                                    <h5 class="font-semibold text-slate-900 dark:text-white">No Batches Enrolled</h5>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">You haven't enrolled in any batch yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-6">

                    <!-- Due Balance Summary -->
                    <div class="bg-slate-900 text-white rounded-xl p-6 relative overflow-hidden">
                        <div class="relative z-10">
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Dues</p>
                            <h3 class="text-3xl font-bold">{{ number_format($dueInfo['total_due'] ?? 0, 2) }} BDT</h3>
                            <p class="text-slate-400 text-xs mt-4 cursor-pointer hover:underline" onclick="openDueModal()">
                                @if(($dueInfo['unpaid_months'] ?? 0) > 0)
                                    {{ $dueInfo['unpaid_months'] }} month(s) pending
                                @else
                                    All dues paid!
                                @endif
                            </p>
                            @if(($dueInfo['total_due'] ?? 0) > 0)
                            <button
                                onclick="openDueModal()"
                                class="mt-6 w-full py-2.5 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors">Pay
                                Now</button>
                            @else
                            <button
                                class="mt-6 w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition-colors">Up to Date</button>
                            @endif
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
                            @if(($dueInfo['total_due'] ?? 0) > 0)
                            <div
                                class="flex gap-3 items-start p-3 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
                                <span class="material-symbols-outlined text-red-500 text-xl">warning</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                        Payment Due Alert</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                                        You have {{ number_format($dueInfo['total_due'], 2) }} BDT due for {{ $dueInfo['unpaid_months'] }} month(s). Please pay soon.</p>
                                </div>
                            </div>
                            @endif
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
            <!-- Payment History -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-slate-100">Payment History</h4>
                        <p class="text-xs text-slate-500 mt-1">Showing latest {{ $paymentHistory->count() }} records</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($paymentHistory as $payment)
                                <tr class="text-sm text-slate-900 dark:text-slate-100">
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                        {{ \Carbon\Carbon::parse($payment->earning_date ?? '')->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $payment->earning_category->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $payment->title ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-600">
                                        {{ number_format($payment->amount ?? 0, 2) }} BDT
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                        {{ $payment->earning_reference ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-6 text-sm text-slate-500 dark:text-slate-400" colspan="5">
                                        No payment records found yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Due Details Modal -->
    <div id="dueModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Due Details</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Your pending payment breakdown</p>
                </div>
                <button onclick="closeDueModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-slate-500">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                @if($unpaidDues->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($unpaidDues as $due)
                            <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 {{ $due->status === 'partial' ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200' : 'bg-red-50/50 dark:bg-red-900/10' }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-900 dark:text-white">
                                            {{ \Carbon\Carbon::createFromDate(null, $due->month, 1)->format('F') }} {{ $due->year }}
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $due->batch->batch_name ?? 'N/A' }}
                                            @if($due->academicClass)
                                                <span class="mx-1">•</span>
                                                {{ $due->academicClass->class_name }}
                                            @endif
                                        </p>
                                    </div>
                                    @if($due->status === 'partial')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">Partial</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300">Unpaid</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">Due Amount</p>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ number_format($due->due_amount, 2) }} BDT</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">Paid</p>
                                        <p class="font-bold text-emerald-600">{{ number_format($due->paid_amount, 2) }} BDT</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">Remaining</p>
                                        <p class="font-bold text-red-600">{{ number_format($due->due_remaining, 2) }} BDT</p>
                                    </div>
                                </div>
                                @if($due->status === 'partial')
                                    <div class="mt-3 pt-3 border-t border-amber-200 dark:border-amber-800">
                                        <div class="flex items-center gap-2 text-xs text-amber-700 dark:text-amber-300">
                                            <span class="material-symbols-outlined text-sm">info</span>
                                            Partial payment received. Complete your payment to clear this month.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 p-4 bg-slate-900 text-white rounded-xl">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300 font-medium">Total Outstanding</span>
                            <span class="text-2xl font-bold">{{ number_format($dueInfo['total_due'] ?? 0, 2) }} BDT</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-6xl text-emerald-500">check_circle</span>
                        <h4 class="mt-4 font-bold text-slate-900 dark:text-white">All Clear!</h4>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">You have no pending dues.</p>
                    </div>
                @endif
            </div>
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center">
                    For any payment related queries, please contact the administration.
                </p>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {});

        function openDueModal() {
            const modal = document.getElementById('dueModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDueModal() {
            const modal = document.getElementById('dueModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.getElementById('dueModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDueModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDueModal();
            }
        });
    </script>
@endsection
