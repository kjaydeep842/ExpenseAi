<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ExpenseAI') }} - Premium AI Expense Intelligence Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen selection:bg-indigo-500 selection:text-white bg-radial-gradient">

    <!-- Public Navigation Bar -->
    <header class="sticky top-0 z-50 border-b border-slate-800/60 glass-card px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i class="fa-solid fa-brain text-indigo-400 text-lg"></i>
                    </div>
                </div>
                <span class="font-extrabold text-xl tracking-tight text-white">Expense<span class="text-gradient">AI</span></span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
                <a href="{{ route('landing') }}" class="hover:text-white transition">Features</a>
                <a href="{{ route('pricing') }}" class="hover:text-white transition">Pricing</a>
                <a href="{{ route('pricing') }}#faq" class="hover:text-white transition">FAQ</a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-semibold text-sm text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 transition">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white transition">Sign In</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-semibold text-sm text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 transition">Start Free Trial</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-800/80 bg-slate-950/90 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-brain text-indigo-400"></i>
                <span class="font-bold text-slate-300">ExpenseAI OS</span> &copy; {{ date('Y') }} All rights reserved. Enterprise FinTech Architecture.
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-slate-400">Privacy Policy</a>
                <a href="#" class="hover:text-slate-400">Terms of Service</a>
                <a href="#" class="hover:text-slate-400">Security Audit</a>
            </div>
        </div>
    </footer>
</body>
</html>
