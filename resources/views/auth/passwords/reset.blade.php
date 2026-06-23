<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Set New Password | {{ setting('site_title') ?: trans('panel.site_title') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <!-- Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Theme Configuration -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#135bec",
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
                        "slate-850": "#151e2e",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 h-screen overflow-hidden flex flex-col font-display antialiased">
    <div class="flex flex-1 h-full w-full">
        <!-- Left Panel: Branding & Inspiration -->
        <div class="relative hidden lg:flex w-1/2 flex-col justify-between overflow-hidden"
            style="background-color: rgb(15 23 42 / 67%);">
            <div class="absolute inset-0 h-full w-full bg-cover bg-center opacity-60 mix-blend-overlay transition-opacity duration-1000"
                style="background-image: url('https://images.unsplash.com/photo-1510070112810-d4e9a46d9e91?q=80&w=2069&auto=format&fit=crop');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-primary/90 to-blue-900/90 mix-blend-multiply"></div>
            <div class="relative z-10 flex h-full flex-col justify-between p-12 text-white">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white">school</span>
                    </div>
                    <span class="text-xl font-bold tracking-wide">{{ setting('site_title') ?: trans('panel.site_title') }}</span>
                </div>
                <div class="max-w-lg">
                    <h2 class="mb-4 text-4xl font-bold leading-tight tracking-tight">Access Restored.</h2>
                    <p class="text-lg text-blue-100 font-light">Choose a strong password to keep your account secure.
                    </p>
                </div>
                <div class="text-sm text-blue-200">© {{ date('Y') }} {{ setting('site_title') ?: trans('panel.site_title') }}. All rights
                    reserved.</div>
            </div>
        </div>

        <!-- Right Panel: Password Reset Form -->
        <div class="flex w-full items-center justify-center bg-white dark:bg-slate-850 lg:w-1/2 overflow-y-auto">
            <div class="w-full max-w-[440px] px-8 py-12 sm:px-12">
                <div class="mb-8">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Reset Password
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400">Please enter your new password below.</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Reset Code / OTP -->
                    <label class="flex flex-col gap-1.5 mb-6">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">OTP or Reset Code</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">vpn_key</span>
                            <input
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white transition-all shadow-sm {{ $errors->has('token') ? 'border-red-500' : '' }}"
                                placeholder="Enter 6-digit OTP or paste reset code" required type="text" name="token"
                                value="{{ (isset($token) && $token !== 'manual') ? $token : old('token') }}"
                                autocomplete="off" />
                        </div>
                        @if($errors->has('token'))
                            <div class="invalid-feedback text-xs mt-1 text-red-500">{{ $errors->first('token') }}</div>
                        @endif
                    </label>

                    <!-- Email Address -->
                    <label class="flex flex-col gap-1.5 mb-6">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">Email Address</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">mail</span>
                            <input
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 cursor-not-allowed transition-all"
                                type="email" name="email" value="{{ $email ?? old('email') }}" required readonly />
                        </div>
                        @if($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </label>

                    <!-- New Password -->
                    <label class="flex flex-col gap-1.5 mb-4">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">New Password</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">lock</span>
                            <input id="password"
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white transition-all shadow-sm"
                                placeholder="••••••••" required type="password" name="password" autofocus />
                            <button id="togglePassword"
                                class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                type="button">
                                <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                            </button>
                        </div>
                        @if($errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        @endif
                    </label>

                    <!-- Confirm Password -->
                    <label class="flex flex-col gap-1.5 mb-8">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">Confirm Password</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">lock_reset</span>
                            <input id="password_confirmation"
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white transition-all shadow-sm"
                                placeholder="••••••••" required type="password" name="password_confirmation" />
                            <button id="togglePasswordConfirm"
                                class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                type="button">
                                <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                            </button>
                        </div>
                    </label>

                    <button
                        class="flex h-12 w-full items-center justify-center rounded-lg bg-primary text-base font-semibold text-white shadow-md shadow-blue-500/20 transition-all hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                        type="submit">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');

            btn.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        }

        setupToggle('togglePassword', 'password');
        setupToggle('togglePasswordConfirm', 'password_confirmation');
    </script>
</body>

</html>