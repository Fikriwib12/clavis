<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfTeamRegistered
{
    /**
     * Send users who already registered a team to the finish page instead of the registration form.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->hasRegisteredTeam()) {
            return redirect()->route('tournament.finish');
        }

        return $next($request);
    }
}
