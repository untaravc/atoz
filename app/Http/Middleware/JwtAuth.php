<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use App\Support\JwtToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JwtAuth
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');
        if (!is_string($header) || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $payload = JwtToken::decode($token);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $userId = $payload['user_id'] ?? null;
        $userEmail = $payload['user_email'] ?? null;

        if (!is_numeric($userId) || !is_string($userEmail)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = User::query()->find((int) $userId);
        if (!$user || $user->email !== $userEmail) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $role = null;
        if (is_numeric($user->role_id) && (int) $user->role_id > 0) {
            $role = Role::query()->find((int) $user->role_id);
        }

        Auth::setUser($user);
        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_role', $role);

        return $next($request);
    }
}

