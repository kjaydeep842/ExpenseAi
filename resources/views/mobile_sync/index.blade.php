<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-cyan-500 flex items-center justify-center text-xl text-white shadow-xl shadow-teal-500/20">
                        <i class="fa-solid fa-[#mobile]"></i>
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </span>
                    Live Mobile GPay & Bank Auto-Sync Engine
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Hands-free real-time integration for your phone. Automatically capture live Google Pay, PhonePe, Paytm & Kotak Bank SMS debits with 0 clicks.
                </p>
            </div>

            <a href="{{ route('mobileSync.download') }}" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 hover:brightness-110 transition flex items-center gap-2">
                <i class="fa-solid fa-download"></i> Download 1-Click Sync Config (.json)
            </a>
        </div>

        <!-- How It Works Explanation Card -->
        <div class="glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6">
            <div class="border-b border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-400"></i> Why Is This Required for Live Real-World Google Pay?
                </h3>
                <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                    Google Pay does not allow 3rd party web applications to query personal user balances directly via public APIs due to <strong>RBI & Google Privacy Regulations</strong>. FinTech apps like CRED, Jupiter, and Fold capture live payments through a lightweight <strong>Android Notification Listener</strong> running on your phone.
                </p>
            </div>

            <!-- 3-Step Live Setup Guide -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-extrabold text-sm">1</div>
                    <h4 class="font-bold text-white text-sm">Download Listener App</h4>
                    <p class="text-xs text-slate-400">Install <strong>MacroDroid</strong> or <strong>Tasker</strong> (Free on Play Store) or download our JSON Config File.</p>
                </div>

                <!-- Step 2 -->
                <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-extrabold text-sm">2</div>
                    <h4 class="font-bold text-white text-sm">Import Config File</h4>
                    <p class="text-xs text-slate-400">Import <code class="text-purple-300">ExpenseAI_GPay_Live_Sync.json</code> into MacroDroid on your phone.</p>
                </div>

                <!-- Step 3 -->
                <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-extrabold text-sm">3</div>
                    <h4 class="font-bold text-white text-sm">Make Real GPay Payment</h4>
                    <p class="text-xs text-slate-400">Pay ₹1 or ₹50 on Google Pay at any shop. Watch it log automatically on your dashboard!</p>
                </div>
            </div>

            <!-- Live Webhook Target URL -->
            <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-tower-cell text-emerald-400"></i> Your Registered Mobile Listener Endpoint
                    </span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-mono font-bold">READY FOR LIVE PAYLOADS</span>
                </div>
                <pre class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-emerald-300 font-mono text-xs overflow-x-auto">{{ url('/api/v1/payment-app/notification') }}</pre>
                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                    <span>Registered Phone: <strong class="text-indigo-400 font-mono">{{ $user->phone }}</strong></span>
                    <span>Format: <code class="text-slate-300">JSON POST</code></span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
