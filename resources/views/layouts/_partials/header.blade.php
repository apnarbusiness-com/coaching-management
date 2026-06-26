<header
    class="c-header c-header-fixed px-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 transition-colors duration-300 no-print">
    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
        data-class="c-sidebar-show">
        <i class="fas fa-fw fa-bars"></i>
    </button>

    <a class="c-header-brand d-lg-none" href="#">{{ setting('site_title') ?: trans('panel.site_title') }}</a>

    <button class="c-header-toggler mfs-3 d-md-down-none" type="button" responsive="true">
        <i class="fas fa-fw fa-bars"></i>
    </button>


    <ul class="c-header-nav ml-auto">
        @if (count(config('panel.available_languages', [])) > 1)
            <li class="c-header-nav-item dropdown d-md-down-none">
                <a class="c-header-nav-link text-slate-700 dark:text-slate-300" data-toggle="dropdown" href="#"
                    role="button" aria-haspopup="true" aria-expanded="false">
                    {{ strtoupper(app()->getLocale()) }}
                </a>
                <div
                    class="dropdown-menu dropdown-menu-right bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700">
                    @foreach (config('panel.available_languages') as $langLocale => $langName)
                        <a class="dropdown-item text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700"
                            href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }}
                            ({{ $langName }})
                        </a>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>

    <div class="flex items-center gap-4 w-full max-w-md mx-4 hidden md:flex">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
            </div>
            <input
                class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg leading-5 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm"
                placeholder="Search students, teachers, records..." type="text" />
        </div>
    </div>

    <div class="flex items-center gap-4">
        @php
            $notifUser = auth()->user();
            $unreadNotifCount = $notifUser ? $notifUser->unreadNotifications()->count() : 0;
            $recentNotifs = $notifUser ? $notifUser->notifications()->latest()->take(5)->get() : collect();
        @endphp
        <div class="relative" id="headerNotificationDropdown">
            <button onclick="toggleNotifMenu()"
                class="relative p-2 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-full transition-colors">
                <span class="material-symbols-outlined">notifications</span>
                @if ($unreadNotifCount > 0)
                    <span
                        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center px-1 border-2 border-white dark:border-slate-900">
                        {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                    </span>
                @endif
            </button>

            <div id="headerNotifMenu"
                class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 z-50 origin-top-right transition-all duration-200">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</span>
                    @if ($unreadNotifCount > 0)
                        <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                        </form>
                    @endif
                </div>

                <div class="max-h-[320px] overflow-y-auto">
                    @forelse ($recentNotifs as $notif)
                        <a href="{{ $notif->link }}"
                           class="block px-4 py-3 border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors {{ !$notif->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                            <div class="flex gap-3">
                                <div class="mt-0.5 flex-shrink-0">
                                    @if ($notif->type === 'referral_reward')
                                        <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg">person_add</span>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-slate-500 text-lg">circle_notifications</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if ($notif->type === 'referral_reward')
                                        <p class="text-sm text-slate-700 dark:text-slate-300">
                                            Your referred student <strong>{{ $notif->data['student_name'] ?? '' }}</strong> has been admitted.
                                        </p>
                                        <p class="text-sm font-semibold text-green-600 dark:text-green-400 mt-0.5">
                                            You earned {{ number_format($notif->data['reward_amount'] ?? 0, 2) }} TK
                                        </p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Withdraw from wallet →</p>
                                    @else
                                        <p class="text-sm text-slate-700 dark:text-slate-300">Notification</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl">notifications_off</span>
                            <p class="text-sm mt-1">No notifications</p>
                        </div>
                    @endforelse
                </div>

                @if ($recentNotifs->isNotEmpty())
                    <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-700 text-center">
                        <span class="text-xs text-slate-400">Showing last {{ $recentNotifs->count() }}</span>
                    </div>
                @endif
            </div>
        </div>
        <button id="theme-toggle"
            class="p-2 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-full transition-colors">
            <span class="material-symbols-outlined">dark_mode</span>
        </button>

        <div class="relative" id="headerUserDropdown">
            <button onclick="toggleUserMenu()"
                class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                @php
                    $user = auth()->user();
                    $userRole = $user->roles->first()->title ?? 'User';
                    $userInitials = strtoupper(substr($user->name, 0, 1));
                @endphp
                @if ($user->photo)
                    <img src="{{ $user->photo->getUrl('thumb') }}" alt=""
                        class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600">
                @else
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                        {{ $userInitials }}
                    </div>
                @endif
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-tight">{{ $userRole }}</p>
                </div>
                <span class="material-symbols-outlined text-slate-400 dark:text-slate-500 text-sm hidden md:block">expand_more</span>
            </button>

            <div id="headerUserMenu"
                class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50 origin-top-right transition-all duration-200">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ $userRole }}</span>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-slate-400 text-lg">settings</span>
                    My Profile
                </a>
                <a href="#"
                    onclick="event.preventDefault(); document.getElementById('logoutform').submit();"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span>
                    Logout
                </a>
            </div>
        </div>

    </div>
</header>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('headerUserMenu');
        menu.classList.toggle('hidden');
    }

    function toggleNotifMenu() {
        const menu = document.getElementById('headerNotifMenu');
        menu.classList.toggle('hidden');
    }

    function closeAllDropdowns() {
        const userMenu = document.getElementById('headerUserMenu');
        const notifMenu = document.getElementById('headerNotifMenu');
        if (userMenu && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
        }
        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const userDropdown = document.getElementById('headerUserDropdown');
        const notifDropdown = document.getElementById('headerNotificationDropdown');
        const userMenu = document.getElementById('headerUserMenu');
        const notifMenu = document.getElementById('headerNotifMenu');

        if (userDropdown && !userDropdown.contains(e.target) && userMenu && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
        }
        if (notifDropdown && !notifDropdown.contains(e.target) && notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
        }
    });
</script>
