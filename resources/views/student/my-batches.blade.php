@extends('layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark">
        <div class="max-w-7xl mx-auto p-6 md:p-8 space-y-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">My Batches</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">View and manage your enrolled batches</p>
                </div>
                <a href="{{ route('admin.home') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Back to Dashboard
                </a>
            </div>

            @php
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

            @if($batches->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($batches as $batch)
                        @php
                            $schedule = $batch->formatted_schedule;
                            $subjectNames = $batch->subjects->pluck('name')->filter()->unique()->implode(', ');
                            $teacherNames = $batch->teachers->pluck('name')->implode(', ');
                            $className = $batch->class->class_name ?? '';
                            $feeType = \App\Models\Batch::FEE_TYPE_SELECT[$batch->fee_type] ?? '';
                        @endphp
                        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden">
                            <!-- Gradient Accent -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                            
                            <div class="p-6 pt-8">
                                <!-- Batch Header -->
                                <div class="flex items-start justify-between mb-5">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold uppercase rounded">
                                                Batch
                                            </span>
                                        </div>
                                        <h5 class="text-lg font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $batch->batch_name }}
                                        </h5>
                                        @if($subjectNames)
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                                <span class="material-symbols-outlined text-xs align-middle">menu_book</span>
                                                {{ $subjectNames }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Class & Fee Info -->
                                <div class="flex flex-wrap gap-3 mb-5">
                                    @if($className)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg">
                                            <span class="material-symbols-outlined text-slate-500 text-sm">meeting_room</span>
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $className }}</span>
                                        </div>
                                    @endif
                                    @if($feeType)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                                            <span class="material-symbols-outlined text-emerald-500 text-sm">payments</span>
                                            <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">{{ number_format($batch->fee_amount, 2) }} BDT</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Schedule Section -->
                                <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800 rounded-xl p-4 mb-5">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="material-symbols-outlined text-indigo-500 text-lg">calendar_month</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Class Schedule</span>
                                        @if(!empty($schedule))
                                            <span class="ml-auto px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold rounded-full">
                                                {{ count($schedule) }} day(s)
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if(!empty($schedule))
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($schedule as $dayKey => $info)
                                                <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg {{ $dayColors[$dayKey]['bg'] ?? 'bg-slate-100 dark:bg-slate-800' }} border {{ $dayColors[$dayKey]['border'] ?? 'border-slate-200 dark:border-slate-700' }}">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-semibold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-600 dark:text-slate-400' }} uppercase">
                                                            {{ substr($info['day'], 0, 3) }}
                                                        </span>
                                                        <span class="text-sm font-bold {{ $dayColors[$dayKey]['text'] ?? 'text-slate-900 dark:text-white' }}">
                                                            {{ $info['time'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400 dark:text-slate-500 italic text-center py-2">No schedule set</p>
                                    @endif
                                </div>

                                <!-- Teacher -->
                                @if($teacherNames)
                                    <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg">
                                            <span class="text-white text-sm font-bold">{{ substr($teacherNames, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">Instructor</p>
                                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $teacherNames }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Footer -->
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700">
                                <a href="{{ route('admin.batches.show', $batch->id) }}" class="flex items-center justify-center gap-2 w-full py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-300">
                                    <span>View Details</span>
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </a>
                            </div>

                            <!-- Clickable Overlay -->
                            <a href="{{ route('admin.batches.show', $batch->id) }}" class="absolute inset-0 z-10">
                                <span class="sr-only">View {{ $batch->batch_name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-5xl text-slate-400">school</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Batches Enrolled</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                        You haven't enrolled in any batch yet. Contact the administration to get enrolled in a batch.
                    </p>
                    <a href="{{ route('admin.home') }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-lg">home</span>
                        Go to Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
