<?php

namespace App\Http\Middleware;

use Closure;

/**
 * HTTP middleware for ensuring the currently logged-in user is in a specific role.
 */
class CheckRole
{
    /**
     * Handles the incoming request.
     *
     * Checks if the user's role matches the specified accepted role and redirects to the home page if not.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $acceptedRole The expected role of the user.
     * @return mixed
     */
    public function handle($request, Closure $next, $acceptedRole)
    {
        if ($request->user()->role != $acceptedRole) {
            return redirect("/");
        }

        return $next($request);
    }
}