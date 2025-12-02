<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar que el usuario esté activo.
 *
 * Los usuarios inactivos (is_active = false) son deslogueados automáticamente
 * y redirigidos al login con un mensaje de error.
 */
class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Verificar si está activo
            if (! $user->is_active) {
                // Desloguear al usuario
                Auth::logout();

                // Invalidar sesión
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirigir al login con mensaje
                return redirect()->route('login')
                    ->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
            }
        }

        return $next($request);
    }
}
