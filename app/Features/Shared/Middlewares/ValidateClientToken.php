<?php

declare(strict_types=1);

namespace Features\Shared\Middlewares;

use Closure;
use Features\Token\Models\Token;
use Illuminate\Http\Request;

class ValidateClientToken
{
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->bearerToken(); // Get token from Authorization: Bearer <token>

        if (! $token) {
            return response()->json(['error' => 'No token provided'], 401);
        }

        $token = Token::where('token', $token)
            ->first();

        if (! $token || ! $token->user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $user = $token->user;

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
