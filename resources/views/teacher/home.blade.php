@extends('layouts.admin')
@section('content')
    <main class="flex-1 overflow-y-auto flex flex-col">
        <div class="p-8 space-y-8">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50">
                    Welcome back, <span class="text-primary">{{ $teacher->name ?? 'Teacher' }}</span> 👋</h1>
                <p class="text-slate-500 mt-1">Here is a quick look at your teaching schedule and payments.</p>
            </div>

            @if($teacher)
            <!-- Quick Links -->
            <div class="grid grid-cols-4 gap-4">
                <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                    href="{{ route('admin.teacher.profile') }}">
                    <span class="material-symbols-outlined text-primary mb-2">person</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Profile</p>
                </a>
                <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                    href="{{ route('admin.teacher.myIdCard') }}" target="_blank">
                    <span class="material-symbols-outlined text-primary mb-2">badge</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Get ID Card</p>
                </a>
                <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                    href="#">
                    <span class="material-symbols-outlined text-primary mb-2">calendar_month</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Schedule</p>
                </a>
                <a class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-colors group"
                    href="#">
                    <span class="material-symbols-outlined text-primary mb-2">payments</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Payments</p>
                </a>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- My Batches (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Active Batches -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-xl">school</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100">My Teaching Batches</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $myBatches->count() }} batch(es) assigned</p>
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
                                            $studentCount = $batch->students->count();
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

                                            <!-- Student Count -->
                                            <div class="flex items-center gap-2 mb-4">
                                                <span class="material-symbols-outlined text-slate-400 text-sm">person</span>
                                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ $studentCount }} Students</span>
                                            </div>
                                            
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
                                    <h5 class="font-semibold text-slate-900 dark:text-white">No Batches Assigned</h5>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">You haven't been assigned to any batch yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Teacher Info Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-4">My Information</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">badge</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Employee Code</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $teacher->emloyee_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">email</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Email</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $teacher->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">call</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Phone</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $teacher->phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">event</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Joining Date</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $teacher->joining_date ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ route('admin.teacher.myIdCard') }}" target="_blank"
                                class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">
                                <span class="material-symbols-outlined text-sm">badge</span>
                                View ID Card
                            </a>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100">Payment History</h4>
                            <p class="text-xs text-slate-500 mt-1">Showing latest {{ $paymentHistory->count() }} records</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($paymentHistory as $payment)
                                        <tr class="text-sm text-slate-900 dark:text-slate-100">
                                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($payment->payment_date ?? '')->format('d-m-Y') }}
                                            </td>
                                            <td class="px-4 py-3 font-bold text-emerald-600">
                                                {{ number_format($payment->amount ?? 0, 2) }} BDT
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400" colspan="2">
                                                No payment records found yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- No Teacher Profile -->
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
