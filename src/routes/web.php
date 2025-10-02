<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ItemController, PurchaseController, CommentController, MyPageController};

// トップ・商品
Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item}', [ItemController::class, 'show']);

// 認証が必要なページだけを auth で守る
Route::middleware('auth')->group(function () {
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell', [ItemController::class, 'store']);

    Route::post('/item/{item}/comments', [CommentController::class, 'store']);

    Route::get('/purchase/{item}', [PurchaseController::class, 'create']);
    Route::post('/purchase/{item}', [PurchaseController::class, 'store']);
    Route::get('/mypage',            [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/purchases',  [MyPageController::class, 'purchases'])->name('mypage.purchases');
    Route::get('/mypage/sales',      [MyPageController::class, 'sales'])->name('mypage.sales');

    Route::get('/profile/edit', [MyPageController::class, 'edit'])->name('profile.edit');
    Route::post('/profile',      [MyPageController::class, 'update'])->name('profile.update');

    Route::get('/dev-logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->middleware('auth');
});



// ※ /login /register は書かない（Fortify が自動で用意）
