<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVNDCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $hasVND = $user->currencies()->where('currency', 'VND')->exists();

            if (!$hasVND) {
                abort(403, 'Access denied. Only VND currency users allowed.');
            }
        }

        return $next($request);
    }
    
}
