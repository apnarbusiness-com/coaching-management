<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json') {
            return null;
        }
        return route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => 'Unauthenticated'
            ], JsonResponse::HTTP_UNAUTHORIZED));
        }

        parent::unauthenticated($request, $guards);
    }
}
