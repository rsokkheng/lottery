<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckVNDCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $currency): Response
    {
        $user = auth()->user();
        if ($user) {
            $hasAccess = $user->currencies()->where('currency', strtoupper($currency))->exists();

            if (!$hasAccess) {
                Log::warning('Access denied: currency mismatch', [
                    'user_id' => $user->id,
                    'required_currency' => $currency,
                    'user_currencies' => $user->currencies()->pluck('currency')->toArray(),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);

                abort(403, "Access denied. Only {$currency} currency users allowed.");
            }

            // Optional: store active currency in session
            session(['currency' => strtoupper($currency)]);
        }

        return $next($request);
    }
    
}
