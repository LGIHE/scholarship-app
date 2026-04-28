<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicantOrScholar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasRole('Committee')) {
            return redirect('/admin');
        }

        if (! $request->user() || ! $request->user()->hasAnyRole(['Applicant', 'Scholar'])) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
