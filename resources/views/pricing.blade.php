<x-guest-layout>
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto space-y-12 text-center">
            <div class="space-y-3">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white">Transparent SaaS Pricing</h1>
                <p class="text-sm text-slate-400 max-w-xl mx-auto">Choose the tier that powers your personal or enterprise financial intelligence.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <!-- Free Plan -->
                <div class="glass-card rounded-3xl p-8 border border-slate-800 space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Starter</h3>
                        <p class="text-xs text-slate-400 mt-1">For basic tracking</p>
                    </div>
                    <div class="text-4xl font-extrabold text-white">$0 <span class="text-xs text-slate-500 font-normal">/ forever</span></div>
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Up to 100 Transactions/mo</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 2 Bank Accounts</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Standard Categories</li>
                        <li class="text-slate-600"><i class="fa-solid fa-xmark mr-2"></i> No AI Assistant</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 rounded-xl bg-slate-900 border border-slate-700 text-center font-bold text-xs text-white hover:border-slate-500 transition">Get Started</a>
                </div>

                <!-- Pro Plan -->
                <div class="glass-card rounded-3xl p-8 border-2 border-indigo-500 relative space-y-6 shadow-2xl shadow-indigo-500/20">
                    <span class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-indigo-600 text-white font-extrabold text-[10px] tracking-wider uppercase">Most Popular</span>
                    <div>
                        <h3 class="text-xl font-bold text-white">Pro AI Vault</h3>
                        <p class="text-xs text-indigo-300 mt-1">For active investors & professionals</p>
                    </div>
                    <div class="text-4xl font-extrabold text-white">$12 <span class="text-xs text-slate-400 font-normal">/ month</span></div>
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Unlimited Transactions</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Gemini AI Assistant (Unlimited)</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Android SMS XML Auto Import</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Receipt OCR Scanner</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> DomPDF & Excel Export</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-center font-extrabold text-xs text-white shadow-lg shadow-indigo-500/30 hover:brightness-110 transition">Start 14-Day Free Trial</a>
                </div>

                <!-- Enterprise Plan -->
                <div class="glass-card rounded-3xl p-8 border border-slate-800 space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Enterprise OS</h3>
                        <p class="text-xs text-slate-400 mt-1">For multi-account teams & CFOs</p>
                    </div>
                    <div class="text-4xl font-extrabold text-white">$49 <span class="text-xs text-slate-500 font-normal">/ month</span></div>
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> All Pro Features</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Multi-workspace Support</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Custom Spatie RBAC</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Dedicated API Tokens</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 rounded-xl bg-slate-900 border border-slate-700 text-center font-bold text-xs text-white hover:border-slate-500 transition">Contact Enterprise Sales</a>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
