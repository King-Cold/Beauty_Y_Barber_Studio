<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionInactivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Tiempo límite de inactividad: 5 minutos (300 segundos)
            $timeoutSeconds = 5 * 60;
            $lastActivity = session('last_activity_time');

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tu sesión se ha cerrado por 5 minutos de inactividad.',
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->with('warning', 'Tu sesión se ha cerrado automáticamente por 5 minutos de inactividad.');
            }

            // Actualizar la marca de tiempo de la última actividad del usuario
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
