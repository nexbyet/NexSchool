<?php

// NexSchool - Register Controller
// New user registration (web)
// ગુજરાતી: નવા વપરાશકર્તાની નોંધણી

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Registration form page બતાવો
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Registration form submit કરો - નવો યુઝર બનાવો અને લોગિન કરો
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Default role: admin (can be changed later)
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
