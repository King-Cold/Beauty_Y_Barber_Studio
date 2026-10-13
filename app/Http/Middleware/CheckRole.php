<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si el usuario es administrador (rol 1), siempre tiene acceso a todo.
        if ((int)$user->role_id === 1) {
            return $next($request);
        }

        // Verifica si el rol del usuario está dentro de los roles permitidos para esta ruta
        if (in_array((string) $user->role_id, $roles)) {
            return $next($request);
        }

        // Si no tiene permiso y la petición es AJAX / JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para acceder a esta página.',
            ], 403);
        }

        // Si es una petición web convencional desde el navegador, abortar con error 403 (Prohibido)
        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
