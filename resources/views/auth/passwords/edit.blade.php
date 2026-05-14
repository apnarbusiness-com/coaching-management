@extends('layouts.admin')
@section('title', 'Change Password')
@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ trans('global.my_profile') }}</h2>
            <p class="text-slate-500 dark:text-slate-400">Manage your account settings and change your password.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profile Information Card -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all hover:shadow-md">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">person</span>
                    <h3 class="font-semibold text-slate-800 dark:text-white">{{ trans('global.my_profile') }}</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.password.updateProfile') }}" class="space-y-4">
                        @csrf
                        <div class="form-group">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 required"
                                for="name">{{ trans('cruds.user.fields.name') }}</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 material-symbols-outlined"
                                    style="font-size: 20px;">badge</span>
                                <input
                                    class="form-control w-full h-11 pl-10 pr-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-all {{ $errors->has('name') ? 'border-red-500' : '' }}"
                                    type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                                    required>
                            </div>
                            @if ($errors->has('name'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('name') }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 required"
                                for="email">{{ trans('cruds.user.fields.email') }}</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 material-symbols-outlined"
                                    style="font-size: 20px;">mail</span>
                                <input
                                    class="form-control w-full h-11 pl-10 pr-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-all {{ $errors->has('email') ? 'border-red-500' : '' }}"
                                    type="text" name="email" id="email" value="{{ old('email', auth()->user()->email) }}"
                                    required>
                            </div>
                            @if ($errors->has('email'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <div class="pt-2">
                            <button
                                class="w-full h-10 bg-primary hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm shadow-blue-500/20 transition-all flex items-center justify-center gap-2"
                                type="submit">
                                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all hover:shadow-md">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">lock</span>
                    <h3 class="font-semibold text-slate-800 dark:text-white">{{ trans('global.change_password') }}</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                        @csrf
                        <div class="form-group">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 required"
                                for="password">New {{ trans('cruds.user.fields.password') }}</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 material-symbols-outlined"
                                    style="font-size: 20px;">key</span>
                                <input
                                    class="form-control w-full h-11 pl-10 pr-12 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-all {{ $errors->has('password') ? 'border-red-500' : '' }}"
                                    type="password" name="password" id="password" required>
                                <button
                                    class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 hover:text-primary dark:hover:text-blue-400 transition-colors"
                                    id="toggleNewPassword" type="button">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 required"
                                for="password_confirmation">Repeat New {{ trans('cruds.user.fields.password') }}</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 material-symbols-outlined"
                                    style="font-size: 20px;">lock_reset</span>
                                <input
                                    class="form-control w-full h-11 pl-10 pr-12 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-all"
                                    type="password" name="password_confirmation" id="password_confirmation" required>
                                <button
                                    class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 hover:text-primary dark:hover:text-blue-400 transition-colors"
                                    id="toggleRepeatPassword" type="button">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button
                                class="w-full h-10 bg-primary hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm shadow-blue-500/20 transition-all flex items-center justify-center gap-2"
                                type="submit">
                                <span class="material-symbols-outlined" style="font-size: 18px;">sync</span>
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-8">
            <div class="bg-red-50/50 dark:bg-red-950/10 border border-red-200 dark:border-red-900/50 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-lg">
                        <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-800 dark:text-red-400 mb-1">
                            {{ trans('global.delete_account') }}
                        </h3>
                        <p class="text-red-700/80 dark:text-red-500/70 text-sm mb-4">Once you delete your account, there is
                            no going back. Please be certain.</p>

                        {{--
                        <form method="POST" action="{{ route('profile.password.destroyProfile') }}"
                            onsubmit="return prompt('{{ __('global.delete_account_warning') }}') == '{{ auth()->user()->email }}'">
                            @csrf
                            <button
                                class="h-10 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm shadow-red-500/20 transition-all flex items-center gap-2"
                                type="submit">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete_forever</span>
                                {{ trans('global.delete') }}
                            </button>
                        </form>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function setupToggle(buttonId, inputId) {
            const btn = document.getElementById(buttonId);
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');

            btn.addEventListener('click', () => {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    icon.textContent = 'visibility';
                }
            });
        }

        setupToggle('toggleNewPassword', 'password');
        setupToggle('toggleRepeatPassword', 'password_confirmation');
    </script>
@endsection