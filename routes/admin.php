<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubCateoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LotteryResultController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard',[ProfileController::class,'dashboard'])->name('dashboard');
    Route::get('/homepage',[ProfileController::class,'homepage'])->name('homepage');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['role:admin'])->group(function(){
        # <Menu>
        Route::resource('user',UserController::class);
        Route::resource('role',RoleController::class);
        Route::resource('permission',PermissionController::class);
        Route::resource('category',CategoryController::class);
        Route::resource('subcategory',SubCateoryController::class);
        Route::resource('collection',CollectionController::class);
        Route::resource('product',ProductController::class);
        Route::get('/get/subcategory',[ProductController::class,'getsubcategory'])->name('getsubcategory');
        Route::get('/remove-external-img/{id}',[ProductController::class,'removeImage'])->name('remove.image');


        Route::resource('lottery-result',LotteryResultController::class);

        Route::get('result/index-mien-nam',[LotteryResultController::class, 'indexMienNam'])->name('result.index-mien-nam');
        Route::get('result/create-mien-nam',[LotteryResultController::class, 'createMienNam'])->name('result.create-mien-nam');

        Route::get('result/index-mien-trung',[LotteryResultController::class, 'indexMienTrung'])->name('result.index-mien-trung');
        Route::get('result/create-mien-trung',[LotteryResultController::class, 'createMienTrung'])->name('result.create-mien-trung');

        Route::get('result/index-mien-bac',[LotteryResultController::class, 'indexMienBac'])->name('result.index-mien-bac');
        Route::get('result/create-mien-bac',[LotteryResultController::class, 'createMienBac'])->name('result.create-mien-bac');

        Route::post('result/store-winning-result',[LotteryResultController::class, 'storeWinningResult'])->name('result.store-winning-result');


        Route::get('result/get-bet-result/{date}/{region}',[LotteryResultController::class, 'getBetResultBy'])->name('result.index-get-bet-result');


        # <Language>
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
});
