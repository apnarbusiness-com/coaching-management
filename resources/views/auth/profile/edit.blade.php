@extends('layouts.admin')
@section('title', 'My Profile')
@section('content')
<div class="mx-auto max-w-3xl px-6 py-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.home') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-slate-800 dark:text-white font-medium">My Profile</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Profile</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update your profile information and photo</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Profile Photo Card --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
            <h2 class="mb-5 text-lg font-semibold text-slate-900 dark:text-white">Profile Photo</h2>
            <div class="flex flex-col items-center gap-4 sm:flex-row">
                <div class="relative group">
                    @php
                        $photoUrl = $user->photo?->preview ?? null;
                        $initial = strtoupper(substr($user->name, 0, 1));
                    @endphp
                    <div id="avatar-wrapper" class="relative h-24 w-24 rounded-full overflow-hidden border-4 border-slate-100 dark:border-slate-700 shadow-sm">
                        @if ($photoUrl)
                            <img id="avatar-preview" src="{{ $photoUrl }}" alt=""
                                 class="h-full w-full object-cover">
                        @else
                            <div id="avatar-placeholder" class="h-full w-full bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center">
                                <span class="text-3xl font-bold text-white">{{ $initial }}</span>
                            </div>
                            <img id="avatar-preview" src="" alt="" class="h-full w-full object-cover hidden">
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity rounded-full"
                             onclick="document.getElementById('photo-input').click()">
                            <span class="material-symbols-outlined text-white text-3xl">camera_alt</span>
                        </div>
                    </div>
                    <input type="file" id="photo-input" name="photo" accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden" onchange="previewPhoto(event)">
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Upload a new photo</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">JPG, PNG or WebP. Max 2MB.</p>
                    @error('photo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Profile Info Card --}}
        <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
            <h2 class="mb-5 text-lg font-semibold text-slate-900 dark:text-white">Personal Information</h2>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                           placeholder="Enter your name">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                           placeholder="Enter your email">
                    @error('email')
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
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
