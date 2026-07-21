<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white">System Admin Command Center</h1>
                <p class="text-xs text-slate-400">Global SaaS tenant analytics, activity logs, user security, and AI usage metrics.</p>
            </div>
        </div>

        <!-- Global System Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-2">
                <span class="text-xs font-semibold text-slate-400 uppercase">Registered Users</span>
                <h2 class="text-3xl font-extrabold text-white">{{ number_format($totalUsers) }}</h2>
            </div>
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-2">
                <span class="text-xs font-semibold text-slate-400 uppercase">System Transactions</span>
                <h2 class="text-3xl font-extrabold text-indigo-400">{{ number_format($totalTransactions) }}</h2>
            </div>
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-2">
                <span class="text-xs font-semibold text-slate-400 uppercase">AI Queries Processed</span>
                <h2 class="text-3xl font-extrabold text-amber-400">{{ number_format($totalAiQueries) }}</h2>
            </div>
        </div>

        <!-- User Directory -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
            <h3 class="font-extrabold text-white text-base">User Directory & RBAC Status</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($recentUsers as $u)
                            <tr>
                                <td class="py-3.5 px-4 font-bold text-white">{{ $u->name }}</td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $u->email }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $u->role === 'admin' ? 'bg-purple-500/20 text-purple-300' : 'bg-slate-800 text-slate-300' }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $u->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
