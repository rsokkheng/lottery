<?php

use App\Http\Controllers\AccountReportController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BetReportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BetReportUSDController;
use App\Http\Controllers\BalanceReportController;
use App\Http\Controllers\LotteryResultController;
use App\Http\Controllers\LotteryResultUSDController;
use App\Http\Controllers\BetLotteryPackageController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard',[ProfileController::class,'dashboard'])->name('dashboard');
    Route::get('/homepage',[ProfileController::class,'homepage'])->name('homepage');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['role:admin|manager'])->group(function(){
        Route::resource('menu',MenuController::class);
        Route::resource('balance-report',BalanceReportController::class);
        Route::get('balance-report/detail/{user_id}',[BalanceReportController::class, 'detailByUser'])->name('balance-report.detail');
        Route::post('admin/user/setting', [UserController::class, 'saveSetting'])->name('user.setting');
        Route::resource('user',UserController::class);
        Route::get('user/{user}/change-password', [UserController::class, 'editPassword'])->name('user.change-password');
        Route::put('user/{user}/change-password', [UserController::class, 'updatePassword'])->name('user.update-password');
        Route::get('/users/under-manager/{manager_id}', [UserController::class, 'usersUnderManager'])->name('user.under-manager');
        Route::resource('role',RoleController::class);
        Route::resource('permission',PermissionController::class);
        Route::resource('bet-lottery-package',BetLotteryPackageController::class);
        Route::resource('lottery-result',LotteryResultController::class);
        Route::post('/balance/report/transaction', [BalanceReportController::class, 'handleTransaction'])->name('balance-report.transaction');
        Route::get('result/index-mien-nam',[LotteryResultController::class, 'indexMienNam'])->name('result.index-mien-nam');
        Route::get('result/create-mien-nam',[LotteryResultController::class, 'createMienNam'])->name('result.create-mien-nam');
        Route::get('result/index-mien-trung',[LotteryResultController::class, 'indexMienTrung'])->name('result.index-mien-trung');
        Route::get('result/create-mien-trung',[LotteryResultController::class, 'createMienTrung'])->name('result.create-mien-trung');
        Route::get('result/index-mien-bac',[LotteryResultController::class, 'indexMienBac'])->name('result.index-mien-bac');
        Route::get('result/create-mien-bac',[LotteryResultController::class, 'createMienBac'])->name('result.create-mien-bac');
        Route::post('result/store-winning-result-vnd',[LotteryResultController::class, 'storeWinningResult'])->name('result.store-winning-result');
        Route::post('result/store-winning-result-usd',[LotteryResultUSDController::class, 'storeWinningResult'])->name('result.store-winning-result-usd');
        Route::get('result/get-bet-result/{date}/{region}',[LotteryResultController::class, 'getBetResultBy'])->name('result.index-get-bet-result');
        Route::get('generate-win-result',[LotteryResultController::class, 'callGenerateWinNumber']);
        Route::post('result/store-winning-record',[\App\Http\Controllers\WinningRecordController::class, 'storeWinningRecord'])->name('result.store-winning-record');

        Route::get('/set-lang/{locale}', function (string $locale) {
            try {
                if (! in_array($locale, ['en', 'vi'])) {
                    abort(400);
                }
                App::setLocale($locale);
                Session::put('locale', $locale);
            }catch (Exception $ex){
                \PHPUnit\Framework\throwException($ex);
                return 'Language does not exist!';
            }
        });
        Route::get('/get-lang', function () {
            App::getLocale();
            Session::get('locale');
        });

    });
    Route::middleware(['role:admin'])->group(function(){
        Route::get('/report-daily/report-vnd', [BetReportController::class, 'getDailyReportVND'])->name('report.index');
        Route::get('/report-daily/report-usd', [BetReportUSDController::class, 'getDailyReportUSD'])->name('report.daily-usd');
        Route::get('/account-report/transaction-vnd', [AccountReportController::class, 'index'])->name('account-report.index');
        Route::get('/account-report/transation-usd', [AccountReportController::class, 'transactionUSD'])->name('account-report.transation-usd');
    });
});
