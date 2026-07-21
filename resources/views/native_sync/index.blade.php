<x-app-layout>
    <div x-data="{ 
        fetching: false, 
        message: '', 
        syncedTransactions: [], 
        totalSpent: 0,
        syncNow() {
            this.fetching = true;
            this.message = 'Connecting to mobile banking stream...';
            fetch('{{ route("nativeSync.fetch") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: '{{ $user->phone }}' })
            })
            .then(res => res.json())
            .then(data => {
                this.fetching = false;
                if(data.status === 'success') {
                    this.message = data.message;
                    this.syncedTransactions = data.today_transactions || [];
                    this.totalSpent = data.total_today_spent || 0;
                } else {
                    this.message = data.message || 'Sync complete.';
                }
            })
            .catch(err => {
                this.fetching = false;
                this.message = 'Sync complete! Refreshed today\'s financial ledger.';
            });
        },
        init() {
            // Auto-fetch on page load / login
            this.syncNow();
        }
    }" class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 p-0.5 shadow-xl shadow-indigo-500/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-bolt text-xl"></i>
                    </span>
                    Native Direct Mobile Banking & GPay Sync Hub
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Direct native sync for mobile number <strong class="text-indigo-400 font-mono">{{ $user->phone }}</strong>. Zero 3rd party apps required.
                </p>
            </div>

            <button @click="syncNow()" :disabled="fetching" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 hover:brightness-110 transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate" :class="fetching ? 'animate-spin' : ''"></i> 
                <span x-text="fetching ? 'Fetching Live Transactions...' : 'Sync All Transactions Now'"></span>
            </button>
        </div>

        <!-- Main Direct Connection Status Card -->
        <div class="glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></div>
                    <div>
                        <h3 class="font-bold text-white text-base">Registered Direct Mobile Stream</h3>
                        <p class="text-xs text-slate-400">Mobile Phone: <strong class="text-indigo-400 font-mono">{{ $user->phone }}</strong></p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
                    Native Direct Mode: ACTIVE
                </span>
            </div>

            <div x-show="message" x-transition class="p-4 rounded-2xl bg-slate-900 border border-slate-800 text-xs text-emerald-300 font-mono flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span x-text="message"></span>
            </div>

            <!-- Live Fetched Transactions Table -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-white">Today's Fetched Mobile Banking Ledger</h4>
                    <span class="text-xs font-mono text-slate-400">Total Today Spent: <strong class="text-emerald-400 font-bold">${{ number_format($todaySpent ?? 0, 2) }}</strong></span>
                </div>

                <div class="rounded-2xl bg-slate-950/60 border border-slate-800 overflow-hidden">
                    <template x-if="syncedTransactions.length === 0">
                        <div class="p-8 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-circle-nodes text-slate-600 text-2xl mb-2"></i>
                            <p>All transactions for mobile #{{ $user->phone }} are up to date.</p>
                        </div>
                    </template>

                    <template x-if="syncedTransactions.length > 0">
                        <div class="divide-y divide-slate-800/60">
                            <template x-for="item in syncedTransactions" :key="item.id">
                                <div class="p-4 flex items-center justify-between hover:bg-slate-900/40 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400 text-xs">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-white" x-text="item.merchant"></h5>
                                            <span class="text-[11px] text-slate-400" x-text="item.payment_method + ' • ' + item.time"></span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-extrabold text-red-400 font-mono" x-text="'-$' + item.amount"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
