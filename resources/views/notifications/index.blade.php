<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Smart Notifications & Alerts</h1>
                <p class="text-xs text-slate-400">Budget warnings, transaction anomalies, and subscription renewal alerts.</p>
            </div>

            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white">
                    Mark All as Read
                </button>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $note)
                <div class="glass-card rounded-2xl p-4 border border-slate-800 flex items-start gap-4 {{ $note->status === 'unread' ? 'border-indigo-500/40 bg-indigo-950/10' : '' }}">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h4 class="font-bold text-slate-200 text-xs">{{ $note->title }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $note->message }}</p>
                        <span class="text-[10px] text-slate-500">{{ $note->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="glass-card rounded-3xl p-8 text-center text-slate-500">
                    No new alerts or notifications.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
