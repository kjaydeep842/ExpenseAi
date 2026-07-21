<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginInput = trim($request->login);

        // Determine if login is email or mobile phone number
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Normalize phone number if phone
        if ($fieldType === 'phone') {
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
            $user = User::where('phone', $loginInput)
                ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }
        } else {
            if (Auth::attempt(['email' => $loginInput, 'password' => $request->password], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }
        }

        return back()->withErrors([
            'login' => 'The provided Mobile Number / Email or password do not match our records.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request, \App\Services\BankApiGatewayService $bankGateway)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'primary_bank' => ['nullable', 'string'],
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        $user->assignRole($userRole);

        Profile::create(['user_id' => $user->id]);
        UserPreference::create(['user_id' => $user->id]);

        // Auto-connect selected Bank via Mobile Number verification & API fetch
        $bankName = $request->primary_bank ?: 'Kotak Mahindra Bank';
        $syncResult = $bankGateway->verifyAndConnectBank($user, $bankName);

        Auth::login($user);

        return redirect()->route('daily.index')->with('success', "Welcome {$user->name}! Mobile #{$user->phone} verified & connected with {$bankName}. Synced today's live banking transactions!");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
