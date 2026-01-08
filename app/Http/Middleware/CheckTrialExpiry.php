<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->role === 'barber' && $user->trial_ends_at && $user->trial_ends_at->isPast()) {
            // Allow access to shop settings so they can maybe see contact info or downgrade if needed?
            // For now, let's just block everything except maybe the logout route if we were using it in the group.
            // But we'll redirect them to a specific page.
            if (!$request->is('admin/trial-expired') && !$request->is('logout')) {
                return redirect()->route('admin.trial_expired');
            }
        }
        
        return $next($request);
    }
}
