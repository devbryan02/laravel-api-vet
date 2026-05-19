<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                "message" => "No autenticado.",
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (array_any($roles, fn($role) => $user->hasRole($role))) {
            return $next($request);
        }

        return response()->json([
            "message" => "No tienes permisos para acceder a este recurso.",
        ], Response::HTTP_FORBIDDEN);
    }
}
