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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if the user has the required role
        if (!$user->hasRole($role)) {
            // If the user doesn't have the required role, redirect them based on their actual primary role.
            // Note: This assumes a user primarily belongs to one main role for dashboard access.
            // You might need more complex logic if users can have multiple significant roles simultaneously.
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('talent')) {
                return redirect()->route('talent.dashboard');
            } elseif ($user->hasRole('user')) {
                return redirect()->route('dashboard');
            }

            // Fallback if no primary role dashboard is found or role is unexpected
            Auth::logout();
            return redirect('login')->with('error', 'Unauthorized access or role mismatch.');
        }

        return $next($request);
    }
}
