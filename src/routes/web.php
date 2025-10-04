<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ItemController, PurchaseController, CommentController, MyPageController};

// トップ・商品
Route::get('/', [ItemController::class, 'index'])
    ->middleware('guest')
    ->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
// 認証が必要なページだけを auth で守る
Route::middleware('auth')->group(function () {
    // ログイン後のトップ
    Route::get('/home', [ItemController::class, 'home'])
        ->name('items.home');
    
    Route::get('/mypage',            [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/profile', [MyPageController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [MyPageController::class, 'update'])->name('profile.update');
    Route::post('/profile',          [MyPageController::class, 'update'])->name('profile.update.legacy'); // 互換

    Route::post('/item/{item}/comments', [CommentController::class, 'store']);
    Route::get('/purchase/{item}', [PurchaseController::class, 'create']);
    Route::post('/purchase/{item}', [PurchaseController::class, 'store']);
    Route::get('/mypage/purchases',  [MyPageController::class, 'purchases'])->name('mypage.purchases');
    Route::get('/mypage/sales',      [MyPageController::class, 'sales'])->name('mypage.sales');


    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store']);
});



// ※ /login /register は書かない（Fortify が自動で用意）
