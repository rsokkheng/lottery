<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CheckKHCurrency
{
    public function handle(Request $request, Closure $next, string $currency): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            $khRoutePrefix = 'bet-kh-' . strtolower($currency);
            session(['currency' => strtoupper($currency), 'bet_system' => 'khmer']);
            View::share('khRoutePrefix', $khRoutePrefix);

            if ($user->hasAnyRole(['admin', 'master'])) {
                return $next($request);
            }

            $hasAccess = $user->bet_system === 'khmer'
                      && $user->currency   === strtoupper($currency);

            if (!$hasAccess) {
                abort(403, 'Access denied. You do not have Bet Khmer · ' . strtoupper($currency) . ' access.');
            }
        }

        return $next($request);
    }
}
