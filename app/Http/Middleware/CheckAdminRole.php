<?php // This should already be at the top of the file

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Make sure this line is present or add it
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if a user is authenticated AND if they are an admin.
        // The isAdmin() method should exist in your User model (app/Models/User.php).
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            // If not an admin, redirect them.
            // If you decide to make /dashboard admin-only, redirect non-admins to the homepage ('/').
            // If you have a separate /admin/dashboard, you might redirect to /dashboard (the user one).
            // For now, let's assume we'll redirect to the homepage for non-admins trying to access admin areas.
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request); // If they are an admin, allow the request to proceed.
    }
}