<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            // Not logged in
            return redirect('login');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            // Redirect based on their actual role if they try to access the wrong dashboard
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($user->role === 'talent') {
                return redirect('/talent/dashboard'); // Assuming '/talent/dashboard' is the talent dashboard
            } elseif ($user->role === 'user') {
                return redirect('/dashboard'); // Assuming '/dashboard' is the user dashboard
            }
            // Fallback if role is somehow unexpected (shouldn't happen with enum)
            Auth::logout();
            return redirect('login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
