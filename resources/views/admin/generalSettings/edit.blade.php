@extends('layouts.admin')
@section('title', 'General Settings')
@section('content')
<div class="mx-auto max-w-5xl px-6 py-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.home') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-slate-800 dark:text-white font-medium">General Settings</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">General Settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your site branding and institute information</p>
    </div>

    <form method="POST" action="{{ route('admin.general-settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Logo & Favicon Upload Cards --}}
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Logo Card --}}
            <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                <label class="mb-4 block text-sm font-semibold text-slate-700 dark:text-slate-300">Site Logo</label>
                <div class="upload-zone relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 p-8 transition-all hover:border-primary hover:bg-primary/5 cursor-pointer"
                     onclick="document.getElementById('logo-input').click()">
                    <input type="file" id="logo-input" name="site_logo" accept="image/jpg,image/jpeg,image/png,image/svg+xml,image/webp" class="hidden" onchange="previewLogo(event)">
                    <div id="logo-preview-container" class="flex flex-col items-center gap-3">
                        @php
                            $logo = setting('site_logo');
                            $logoUrl = $logo ? asset('uploads/settings/' . $logo) : asset('assets/images/logo_for_menu.svg');
                        @endphp
                        <img id="logo-preview" src="{{ $logoUrl }}"
                             alt="Site Logo" class="h-20 object-contain rounded-lg">
                        <p id="logo-placeholder" class="text-sm text-slate-400 dark:text-slate-500 {{ $logo ? 'hidden' : '' }}">
                            <span class="block text-center">
                                <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                <span class="block mt-1">Click to upload logo</span>
                                <span class="block text-xs mt-0.5">JPG, PNG, SVG, WebP (max 2MB)</span>
                            </span>
                        </p>
                    </div>
                </div>
                @error('site_logo')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
                @if ($logo)
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-slate-400 truncate max-w-[200px]">{{ $logo }}</span>
                        <label class="flex items-center gap-1.5 cursor-pointer text-xs text-red-500 hover:text-red-700 transition-colors">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-red-500 focus:ring-red-500">
                            Remove
                        </label>
                    </div>
                @endif
            </div>

            {{-- Favicon Card --}}
            <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                <label class="mb-4 block text-sm font-semibold text-slate-700 dark:text-slate-300">Favicon</label>
                <div class="upload-zone relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 p-8 transition-all hover:border-primary hover:bg-primary/5 cursor-pointer"
                     onclick="document.getElementById('favicon-input').click()">
                    <input type="file" id="favicon-input" name="site_favicon" accept="image/jpg,image/jpeg,image/png,image/svg+xml,image/webp,image/x-icon" class="hidden" onchange="previewFavicon(event)">
                    <div id="favicon-preview-container" class="flex flex-col items-center gap-3">
                        @php
                            $favicon = setting('site_favicon');
                            $faviconUrl = $favicon ? asset('uploads/settings/' . $favicon) : asset('assets/images/logo.svg');
                        @endphp
                        <img id="favicon-preview" src="{{ $faviconUrl }}"
                             alt="Favicon" class="h-16 w-16 object-contain rounded-lg">
                        <p id="favicon-placeholder" class="text-sm text-slate-400 dark:text-slate-500 {{ $favicon ? 'hidden' : '' }}">
                            <span class="block text-center">
                                <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                <span class="block mt-1">Click to upload favicon</span>
                                <span class="block text-xs mt-0.5">JPG, PNG, SVG, ICO (max 1MB)</span>
                            </span>
                        </p>
                    </div>
                </div>
                @error('site_favicon')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
                @if ($favicon)
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-slate-400 truncate max-w-[200px]">{{ $favicon }}</span>
                        <label class="flex items-center gap-1.5 cursor-pointer text-xs text-red-500 hover:text-red-700 transition-colors">
                            <input type="checkbox" name="remove_favicon" value="1" class="rounded border-slate-300 text-red-500 focus:ring-red-500">
                            Remove
                        </label>
                    </div>
                @endif
            </div>
        </div>

        {{-- Text Fields --}}
        <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
            <h2 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">Institute Information</h2>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="site_title">
                        Site Title
                    </label>
                    <input type="text" id="site_title" name="site_title"
                           value="{{ old('site_title', $settings['site_title'] ?? 'Excellency') }}"
                           class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                           placeholder="Enter site title">
                    @error('site_title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="institute_name">
                        Institute Name
                    </label>
                    <input type="text" id="institute_name" name="institute_name"
                           value="{{ old('institute_name', $settings['institute_name'] ?? '') }}"
                           class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                           placeholder="Enter institute name">
                    @error('institute_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="institute_phone">
                        Institute Phone
                    </label>
                    <input type="text" id="institute_phone" name="institute_phone"
                           value="{{ old('institute_phone', $settings['institute_phone'] ?? '') }}"
                           class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                           placeholder="Enter institute phone">
                    @error('institute_phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="institute_address">
                        Institute Address
                    </label>
                    <textarea id="institute_address" name="institute_address" rows="2"
                              class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                              placeholder="Enter institute address">{{ old('institute_address', $settings['institute_address'] ?? '') }}</textarea>
                    @error('institute_address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex items-center justify-end gap-4">
            <a href="{{ route('admin.home') }}"
               class="rounded-lg border border-slate-300 bg-white px-6 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-hover transition-colors">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .upload-zone:hover {
        border-color: #137fec;
    }
    .upload-zone.dragover {
        border-color: #137fec;
        background-color: rgba(19, 127, 236, 0.05);
    }
</style>
@endpush

@section('scripts')
<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').src = e.target.result;
                document.getElementById('logo-preview').classList.remove('hidden');
                document.getElementById('logo-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function previewFavicon(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('favicon-preview').src = e.target.result;
                document.getElementById('favicon-preview').classList.remove('hidden');
                document.getElementById('favicon-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    // Drag & drop support
    document.querySelectorAll('.upload-zone').forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const input = this.querySelector('input[type="file"]');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endsection
