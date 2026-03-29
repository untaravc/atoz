<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $role = null;
        if (is_numeric($user->role_id) && (int) $user->role_id > 0) {
            $role = Role::query()->find((int) $user->role_id);
        }

        $token = JwtToken::encode([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role_id' => $user->role_id ?? 0,
            'user_role' => $role?->name,
        ], (int) env('JWT_TTL', 60 * 60 * 24 * 7));

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $role?->name,
            ],
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $role = $request->attributes->get('auth_role');

        return response()->json([
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'role_id' => $user?->role_id,
                'role' => $role?->name,
            ],
        ]);
    }

    public function logout()
    {
        return response()->json(['logged_out' => true]);
    }

    public function genPassword()
    {
        return response()->json([
            'password' => bcrypt('password'),
        ]);
    }
}

