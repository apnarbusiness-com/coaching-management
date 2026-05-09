@extends('layouts.admin')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-4xl mx-auto flex flex-col gap-6 pb-12">
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
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Edit User</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Edit User</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Update account settings and role permissions.</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.users.update', [$user->id]) }}" enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @method('PUT')
                @csrf

                <div class="p-6 md:p-8 space-y-8">
                    <!-- User Account Details -->
                    <div class="space-y-6">
                        <div
                            class="flex items-center gap-2 text-primary border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <span class="material-symbols-outlined">person</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Account Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="name">{{ trans('cruds.user.fields.name') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('name') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="name" name="name" placeholder="Full Name" type="text"
                                    value="{{ old('name', $user->name) }}" required />
                                @if ($errors->has('name'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.user.fields.name_helper') }}</span>
                            </div>

                            {{-- user_name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="user_name">Username</label>
                                <div class="relative mt-1">
                                    <input
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 shadow-sm sm:text-sm py-2.5 px-3 cursor-not-allowed"
                                        id="user_name" type="text" value="{{ $user->user_name }}" disabled readonly />
                                    <input type="hidden" name="user_name" value="{{ $user->user_name }}" />
                                </div>
                                <span class="text-xs text-slate-400 mt-1 block">Auto-generated, cannot be changed</span>
                            </div>

                            {{-- email --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="email">{{ trans('cruds.user.fields.email') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('email') ? 'border-red-500 ring-red-500' : '' }}"
                                    id="email" name="email" placeholder="Email Address" type="email"
                                    value="{{ old('email', $user->email) }}" required />
                                @if ($errors->has('email'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('email') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.user.fields.email_helper') }}</span>
                            </div>

                            {{-- password --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="password">{{ trans('cruds.user.fields.password') }}</label>
                                <div class="relative mt-1">
                                    <input
                                        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 {{ $errors->has('password') ? 'border-red-500 ring-red-500' : '' }}"
                                        id="password" name="password" placeholder="Leave blank to keep current"
                                        type="password" />
                                    <button type="button"
                                        class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                @if ($errors->has('password'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('password') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.user.fields.password_helper') }}</span>
                            </div>

                            {{-- roles --}}
                            <div class="col-span-1">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                        for="roles">
                                        {{ trans('cruds.user.fields.roles') }}
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            class="select-all text-[10px] font-bold uppercase tracking-wider text-primary hover:text-blue-600">Select
                                            All</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button"
                                            class="deselect-all text-[10px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600">Deselect
                                            All</button>
                                    </div>
                                </div>
                                <select
                                    class="form-control select2 block w-full {{ $errors->has('roles') ? 'is-invalid' : '' }}"
                                    name="roles[]" id="roles" multiple required>
                                    @foreach($roles as $id => $role)
                                        <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('roles'))
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('roles') }}</p>
                                @endif
                                <span
                                    class="text-xs text-slate-400 mt-1 block">{{ trans('cruds.user.fields.roles_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700"
                        type="button" onclick="window.history.back()">
                        Cancel
                    </button>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function () {
                $('.toggle-password').on('click', function () {
                    const input = $(this).siblings('input');
                    const icon = $(this).find('.material-symbols-outlined');

                    if (input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        icon.text('visibility_off');
                    } else {
                        input.attr('type', 'password');
                        icon.text('visibility');
                    }
                });
            });
        </script>
    @endsection
@endsection