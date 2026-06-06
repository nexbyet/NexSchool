<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required_without:email',
            'email' => 'required_without:login|email',
            'password' => 'required',
        ]);

        $login = $request->input('login') ?? $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['આ ઇમેઇલ, GR નંબર અથવા પાસવર્ડ મેળ ખાતા નથી.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $response = ['user' => $user, 'token' => $token];

        if ($user->role === 'parent') {
            $response['students'] = \App\Models\Student::where('mobile', $user->parent_mobile)
                ->with(['admissionStandard', 'currentStandard'])->defaultSort()->get();
        }

        return response()->json($response);
    }

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
            'role' => 'admin',
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 201);
    }

    public function user(Request $request)
    {
        $user = $request->user()->load('student');
        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'લોગઆઉટ સફળ']);
    }
}
