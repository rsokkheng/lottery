<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AccountKH;
use App\Models\AccountKHUSD;
use App\Models\AccountUSD;
use App\Models\AccountVND;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure both accounts exist for currency-based dual-system access
        if ($user && $user->currency) {
            $uid = $user->id;
            if ($user->currency === 'VND') {
                AccountVND::firstOrCreate(['user_id' => $uid], ['credit_balance' => 0, 'record_status_id' => 1, 'created_by' => $uid]);
                AccountKH::firstOrCreate(['user_id' => $uid],  ['credit_balance' => 0, 'record_status_id' => 1, 'created_by' => $uid]);
            } elseif ($user->currency === 'USD') {
                AccountUSD::firstOrCreate(['user_id' => $uid],    ['credit_balance' => 0, 'record_status_id' => 1, 'created_by' => $uid]);
                AccountKHUSD::firstOrCreate(['user_id' => $uid],  ['credit_balance' => 0, 'record_status_id' => 1, 'created_by' => $uid]);
            }
        }

        return view('dashboard');
    }
    public function homepage()
    {
        return view('homepage');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // dd($request->post());
        // dd($request->user());
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        User::where('id', $request->user()->id)->update(['mode'=>$request->mode]);

        $request->user()->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
}
