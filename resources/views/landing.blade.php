<x-guest-layout>
    <!-- Hero Section -->
    <section class="py-24 px-6 relative overflow-hidden">
        <div class="max-w-7xl mx-auto text-center space-y-8 z-10 relative">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass-pill text-xs font-semibold text-indigo-300 border border-indigo-500/30 shadow-inner">
                <i class="fa-solid fa-sparkles text-amber-400"></i> Powered by Google Gemini 1.5/2.0 AI
            </div>

            <h1 class="text-4xl md:text-7xl font-extrabold text-white tracking-tight leading-tight max-w-5xl mx-auto">
                Next-Generation <span class="text-gradient">AI Financial Intelligence</span> for Modern Wealth
            </h1>

            <p class="text-base md:text-xl text-slate-400 max-w-3xl mx-auto font-normal">
                ExpenseAI automatically aggregates, parses, categorizes, and optimizes your financial life from bank accounts, SMS backups, and receipt scans with instant AI advice.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-indigo-500 via-purple-600 to-emerald-500 text-white font-extrabold text-base shadow-xl shadow-indigo-500/30 hover:scale-105 transition transform">
                    Launch ExpenseAI Free <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl glass-card border border-slate-700 text-slate-200 font-bold text-base hover:bg-slate-900/60 transition">
                    Explore Demo Vault
                </a>
            </div>

            <!-- Dashboard Preview Graphic -->
            <div class="pt-12 max-w-5xl mx-auto">
                <div class="glass-card rounded-3xl p-4 md:p-8 border border-slate-700/80 shadow-2xl relative overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
                            <span class="text-xs text-slate-400 font-semibold uppercase">Total Wealth</span>
                            <h3 class="text-3xl font-extrabold text-white mt-1">$48,920.50</h3>
                            <span class="text-xs text-emerald-400 font-bold flex items-center gap-1 mt-2">
                                <i class="fa-solid fa-arrow-trend-up"></i> +14.2% from last month
                            </span>
                        </div>
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
                            <span class="text-xs text-slate-400 font-semibold uppercase">Monthly Burn Rate</span>
                            <h3 class="text-3xl font-extrabold text-white mt-1">$3,410.80</h3>
                            <span class="text-xs text-indigo-400 font-bold flex items-center gap-1 mt-2">
                                <i class="fa-solid fa-shield-check"></i> Within safe budget bounds
                            </span>
                        </div>
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
                            <span class="text-xs text-slate-400 font-semibold uppercase">Gemini AI Score</span>
                            <h3 class="text-3xl font-extrabold text-gradient-emerald mt-1">94 / 100</h3>
                            <span class="text-xs text-amber-400 font-bold flex items-center gap-1 mt-2">
                                <i class="fa-solid fa-lightbulb"></i> 3 optimization tips ready
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Grid -->
    <section class="py-20 px-6 border-t border-slate-800/80 bg-slate-950/60">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl md:text-5xl font-extrabold text-white">Engineered Like CRED & Stripe</h2>
                <p class="text-sm text-slate-400 max-w-xl mx-auto">Built for maximum financial clarity with zero compromise on design or privacy.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card rounded-2xl p-6 border border-slate-800 space-y-4 hover:border-indigo-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-message"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Android SMS Backup Parser</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Import XML backups from Android devices to parse debit, credit, UPI, NEFT, and salary alerts instantly without manual typing.</p>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-slate-800 space-y-4 hover:border-indigo-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Receipt & Invoice OCR</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Scan grocery or store bills to automatically extract merchant, GST, line items, and invoice total in seconds.</p>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-slate-800 space-y-4 hover:border-indigo-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Gemini Natural Language Assistant</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Ask "How much did I spend on coffee?" or "Am I on target for my Japan vacation?" and receive instant markdown insights.</p>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
