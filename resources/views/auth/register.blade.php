<x-guest-layout>
    <div class="min-h-[85vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6">
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 mb-2">
                    <i class="fa-solid fa-user-plus text-xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Create Account</h2>
                <p class="text-xs text-slate-400">Join 50,000+ users tracking expenses with Gemini AI</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition" placeholder="e.g. Alex Morgan">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition" placeholder="alex@company.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-600 to-indigo-600 font-bold text-sm text-white shadow-lg shadow-emerald-500/25 hover:brightness-110 transition">
                    Start 14-Day Free Trial
                </button>
            </form>

            <p class="text-center text-xs text-slate-400 pt-2">
                Already have an account? <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</x-guest-layout>
