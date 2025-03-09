<?php

use App\Http\Controllers\LoginWithOTPController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('login');
})->name('login');

// Login with OTP Routes
Route::prefix('/otp')->middleware( 'guest')->name('otp.')->controller(LoginWithOTPController::class)->group(function(){
    Route::get('/login','login')->name('login');
    Route::post('/generate','generate')->name('generate');
    Route::get('/verification/{userId}','verification')->name('verification');
    Route::post('login/verification','loginWithOtp')->name('loginWithOtp');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lotto_vn/bet', \App\Livewire\LottoBet::class)->name('bet.input');
    Route::get('/lotto_vn/result', [\App\Http\Controllers\LotteryResultController::class, 'getBetResultBy'])->name('bet.result-show');
    Route::get('/lotto_vn/receipt-list', [\App\Http\Controllers\BetReceiptController::class, 'index'])->name('bet.receipt-list');

});


// Auth routes
require __DIR__.'/auth.php';
// Admin Routes
require('admin.php');
