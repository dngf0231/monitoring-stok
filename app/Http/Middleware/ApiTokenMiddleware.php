<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json(['message' => 'Bearer token wajib diisi.'], 401);
        }

        $token = ApiToken::with('user')->where('token', hash('sha256', $plainToken))->first();

        if (!$token || !$token->user || !$token->user->isActive()) {
            return response()->json(['message' => 'Token tidak valid atau akun belum aktif.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        Auth::setUser($token->user);

        return $next($request);
    }
}
