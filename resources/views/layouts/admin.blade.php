<!DOCTYPE html>
<html>

<head>
    @include('layouts._partials.head')

    <style>
        .symbol-of-tk {
            font-size: 20px;
            font-weight: bold;
            margin-top: -4px;
            margin-left: -4px;
        }

        /* Override sidebar active menu item background to white */
        .c-sidebar .c-active.c-sidebar-nav-dropdown-toggle,
        .c-sidebar .c-sidebar-nav-link.c-active,
        .c-sidebar.c-sidebar-light .c-active.c-sidebar-nav-dropdown-toggle {
            background: #ffffff !important;
            color: #1e293b !important;
            font-weight: 600;
            border-left: 3px solid #667eea;
        }

        .c-sidebar .c-active.c-sidebar-nav-dropdown-toggle .c-sidebar-nav-icon,
        .c-sidebar .c-sidebar-nav-link.c-active .c-sidebar-nav-icon {
            color: #667eea !important;
        }


        .c-sidebar-nav-dropdown-items {
            margin-left: 12% !important;
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>

<body class="c-app">
    @include('partials.menu')


    <div class="c-wrapper bg-slate-50 dark:bg-background-dark transition-colors duration-300">

        @include('layouts._partials.header')

        <div class="c-body">
            <main class="c-main">
                <div class="container-fluid">
                    @include('layouts._partials.global_alerts')


                    {{-- all page's content goes here --}}
                    @yield('content')


                </div>
            </main>
            <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>

        <!-- Footer Copyright -->
        <div class="mt-12 border-t border-slate-200 py-6 text-center dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">© {{ date('Y') }} {{ config('app.name') }}. All
                rights reserved.
            </p>
        </div>
    </div>



    {{-- All the layout scripts --}}
    @include('layouts._partials.footer_scripts')




    <script>
        @can('due_collection_access')
            document.addEventListener('keydown', function(e) {
                const tag = e.target.tagName.toLowerCase();

                if (e.ctrlKey && (e.key === 'Enter' || e.keyCode === 13) && tag !== 'input' && tag !== 'textarea') {
                    e.preventDefault();
                    window.location.href = '{{ route("admin.due-collections.checker") }}';
                }
            });
        @endcan
    </script>


    {{-- Page specific scripts --}}
    @yield('scripts')

    @stack('scripts')

    <button id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-teal-600 text-white shadow-lg hover:bg-teal-700 transition-all duration-300 flex items-center justify-center opacity-0 invisible scale-75"
        style="box-shadow: 0 4px 15px rgba(15,118,110,0.4);">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    <style>
        #scrollTopBtn.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: scale(1) !important;
        }
    </style>

    <script>
        window.addEventListener('scroll', function() {
            const btn = document.getElementById('scrollTopBtn');
            if (window.scrollY > 300) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        });
    </script>
</body>

</html>
