@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route('admin.dashboard-widgets.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="flex justify-between items-center mb-6">
            <div>
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Configure Widgets for: {{ $role->title }}
                </h4>
                <p class="text-sm text-slate-500 dark:text-[#9da6b9]">Select which widgets this role can see on their
                    dashboard</p>
            </div>
            <button type="submit"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Save Configuration
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($allWidgets as $key => $widget)
                <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="widgets[{{ $key }}]" id="widget_{{ $key }}"
                            
                                value="1" {{ $widgets[$key]['is_visible'] ?? false ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary">
                            <label for="widget_{{ $key }}" class="cursor-pointer">
                                <span class="font-medium text-slate-900 dark:text-white">{{ $widget['label'] }}</span>
                            </label>
                        </div>
                        {{-- @if ($widgets[$key]['has_db_config'] ?? false)
                            <span class="text-xs text-green-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                Custom
                            </span>
                        @endif --}}
                    </div>
                    <p class="text-xs text-slate-500 dark:text-[#9da6b9] mt-2 ml-7">
                        Default: {{ $widget['default_visible'] ? 'Visible' : 'Hidden' }}
                    </p>
                </div>
            @endforeach
        </div>
    </form>
@endsection
