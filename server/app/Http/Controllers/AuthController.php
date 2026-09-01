<?php
namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (
            ! $request->filled('Email') ||
            ! filter_var($request->Email, FILTER_VALIDATE_EMAIL) ||
            ! $request->filled('Password')
        ) {
            return response()->json([
                'error' => 'Invalid username or password.',
            ], 401);
        }

        $user = Administrator::where('Email', $request->Email)->first();

        if (! $user || ! Hash::check($request->Password, $user->Password)) {
            return response()->json([
                'error' => 'Invalid username or password.',
            ], 401);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'administrator' => [
                'Administrator_ID' => $user->Administrator_ID,
                'Email'            => $user->Email,
                'Name'             => $user->Name,
                'Role'             => $user->Role,
                'Image'            => $user->Image,
            ],
            'token'         => csrf_token(),
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

    public function auth()
    {
        if (! Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        $administrator = Auth::user();

        return response()->json([
            'Administrator_ID' => $administrator->Administrator_ID,
            'Email'            => $administrator->Email,
            'Name'             => $administrator->Name,
            'Role'             => $administrator->Role,
            'Phone'            => $administrator->Phone,
            'Image'            => $administrator->Image,
        ]);
    }
}
