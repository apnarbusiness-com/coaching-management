<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Reset Password | {{ setting('site_title') ?: trans('panel.site_title') }}</title>
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
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDEaFg7O5Iba-OU3kPf_u-ZIcRbWqb5zkDCuFkTXOkE0htcf0UoCexR6cgMD66-vnanR8yhOeh-17lHQLV3hQ_vC4kjUeuiz-_yA-fLDsQ2STxscPDvDMtCBogmTVYe5ofTfDmixw18Tu9SZoaa4-GlEl_7w9v-MBaabwgBftKF0hVW5UKF2WY5oefmKfUphU0lxeXZV3sUayRE8ai2LSVfaw0fo0IxTWlsBBVSwKk6cIXvgi_ls4h1pZahDO1aJkdHcSTlZRxjZ94');">
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
                    <h2 class="mb-4 text-4xl font-bold leading-tight tracking-tight">Security in every step.</h2>
                    <p class="text-lg text-blue-100 font-light">Don't worry, it happens to the best of us. We'll help
                        you get back into your account safely.</p>
                </div>
                <div class="text-sm text-blue-200">© {{ date('Y') }} {{ setting('site_title') ?: trans('panel.site_title') }}. All rights
                    reserved.</div>
            </div>
        </div>

        <!-- Right Panel: Password Reset Request Form -->
        <div class="flex w-full items-center justify-center bg-white dark:bg-slate-850 lg:w-1/2 overflow-y-auto">
            <div class="w-full max-w-[440px] px-8 py-12 sm:px-12">
                <div class="mb-8">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Forgot Password?
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400">Enter your email and we'll send you a link to reset
                        your password.</p>
                </div>

                @if(session('status'))
                    <div
                        class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm flex items-center gap-3 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <label class="flex flex-col gap-1.5 mb-6">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">Email Address</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">mail</span>
                            <input
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white transition-all shadow-sm"
                                placeholder="name@school.com" required type="email" name="email"
                                value="{{ old('email') }}" autofocus />
                        </div>
                        @if($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </label>

                    <button
                        class="flex h-12 w-full items-center justify-center rounded-lg bg-primary text-base font-semibold text-white shadow-md shadow-blue-500/20 transition-all hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                        type="submit">
                        Send Reset Link
                    </button>

                    <div class="mt-8 text-center">
                        <a class="text-sm font-semibold text-primary hover:underline dark:text-blue-400 flex items-center justify-center gap-2"
                            href="{{ route('login') }}">
                            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>