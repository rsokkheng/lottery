<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Supervisors (admin/master) and agents always go to admin panel
        if ($user->hasAnyRole(['admin', 'master', 'agent'])) {
            return redirect(RouteServiceProvider::HOME)->with('success', 'Login successfully.');
        }

        // Members: redirect based on their bet_system + currency columns
        if ($user->bet_system && $user->currency) {
            Session::put('currency', $user->currency);
            Session::put('bet_system', $user->bet_system);

            if ($user->bet_system === 'khmer' && $user->currency === 'USD') {
                return redirect()->route('bet-kh-usd.input')->with('success', 'Login successfully.');
            }
            if ($user->bet_system === 'khmer') {
                return redirect()->route('bet-kh-vnd.input')->with('success', 'Login successfully.');
            }
            if ($user->bet_system === 'vietnam' && $user->currency === 'USD') {
                return redirect()->route('bet-usd.input')->with('success', 'Login successfully.');
            }
            return redirect()->route('bet.input')->with('success', 'Login successfully.');
        }

        return redirect(RouteServiceProvider::HOME)->with('success', 'Login successfully.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
