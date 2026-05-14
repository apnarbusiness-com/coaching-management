<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login — {{ trans('panel.site_title') }}</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/logo.svg') }}" type="image/x-icon">


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />

    {{-- Logo Fonts --}}
    <style>
        @font-face {
            font-family: 'fall-in-love-font';
            src: url('assets/fonts/fonts/fall_in_love.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .logo-moto {
            font-family: 'fall-in-love-font', cursive;
            /* font-size: 1.25rem; */
        }
    </style>



    <!-- Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
            color: red;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 h-screen overflow-hidden flex flex-col font-display antialiased">
    <div class="flex flex-1 h-full w-full">
        <!-- Left Panel: Branding & Inspiration -->
        <div class="relative hidden lg:flex w-1/2 flex-col justify-between overflow-hidden " style="--tw-bg-opacity: 1;
    background-color: rgb(15 23 42 / 67%);">
            <!-- Background Image -->
            <div class="absolute inset-0 h-full w-full bg-cover bg-center opacity-60 mix-blend-overlay transition-opacity duration-1000"
                data-alt="Students focused on learning in a modern library environment with warm lighting"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDEaFg7O5Iba-OU3kPf_u-ZIcRbWqb5zkDCuFkTXOkE0htcf0UoCexR6cgMD66-vnanR8yhOeh-17lHQLV3hQ_vC4kjUeuiz-_yA-fLDsQ2STxscPDvDMtCBogmTVYe5ofTfDmixw18Tu9SZoaa4-GlEl_7w9v-MBaabwgBftKF0hVW5UKF2WY5oefmKfUphU0lxeXZV3sUayRE8ai2LSVfaw0fo0IxTWlsBBVSwKk6cIXvgi_ls4h1pZahDO1aJkdHcSTlZRxjZ94');">
            </div>
            <!-- Primary Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/90 to-blue-900/90 mix-blend-multiply"></div>
            <!-- Content -->
            <div class="relative z-10 flex h-full flex-col justify-between p-12 text-white">
                <!-- Logo Area -->
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white">school</span>
                    </div>
                    <img src="{{ asset('assets/images/logo_for_menu.svg') }}" alt="Logo" class="h-10">
                    {{-- <span class="text-xl font-bold tracking-wide">
                        {{ trans('panel.site_title') }}
                    </span> --}}
                </div>
                <!-- Inspirational Text -->
                <div class="max-w-lg">
                    <h2 class="mb-4 text-4xl font-bold leading-tight tracking-tight">
                        Empowering the next generation of learners.
                    </h2>
                    <p class="text-lg text-blue-100 font-light logo-moto">
                        {{-- Join thousands of students and educators transforming
                        the way we learn, teach, and grow together. --}}
                        <i>Learn to Serve The Nation</i>
                    </p>
                </div>
                <!-- Footer/Copyright on Image -->
                <div class="text-sm text-blue-200">
                    © {{ date('Y') }} {{ trans('panel.site_title') }}. All rights reserved.
                </div>
            </div>
        </div>
        <!-- Right Panel: Login Form -->
        <div class="flex w-full items-center justify-center bg-white dark:bg-slate-850 lg:w-1/2 overflow-y-auto">
            <div class="w-full max-w-[440px] px-8 py-12 sm:px-12">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" class="h-10"
                    style="margin-left: -9px;margin-bottom: 16px;">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Welcome Back</h1>
                    <p class="text-slate-500 dark:text-slate-400">Please enter your details to sign in.</p>
                </div>
                <!-- Role Selector (Segmented Button) -->

                {{-- commented out role selector
                <div class="mb-8">
                    <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Select Role</p>
                    <div
                        class="flex h-12 w-full items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 p-1">
                        <!-- Admin -->
                        <label
                            class="group flex h-full flex-1 cursor-pointer items-center justify-center rounded-lg px-2 transition-all has-[:checked]:bg-white dark:has-[:checked]:bg-slate-700 has-[:checked]:text-primary dark:has-[:checked]:text-blue-400 has-[:checked]:shadow-sm">
                            <span
                                class="text-sm font-medium text-slate-500 group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-slate-200">Admin</span>
                            <input class="invisible w-0" name="role" type="radio" value="Admin" />
                        </label>
                        <!-- Teacher -->
                        <label
                            class="group flex h-full flex-1 cursor-pointer items-center justify-center rounded-lg px-2 transition-all has-[:checked]:bg-white dark:has-[:checked]:bg-slate-700 has-[:checked]:text-primary dark:has-[:checked]:text-blue-400 has-[:checked]:shadow-sm">
                            <span
                                class="text-sm font-medium text-slate-500 group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-slate-200">Teacher</span>
                            <input class="invisible w-0" name="role" type="radio" value="Teacher" />
                        </label>
                        <!-- Student -->
                        <label
                            class="group flex h-full flex-1 cursor-pointer items-center justify-center rounded-lg px-2 transition-all has-[:checked]:bg-white dark:has-[:checked]:bg-slate-700 has-[:checked]:text-primary dark:has-[:checked]:text-blue-400 has-[:checked]:shadow-sm">
                            <span
                                class="text-sm font-medium text-slate-500 group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-slate-200">Student</span>
                            <input checked="" class="invisible w-0" name="role" type="radio" value="Student" />
                        </label>
                    </div>
                </div>
                --}}






                @if (session('message'))
                    <div class="alert alert-info" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Form Inputs -->
                <!-- <form action="#" class="flex flex-col gap-5" method="POST"> -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Identity Input -->
                    <label class="flex flex-col gap-1.5 mb-4">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">Email / Username /
                            Admission ID</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">account_circle</span>
                            <input
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                placeholder="Email, Username or ID" required="" type="text" name="login"
                                value="{{ old('login') }}" />
                        </div>
                        @if ($errors->has('login'))
                            <div class="invalid-feedback">
                                {{ $errors->first('login') }}
                            </div>
                        @endif
                    </label>
                    <!-- Password Input -->
                    <label class="flex flex-col gap-1.5 mb-2">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">Password</span>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 material-symbols-outlined"
                                style="font-size: 20px;">lock</span>
                            <input id="password"
                                class="form-input h-12 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                placeholder="••••••••" required="" type="password" name="password" />
                            <button id="togglePassword"
                                class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                type="button">
                                <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </label>
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                type="checkbox" />
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Remember me</span>
                        </label>
                        <a class="text-sm font-semibold text-primary hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                            href="{{ route('password.request') }}">Forgot Password?</a>
                    </div>
                    <!-- Login Button -->
                    <button
                        class="mt-2 flex h-12 w-full items-center justify-center rounded-lg bg-primary text-base font-semibold text-white shadow-md shadow-blue-500/20 transition-all hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                        type="submit">
                        Log In
                    </button>
                </form>
                <!-- Footer Action -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Don't have an account?
                        <a class="font-semibold text-primary hover:underline dark:text-blue-400" href="#">Contact
                            Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('.material-symbols-outlined');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    </script>
</body>

</html>