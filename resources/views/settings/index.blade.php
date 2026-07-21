<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Account Settings & Security</h1>
            <p class="text-xs text-slate-400">Manage user profile preferences, employment targets, and 2FA credentials.</p>
        </div>

        <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-6">
            <h3 class="font-extrabold text-white text-base">Profile Information</h3>

            <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Monthly Income Target ($)</label>
                        <input type="number" step="100" name="monthly_income_target" value="{{ $profile->monthly_income_target }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                </div>

                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white">Save Profile Changes</button>
            </form>
        </div>
    </div>
</x-app-layout>
