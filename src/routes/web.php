<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ItemController, PurchaseController, CommentController, MyPageController, LikeController};

// トップ・商品（← guest を外す）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

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
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');



    // 購入ページ
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/checkout/{item}', [PurchaseController::class, 'checkout'])->name('purchase.store');
    // 住所変更ページ
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    // Stripe決済
    Route::get('/purchase/checkout/{item}', [\App\Http\Controllers\PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');
    Route::get('/purchase/success/{item}', [\App\Http\Controllers\PurchaseController::class, 'success'])
        ->name('purchase.success');
    // Route::get('/purchase/konbini/{item}', [PurchaseController::class, 'konbini'])->name('purchase.konbini');
    // Route::get('/purchase/konbini/success/{item}', [PurchaseController::class, 'konbiniSuccess'])->name('purchase.konbini.success');

    Route::get('/mypage/purchases', [MyPageController::class, 'purchases'])->name('mypage.purchases');
    Route::get('/mypage/sales', [MyPageController::class, 'sales'])->name('mypage.sales');
    Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::post('/item/{item}/like',   [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/item/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');
    // コンビニ支払い完了待ちページ
    Route::get('/purchase/pending/{item}', [PurchaseController::class, 'pending'])->name('purchase.pending');


    Route::post('/item/{item}/comments/prepare', [CommentController::class, 'prepare'])
        ->name('comments.prepare');
    // 一時住所登録（購入ページ用）
    Route::post('/purchase/address/temp/{item}', [PurchaseController::class, 'tempAddress'])
        ->name('purchase.address.temp');
});
