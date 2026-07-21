<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-indigo-500 text-white flex items-center justify-center text-xl shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-sparkles"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white">Ask Gemini AI Assistant</h1>
                <p class="text-xs text-slate-400">Natural language financial analyst trained on your transaction ledger.</p>
            </div>
        </div>

        <!-- Latest AI Response Card -->
        @if(session('latest_answer'))
            <div class="glass-card rounded-3xl p-6 border border-amber-500/30 bg-gradient-to-b from-amber-950/20 to-slate-950 space-y-3">
                <div class="flex items-center gap-2 text-amber-400 text-xs font-bold uppercase">
                    <i class="fa-solid fa-brain"></i> Response for: "{{ session('latest_query') }}"
                </div>
                <div class="text-xs text-slate-200 leading-relaxed font-medium">
                    {!! Str::markdown(session('latest_answer')) !!}
                </div>
            </div>
        @endif

        <!-- Prompt Console Input -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
            <form method="POST" action="{{ route('ai.ask') }}" class="space-y-4">
                @csrf
                <div class="relative">
                    <textarea name="prompt" rows="3" required placeholder="Ask anything, e.g.: 'How much did I spend on food this month?' or 'Suggest savings tips for my budget'" class="w-full p-4 rounded-2xl bg-slate-900 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"></textarea>
                    <button type="submit" class="absolute bottom-3 right-3 px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-indigo-600 font-bold text-xs text-white shadow-lg">
                        Ask Gemini <i class="fa-solid fa-paper-plane ml-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Previous Queries Log -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase">Recent AI Conversations</h3>
            @forelse($aiLogs as $log)
                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-2">
                    <p class="text-xs font-bold text-amber-300">Q: "{{ $log->prompt }}"</p>
                    <div class="text-[11px] text-slate-400">
                        {!! Str::markdown(Str::limit($log->response, 250)) !!}
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500">No previous questions asked yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
