<x-app-layout>
    <div x-data="{ subModal: false }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Recurring Subscriptions Engine</h1>
                <p class="text-xs text-slate-400">Detect and manage recurring SaaS, streaming, and utility subscriptions.</p>
            </div>

            <button @click="subModal = true" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Track Subscription
            </button>
        </div>

        <!-- Monthly Outflow Banner -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-slate-400 uppercase">Total Subscription Outflow</span>
                <h2 class="text-3xl font-extrabold text-white">${{ number_format($totalMonthlyCost, 2) }} <span class="text-xs text-slate-500 font-normal">/ month</span></h2>
            </div>
            <div class="px-4 py-2 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-300 text-xs font-bold">
                <i class="fa-solid fa-rotate mr-1"></i> {{ count($subscriptions) }} Active Subscriptions
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($subscriptions as $sub)
                <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 hover:border-indigo-500/40 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-lg">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">{{ $sub->name }}</h3>
                                <p class="text-[11px] text-slate-400 uppercase font-semibold">{{ $sub->billing_cycle }}</p>
                            </div>
                        </div>
                        <span class="text-lg font-extrabold text-white">${{ number_format($sub->amount, 2) }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Next Charge:</span>
                        <span class="font-bold text-slate-200">{{ $sub->next_billing_date?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 glass-card rounded-3xl p-8 text-center text-slate-500">
                    No recurring subscriptions being tracked.
                </div>
            @endforelse
        </div>

        <!-- Add Modal -->
        <div x-show="subModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="subModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Track New Subscription</h3>
                    <button @click="subModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('subscriptions.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Service / Merchant Name</label>
                        <input type="text" name="name" required placeholder="e.g. Netflix Ultra 4K" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Billing Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="19.99" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Billing Cycle</label>
                        <select name="billing_cycle" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Next Billing Date</label>
                        <input type="date" name="next_billing_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-purple-600 font-bold text-xs text-white">Save Subscription</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
