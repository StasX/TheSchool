<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'Email' => ['required', 'email'],
            'Password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'Email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $administrator = Auth::user();

        return response()->json([
            'message' => 'Login successful',
            'administrator' => [
                'Administrator_ID' => $administrator->Administrator_ID,
                'Email' => $administrator->Email,
                'Name' => $administrator->Name,
                'Role' => $administrator->Role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
