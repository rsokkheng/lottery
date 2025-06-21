<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUSDCurrency
{
    public function handle(Request $request, Closure $next, string $expectedCurrency): Response
    {
        $user = auth()->user();
        if ($user) {
            $hasAccess = $user->currencies()
                ->where('currency', strtoupper($expectedCurrency))
                ->exists();

            if (!$hasAccess) {
                Log::warning('Currency access denied', [
                    'user_id' => $user->id,
                    'attempted_currency' => $expectedCurrency,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ]);

                abort(403, "Access denied. Only {$expectedCurrency} currency users allowed.");
            }

            // Optional: Set shared session for current currency
            session(['currency' => strtoupper($expectedCurrency)]);
        }

        return $next($request);
    }
}
