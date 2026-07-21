<x-app-layout>
    <div x-data="{ budgetModal: false }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Smart Budget Limits & Alerting</h1>
                <p class="text-xs text-slate-400">Configure expenditure thresholds per category to receive automated warnings when spending exceeds limits.</p>
            </div>

            <button @click="budgetModal = true" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Set Budget Limit
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($budgets as $budget)
                @php
                    $percent = ($budget->amount > 0) ? min(100, round(($budget->spent / $budget->amount) * 100)) : 0;
                    $remaining = max(0, $budget->amount - $budget->spent);
                    $colorClass = $percent >= 90 ? 'bg-red-500 text-red-400 border-red-500/30' : ($percent >= 75 ? 'bg-amber-500 text-amber-400 border-amber-500/30' : 'bg-indigo-500 text-indigo-400 border-indigo-500/30');
                @endphp
                <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 hover:border-slate-700 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-lg">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">{{ $budget->category?->name ?? 'General' }}</h3>
                                <p class="text-[11px] text-slate-400 uppercase tracking-wide font-semibold">{{ $budget->period }} Cycle</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold border bg-slate-900 {{ $colorClass }}">
                            {{ $percent }}% Used
                        </span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Spent: <strong class="text-white">${{ number_format($budget->spent, 2) }}</strong></span>
                            <span class="text-slate-400">Limit: <strong class="text-white">${{ number_format($budget->amount, 2) }}</strong></span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $percent >= 90 ? 'bg-red-500' : ($percent >= 75 ? 'bg-amber-500' : 'bg-indigo-500') }}" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 text-right">Remaining headroom: <strong class="text-emerald-400">${{ number_format($remaining, 2) }}</strong></p>
                    </div>
                </div>
            @empty
                <div class="col-span-2 glass-card rounded-3xl p-8 text-center text-slate-500">
                    No budget limits set. Create your first category cap above!
                </div>
            @endforelse
        </div>

        <!-- Add Budget Modal -->
        <div x-show="budgetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="budgetModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Configure Category Budget</h3>
                    <button @click="budgetModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Target Category</label>
                        <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Budget Limit ($)</label>
                        <input type="number" step="1" name="amount" required placeholder="e.g. 500" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Cycle Period</label>
                        <select name="period" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white">Save Budget Rule</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
