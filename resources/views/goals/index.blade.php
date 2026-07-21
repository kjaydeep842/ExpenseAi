<x-app-layout>
    <div x-data="{ goalModal: false, depositModal: false, selectedGoal: null }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Savings & Investment Goals</h1>
                <p class="text-xs text-slate-400">Track progress toward emergency funds, vacations, vehicle purchases, or housing deposits.</p>
            </div>

            <button @click="goalModal = true" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/25 hover:brightness-110 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Savings Goal
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($goals as $goal)
                @php
                    $percent = ($goal->target_amount > 0) ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;
                @endphp
                <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 hover:border-emerald-500/40 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-lg">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">{{ $goal->title }}</h3>
                                <p class="text-[11px] text-slate-400">Target Date: {{ $goal->deadline?->format('M Y') ?? 'Flexible' }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-extrabold">{{ $percent }}%</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Saved: <strong class="text-white">${{ number_format($goal->current_amount, 2) }}</strong></span>
                            <span class="text-slate-400">Goal: <strong class="text-white">${{ number_format($goal->target_amount, 2) }}</strong></span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    <button @click="selectedGoal = '{{ $goal->id }}'; depositModal = true" class="w-full py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-200 hover:border-emerald-500/50 hover:text-white transition">
                        + Deposit Funds
                    </button>
                </div>
            @empty
                <div class="col-span-3 glass-card rounded-3xl p-8 text-center text-slate-500">
                    No savings goals set yet.
                </div>
            @endforelse
        </div>

        <!-- Add Goal Modal -->
        <div x-show="goalModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="goalModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">New Goal Target</h3>
                    <button @click="goalModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('goals.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Goal Title</label>
                        <input type="text" name="title" required placeholder="e.g. Japan Vacation 2027" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Target Amount ($)</label>
                        <input type="number" step="1" name="target_amount" required placeholder="5000" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Target Date</label>
                        <input type="date" name="deadline" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 font-bold text-xs text-white">Create Goal</button>
                </form>
            </div>
        </div>

        <!-- Deposit Funds Modal -->
        <div x-show="depositModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="depositModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Deposit Savings Funds</h3>
                    <button @click="depositModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form :action="'/goals/' + selectedGoal + '/deposit'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Deposit Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="100.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 font-bold text-xs text-white">Confirm Deposit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
