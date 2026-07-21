<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ExpenseAI') }} - AI Financial Intelligence</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { slate: { 950: '#090d16', 900: '#0f172a', 800: '#1e293b' } }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .glass-card { background: rgba(15,23,42,0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5); }
        .glass-pill { background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.12); }
        .text-gradient { background: linear-gradient(135deg, #a855f7 0%, #6366f1 50%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .text-gradient-emerald { background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-radial-gradient { background: radial-gradient(circle at 50% 0%, rgba(99,102,241,0.15) 0%, rgba(9,13,22,1) 70%); }
        [x-cloak] { display: none !important; }

        /* Mobile drawer slide animation */
        .mobile-drawer { transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .mobile-drawer.open { transform: translateX(0); }

        /* Bottom nav active */
        .bottom-nav-item.active i, .bottom-nav-item.active span { color: #818cf8; }

        /* Hide scrollbar but keep scroll */
        .no-scroll-bar::-webkit-scrollbar { width: 4px; }
        .no-scroll-bar::-webkit-scrollbar-track { background: transparent; }
        .no-scroll-bar::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen selection:bg-indigo-500 selection:text-white"
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        mobileMenuOpen: false,
        searchModal: false,
        theme: 'dark',
        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) { this.sidebarOpen = true; this.mobileMenuOpen = false; }
                else { this.sidebarOpen = false; }
            });
        }
    }">

    <div class="flex h-screen overflow-hidden bg-radial-gradient">

        <!-- ========== DESKTOP SIDEBAR ========== -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-0 lg:w-20'"
               class="hidden lg:flex transition-all duration-300 ease-in-out glass-card flex-col z-30 border-r border-slate-800/80 overflow-hidden no-scroll-bar">

            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/60 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20 shrink-0">
                        <div class="w-full h-full bg-slate-950 rounded-[9px] flex items-center justify-center">
                            <i class="fa-solid fa-brain text-indigo-400 text-sm"></i>
                        </div>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="font-extrabold text-lg tracking-tight text-white whitespace-nowrap">Expense<span class="text-gradient">AI</span></span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white p-1 rounded-lg shrink-0">
                    <i :class="sidebarOpen ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'" class="text-xs"></i>
                </button>
            </div>

            <!-- Workspace badge -->
            <div x-show="sidebarOpen" class="px-3 py-2 border-b border-slate-800/60 shrink-0">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/60 border border-slate-800 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-semibold text-slate-200">Personal Vault</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono text-[10px]">PRO</span>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto no-scroll-bar">
                @php
                    $navItems = [
                        ['route' => 'dashboard',       'icon' => 'fa-solid fa-chart-pie',            'label' => 'Dashboard'],
                        ['route' => 'daily.index',     'icon' => 'fa-solid fa-calendar-day',          'label' => "Today's Hub",    'color' => 'text-emerald-400'],
                        ['route' => 'nativeSync.index','icon' => 'fa-solid fa-bolt',                  'label' => 'Live Bank Sync', 'color' => 'text-amber-400'],
                        ['route' => 'gpay.connect',    'icon' => 'fa-brands fa-google',               'label' => 'Google Pay',     'color' => 'text-blue-400'],
                        ['route' => 'clients.index',   'icon' => 'fa-solid fa-mobile-screen-button',  'label' => 'Client Lookup',  'color' => 'text-indigo-400'],
                        ['route' => 'transactions.index','icon' => 'fa-solid fa-receipt',             'label' => 'Transactions'],
                        ['route' => 'import.index',    'icon' => 'fa-solid fa-file-import',           'label' => 'Import & SMS'],
                        ['route' => 'receipts.index',  'icon' => 'fa-solid fa-camera',               'label' => 'Receipt OCR'],
                        ['route' => 'accounts.index',  'icon' => 'fa-solid fa-wallet',               'label' => 'Banks & Cards'],
                        ['route' => 'budgets.index',   'icon' => 'fa-solid fa-scale-balanced',        'label' => 'Budgets'],
                        ['route' => 'goals.index',     'icon' => 'fa-solid fa-bullseye',              'label' => 'Goals & Savings'],
                        ['route' => 'subscriptions.index','icon' => 'fa-solid fa-calendar-check',     'label' => 'Subscriptions'],
                        ['route' => 'reports.index',   'icon' => 'fa-solid fa-file-pdf',              'label' => 'Reports'],
                        ['route' => 'ai.index',        'icon' => 'fa-solid fa-sparkles',              'label' => 'Ask AI',         'color' => 'text-amber-400'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                              {{ $active ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/50' }}"
                       title="{{ $item['label'] }}">
                        <i class="{{ $item['icon'] }} {{ $item['color'] ?? '' }} w-5 text-center text-sm shrink-0"></i>
                        <span x-show="sidebarOpen" class="text-sm truncate">{{ $item['label'] }}</span>
                    </a>
                @endforeach

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <div class="pt-3 pb-1">
                        <span x-show="sidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Admin</span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/50' }}">
                        <i class="fa-solid fa-shield-halved text-purple-400 w-5 text-center text-sm"></i>
                        <span x-show="sidebarOpen" class="text-sm">Admin Panel</span>
                    </a>
                @endif
            </nav>

            <!-- User footer -->
            <div class="p-3 border-t border-slate-800/60 shrink-0">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-900/60 transition">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=6366f1&color=fff"
                         class="w-8 h-8 rounded-lg object-cover ring-2 ring-indigo-500/30 shrink-0" alt="Avatar">
                    <div x-show="sidebarOpen" class="flex-1 overflow-hidden">
                        <h4 class="text-xs font-bold text-slate-200 truncate">{{ auth()->user()->name ?? 'User' }}</h4>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </a>
            </div>
        </aside>

        <!-- ========== MOBILE DRAWER OVERLAY ========== -->
        <div x-show="mobileMenuOpen" x-cloak
             class="fixed inset-0 z-50 lg:hidden"
             @click="mobileMenuOpen = false">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

            <!-- Drawer panel -->
            <aside class="absolute inset-y-0 left-0 w-72 glass-card flex flex-col border-r border-slate-800/80 z-10"
                   @click.stop>
                <!-- Brand -->
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/60">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" @click="mobileMenuOpen = false">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20">
                            <div class="w-full h-full bg-slate-950 rounded-[9px] flex items-center justify-center">
                                <i class="fa-solid fa-brain text-indigo-400 text-sm"></i>
                            </div>
                        </div>
                        <span class="font-extrabold text-lg tracking-tight text-white">Expense<span class="text-gradient">AI</span></span>
                    </a>
                    <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- User card in drawer -->
                <div class="px-3 py-3 border-b border-slate-800/60">
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=6366f1&color=fff"
                             class="w-10 h-10 rounded-xl object-cover ring-2 ring-indigo-500/30" alt="Avatar">
                        <div class="flex-1 overflow-hidden">
                            <h4 class="text-sm font-bold text-slate-100 truncate">{{ auth()->user()->name ?? 'User' }}</h4>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->phone ?? auth()->user()->email ?? '' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono text-[10px] font-bold">PRO</span>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto no-scroll-bar">
                    @foreach($navItems as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200
                                  {{ $active ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-900/50' }}"
                           @click="mobileMenuOpen = false">
                            <i class="{{ $item['icon'] }} {{ $item['color'] ?? '' }} w-5 text-center"></i>
                            <span class="text-sm font-medium">{{ $item['label'] }}</span>
                        </a>
                    @endforeach

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="pt-3 pb-1 px-3">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Admin</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 transition"
                           @click="mobileMenuOpen = false">
                            <i class="fa-solid fa-shield-halved text-purple-400 w-5 text-center"></i>
                            <span class="text-sm font-medium">Admin Panel</span>
                        </a>
                    @endif
                </nav>

                <!-- Logout in drawer -->
                <div class="p-3 border-t border-slate-800/60 space-y-2">
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 transition" @click="mobileMenuOpen = false">
                        <i class="fa-solid fa-gear w-5 text-center"></i>
                        <span class="text-sm">Settings</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-400 hover:bg-red-500/10 transition">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span class="text-sm">Sign Out</span>
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- Top Navbar -->
            <header class="h-14 md:h-16 border-b border-slate-800/80 glass-card px-3 md:px-6 flex items-center justify-between z-20 shrink-0 gap-3">

                <!-- Left: Hamburger (mobile) + Search -->
                <div class="flex items-center gap-2 md:gap-4 min-w-0">
                    <!-- Mobile hamburger -->
                    <button @click="mobileMenuOpen = true"
                            class="lg:hidden p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition shrink-0">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <!-- Logo on mobile (center) -->
                    <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5">
                            <div class="w-full h-full bg-slate-950 rounded-md flex items-center justify-center">
                                <i class="fa-solid fa-brain text-indigo-400 text-xs"></i>
                            </div>
                        </div>
                        <span class="font-extrabold text-base text-white">Expense<span class="text-gradient">AI</span></span>
                    </a>

                    <!-- Desktop search -->
                    <form method="GET" action="{{ route('clients.index') }}" class="hidden md:flex items-center">
                        <div class="relative w-56 lg:w-72">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input type="text" name="phone" placeholder="Enter Client Mobile Phone..."
                                   class="w-full pl-8 pr-7 py-2 rounded-xl bg-slate-900/90 border border-slate-800 text-xs text-slate-200 font-mono focus:outline-none focus:border-indigo-500 transition">
                            <button type="submit" class="absolute right-2 top-2 text-slate-400 hover:text-white">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Mobile search icon -->
                    <a href="{{ route('clients.index') }}" class="md:hidden p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 md:p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-indigo-500"></span>
                    </a>

                    <!-- Settings (desktop) -->
                    <a href="{{ route('settings.index') }}" class="hidden md:flex p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-gear text-sm"></i>
                    </a>

                    <!-- Logout (desktop) -->
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>

                    <!-- Avatar (mobile) -->
                    <a href="{{ route('settings.index') }}" class="md:hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'U') }}&background=6366f1&color=fff"
                             class="w-8 h-8 rounded-lg object-cover ring-2 ring-indigo-500/30" alt="Avatar">
                    </a>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-3 md:p-6 lg:p-8 pb-20 lg:pb-6 no-scroll-bar">
                @if(session('success'))
                    <div class="mb-4 p-3 md:p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-check shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 md:p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- ========== MOBILE BOTTOM NAV ========== -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 glass-card border-t border-slate-800/80 flex items-center justify-around px-2 py-2 safe-area-inset-bottom">
        @php
            $bottomNav = [
                ['route' => 'dashboard',        'icon' => 'fa-solid fa-chart-pie',       'label' => 'Home'],
                ['route' => 'daily.index',       'icon' => 'fa-solid fa-calendar-day',    'label' => 'Today'],
                ['route' => 'transactions.index','icon' => 'fa-solid fa-receipt',         'label' => 'Txns'],
                ['route' => 'nativeSync.index',  'icon' => 'fa-solid fa-bolt',            'label' => 'Sync'],
                ['route' => 'ai.index',          'icon' => 'fa-solid fa-sparkles',        'label' => 'AI'],
            ];
        @endphp
        @foreach($bottomNav as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="bottom-nav-item {{ $active ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all
                      {{ $active ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-500 hover:text-slate-300' }}">
                <i class="{{ $item['icon'] }} text-base"></i>
                <span class="text-[10px] font-semibold">{{ $item['label'] }}</span>
            </a>
        @endforeach
        <!-- More -->
        <button @click="mobileMenuOpen = true"
                class="bottom-nav-item flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-500 hover:text-slate-300 transition">
            <i class="fa-solid fa-grip text-base"></i>
            <span class="text-[10px] font-semibold">More</span>
        </button>
    </nav>

    <!-- Search Modal -->
    <div x-show="searchModal" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center pt-16 md:pt-20 bg-slate-950/80 backdrop-blur-md p-4"
         @keydown.window.escape="searchModal = false">
        <div class="w-full max-w-xl glass-card rounded-2xl p-5 md:p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="searchModal = false">
            <div class="flex items-center gap-3 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-sparkles text-amber-400 text-lg shrink-0"></i>
                <input type="text" placeholder="Search transactions, or ask AI..." class="w-full bg-transparent border-none text-white focus:outline-none placeholder-slate-500 text-sm">
            </div>
            <div class="text-xs text-slate-400 space-y-2">
                <p class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Quick Links</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('ai.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition text-xs">Ask AI</a>
                    <a href="{{ route('transactions.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition text-xs">Transactions</a>
                    <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition text-xs">Reports</a>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
