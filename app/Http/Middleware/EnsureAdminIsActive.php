<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Belt-and-braces check alongside User::canAccessPanel(). If an admin's
 * is_active flag is flipped to false mid-session, this forces them out
 * immediately rather than waiting for their session to expire naturally.
 */
class EnsureAdminIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/admin/login')->withErrors([
                'email' => 'This account has been deactivated.',
            ]);
        }

        return $next($request);
    }
}
