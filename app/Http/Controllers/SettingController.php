<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $preference = UserPreference::firstOrCreate(['user_id' => $user->id]);

        return view('settings.index', compact('user', 'profile', 'preference'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
        ]);

        $user->update(['name' => $request->name, 'phone' => $request->phone]);

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            ['employment_type' => $request->employment_type, 'monthly_income_target' => $request->monthly_income_target]
        );

        return redirect()->back()->with('success', 'Profile settings updated!');
    }
}
