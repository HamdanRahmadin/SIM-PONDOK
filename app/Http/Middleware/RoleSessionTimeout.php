<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleSessionTimeout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->role;

            // Timeout dalam detik:
            // Ustaz: 480 menit = 28.800 detik
            // Admin/Bendahara: 120 menit = 7.200 detik
            $timeout = ($role === 'ustaz') ? 28800 : 7200;

            $lastActivity = session('last_activity_timestamp');
            $currentTime = time();

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas.');
            }

            session(['last_activity_timestamp' => $currentTime]);
        }

        return $next($request);
    }
}
