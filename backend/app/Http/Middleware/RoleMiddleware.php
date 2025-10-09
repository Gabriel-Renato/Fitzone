<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autenticado'
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.'
            ], 403);
        }

        return $next($request);
    }
}