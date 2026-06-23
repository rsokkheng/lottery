<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUSDCurrency
{
    public function handle(Request $request, Closure $next, string $currency): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            session(['currency' => strtoupper($currency), 'bet_system' => 'vietnam']);

            if ($user->hasAnyRole(['admin', 'master'])) {
                return $next($request);
            }

            $hasAccess = $user->bet_system === 'vietnam'
                      && $user->currency   === strtoupper($currency);

            if (!$hasAccess) {
                abort(403, 'Access denied. You do not have Bet Vietnam · ' . strtoupper($currency) . ' access.');
            }
        }

        return $next($request);
    }
}
