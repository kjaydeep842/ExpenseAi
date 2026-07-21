<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ExpenseAI') }} - AI Financial Intelligence</title>

    <!-- Google Fonts & Tailwind CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN Engine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            950: '#090d16',
                            900: '#0f172a',
                            800: '#1e293b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine JS & ApexCharts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }
        .glass-card-light {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.05);
        }
        .glass-pill {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .text-gradient {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 50%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-gradient-emerald {
            background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-radial-gradient {
            background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.15) 0%, rgba(9, 13, 22, 1) 70%);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: true, searchModal: false, theme: 'dark' }">

    <div class="flex h-screen overflow-hidden bg-radial-gradient">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="transition-all duration-300 ease-in-out glass-card flex flex-col z-30 border-r border-slate-800/80">
            <!-- Brand Header -->
            <div class="h-20 flex items-center justify-between px-5 border-b border-slate-800/60">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                            <i class="fa-solid fa-brain text-indigo-400 text-lg"></i>
                        </div>
                    </div>
                    <span x-show="sidebarOpen" class="font-extrabold text-xl tracking-tight text-white">Expense<span class="text-gradient">AI</span></span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <i :class="sidebarOpen ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'"></i>
                </button>
            </div>

            <!-- Workspace Switcher -->
            <div x-show="sidebarOpen" class="px-4 py-3 border-b border-slate-800/60">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/60 border border-slate-800 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-semibold text-slate-200">Personal Vault</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono text-[10px]">PRO</span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'fa-solid fa-chart-pie', 'label' => 'Dashboard'],
                        ['route' => 'transactions.index', 'icon' => 'fa-solid fa-receipt', 'label' => 'Transactions'],
                        ['route' => 'import.index', 'icon' => 'fa-solid fa-file-import', 'label' => 'Import & SMS'],
                        ['route' => 'receipts.index', 'icon' => 'fa-solid fa-camera', 'label' => 'Receipt OCR'],
                        ['route' => 'accounts.index', 'icon' => 'fa-solid fa-wallet', 'label' => 'Banks & Cards'],
                        ['route' => 'budgets.index', 'icon' => 'fa-solid fa-scale-balanced', 'label' => 'Budgets'],
                        ['route' => 'goals.index', 'icon' => 'fa-solid fa-bullseye', 'label' => 'Goals & Savings'],
                        ['route' => 'subscriptions.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Subscriptions'],
                        ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-pdf', 'label' => 'Reports & Analytics'],
                        ['route' => 'ai.index', 'icon' => 'fa-solid fa-sparkles text-amber-400', 'label' => 'Ask AI Assistant'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs($item['route']) ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/40' }}">
                        <i class="{{ $item['icon'] }} w-5 text-center text-sm"></i>
                        <span x-show="sidebarOpen" class="text-sm tracking-wide">{{ $item['label'] }}</span>
                    </a>
                @endforeach

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <div class="pt-4 pb-1">
                        <span x-show="sidebarOpen" class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Admin Space</span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/40' }}">
                        <i class="fa-solid fa-shield-halved w-5 text-center text-sm text-purple-400"></i>
                        <span x-show="sidebarOpen" class="text-sm">Admin Panel</span>
                    </a>
                @endif
            </nav>

            <!-- User Footer Menu -->
            <div class="p-3 border-t border-slate-800/60">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-900/60 transition">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=6366f1&color=fff" class="w-9 h-9 rounded-lg object-cover ring-2 ring-indigo-500/30" alt="Avatar">
                    <div x-show="sidebarOpen" class="flex-1 overflow-hidden">
                        <h4 class="text-xs font-bold text-slate-200 truncate">{{ auth()->user()->name ?? 'User Account' }}</h4>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? 'user@expenseai.test' }}</p>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Sticky Navbar -->
            <header class="h-20 border-b border-slate-800/80 glass-card px-6 flex items-center justify-between z-20">
                <div class="flex items-center gap-4">
                    <!-- Mega Search Button -->
                    <button @click="searchModal = true" class="flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 text-xs hover:border-slate-700 transition w-64 md:w-80">
                        <i class="fa-solid fa-magnifying-glass text-indigo-400"></i>
                        <span>Search transactions, AI...</span>
                        <kbd class="ml-auto bg-slate-800 text-slate-400 text-[10px] px-1.5 py-0.5 rounded border border-slate-700">⌘K</kbd>
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-indigo-500 animate-ping"></span>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    </a>

                    <!-- Profile Settings -->
                    <a href="{{ route('settings.index') }}" class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-gear"></i>
                    </a>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
                        <span><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center justify-between">
                        <span><i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mega Search Modal -->
    <div x-show="searchModal" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-slate-950/80 backdrop-blur-md" @keydown.window.escape="searchModal = false">
        <div class="w-full max-w-xl glass-card rounded-2xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="searchModal = false">
            <div class="flex items-center gap-3 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-sparkles text-amber-400 text-lg"></i>
                <input type="text" placeholder="Search transactions, categories, or ask AI..." class="w-full bg-transparent border-none text-white focus:outline-none placeholder-slate-500 text-sm">
            </div>
            <div class="text-xs text-slate-400 space-y-2">
                <p class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Quick Suggestions</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('ai.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition">"How much spent on Food?"</a>
                    <a href="{{ route('transactions.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition">"Show Amazon expenses"</a>
                    <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-indigo-500 transition">"Generate PDF Report"</a>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
