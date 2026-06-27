<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'teacher') {
                return redirect()->intended('/teacher/dashboard');
            }
            if ($user->role === 'student') {
                return redirect()->intended('/student/dashboard');
            }
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'આ ઇમેઇલ, GR નંબર અથવા પાસવર્ડ મેળ ખાતા નથી.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
