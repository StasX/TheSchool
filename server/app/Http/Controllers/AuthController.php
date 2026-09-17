<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        if (
            ! (is_string($email) &&
            filter_var($email, FILTER_VALIDATE_EMAIL) &&
            is_string($password) &&
            $password !== '')
        ) {
            return response()->json([
                'error' => 'Invalid username or password.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $users = Administrator::where('Email', $email)->get();
        if ($users->count() !== 1) {
            return response()->json([
                'error' => 'Invalid username or password.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = $users->first();

        if (! $user || ! Hash::check($password, $user->Password)) {
            return response()->json([
                'error' => 'Invalid username or password.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'administrator' => [
                'id' => $user->Administrator_ID,
                'email' => $user->Email,
                'name' => $user->Name,
                'role' => $user->Role,
                'image' => $user->Image,
            ],
            'token' => csrf_token(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    public function auth(): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $administrator = Auth::user();

        if (! $administrator instanceof Administrator) {
            return response()->json([
                'error' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'id' => $administrator->Administrator_ID,
            'email' => $administrator->Email,
            'name' => $administrator->Name,
            'role' => $administrator->Role,
            'image' => $administrator->Image,
        ]);
    }
}
