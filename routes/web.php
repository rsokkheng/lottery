<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BetController;
use App\Http\Controllers\BetUSDController;
use App\Http\Controllers\BetReportController;
use App\Http\Controllers\BetReceiptController;
use App\Http\Controllers\BetReportUSDController;
use App\Http\Controllers\LoginWithOTPController;
use App\Http\Controllers\BetReceiptUSDController;
use App\Http\Controllers\LotteryResultController;
use App\Http\Controllers\LotteryResultUSDController;
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

Route::middleware(['auth', 'verified', 'check.vnd:VND'])->prefix('lotto_vn')->group(function () {
    Route::get('/bet', \App\Livewire\LottoBet::class)->middleware('role:member')->name('bet.input');
    Route::get('/result', [LotteryResultController::class, 'getBetResultBy'])->name('bet.result-show');
    Route::get('/receipt-list', [BetReceiptController::class, 'index'])->name('bet.receipt-list');
    Route::get('/bet-list', [BetReceiptController::class, 'betList'])->name('bet.bet-list');
    Route::get('/bet-number', [BetController::class, 'getBetNumber'])->name('bet.bet-number');
    Route::get('/bet-winning', [LotteryResultController::class, 'getWinningReport'])->name('bet.bet-winning');
    Route::get('/report-summary', [BetReportController::class, 'getSummaryReport'])->name('reports.summary');
    Route::get('/report-daily', [BetReportController::class, 'getDailyReport'])->name('reports.daily');
    Route::get('/report-daily/sumary', [BetReportController::class, 'getDailyReportManager'])->name('reports.daily-manager');
    Route::get('/report-daily/agenct/{id}', [BetReportController::class, 'getDailyReportMeberAgent'])->name('reports.daily-member-agent');
    Route::get('/bet/{id}', [BetReceiptController::class, 'getBetByReceiptId'])->name('bet.bet-by-id');
    Route::get('/bet_receipt/{receipt_no}', [BetReceiptController::class, 'printReceiptNo']);
    Route::get('/bet_receipt_pay/{receipt_no}', [BetReceiptController::class, 'payReceipt']);
});

Route::middleware(['auth', 'verified', 'check.usd:USD'])->prefix('lotto_usd')->group(function () {
    Route::get('/bet', \App\Livewire\LottoBetUSD::class)->middleware('role:member')->name('bet-usd.input');
    Route::get('/result', [LotteryResultUSDController::class, 'getBetResultBy'])->name('bet-usd.result-show');
    Route::get('/receipt-list', [BetReceiptUSDController::class, 'index'])->name('bet-usd.receipt-list');
    Route::get('/bet-list', [BetReceiptUSDController::class, 'betList'])->name('bet-usd.bet-list');
    Route::get('/bet-number', [BetUSDController::class, 'getBetNumber'])->name('bet-usd.bet-number');
    Route::get('/bet-winning', [LotteryResultUSDController::class, 'getWinningReport'])->name('bet-usd.bet-winning');
    Route::get('/report-summary', [BetReportUSDController::class, 'getSummaryReport'])->name('bet-usd.reports.summary');
    Route::get('/report-daily', [BetReportUSDController::class, 'getDailyReport'])->name('bet-usd.reports.daily');
    Route::get('/report-daily/sumary', [BetReportUSDController::class, 'getDailyReportManager'])->name('bet-usd.reports.daily-manager');
    Route::get('/report-daily/agenct/{id}', [BetReportUSDController::class, 'getDailyReportMeberAgent'])->name('bet-usd.reports.daily-member-agent');
    Route::get('/bet/{id}', [BetReceiptUSDController::class, 'getBetByReceiptId'])->name('bet-usd.bet-by-id');
    Route::get('/bet_receipt/{receipt_no}', [BetReceiptUSDController::class, 'printReceiptNo']);
    Route::get('/bet_receipt_pay/{receipt_no}', [BetReceiptUSDController::class, 'payReceipt']);
});







// Auth routes
require __DIR__.'/auth.php';
// Admin Routes
require('admin.php');
