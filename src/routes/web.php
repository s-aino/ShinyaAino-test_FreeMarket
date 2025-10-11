<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ItemController, PurchaseController, CommentController, MyPageController, LikeController};

// トップ・商品（← guest を外す）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// 認証が必要なページだけ auth で保護
Route::middleware('auth')->group(function () {
    // /home は“画面”にする（← ここで '/' に redirect しない）
    Route::get('/home', [ItemController::class, 'home'])->name('items.home');

    Route::get('/mypage', [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/profile', [MyPageController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [MyPageController::class, 'update'])->name('profile.update');
    Route::post('/profile', [MyPageController::class, 'update'])->name('profile.update.legacy');

    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');



    Route::get('/purchase/{item}',         [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}',         [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');



    Route::post('/sell', [ItemController::class, 'store']);
    Route::get('/mypage/purchases', [MyPageController::class, 'purchases'])->name('mypage.purchases');
    Route::get('/mypage/sales', [MyPageController::class, 'sales'])->name('mypage.sales');
    Route::post('/item/{item}/like',   [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/item/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');


    Route::post('/item/{item}/comments/prepare', [CommentController::class, 'prepare'])
        ->name('comments.prepare');
});
