<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\{ItemController, PurchaseController, CommentController, MyPageController, LikeController};

//  トップ・商品一覧（ゲストも閲覧可）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// 認証必須ページ（auth+verified）
Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ関連
    Route::get('/mypage', [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/profile', [MyPageController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [MyPageController::class, 'update'])->name('profile.update');

    // 出品関連
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
    Route::get('/sell/success', [ItemController::class, 'success'])->name('sell.success');

    // コメント関連
    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    // いいね機能
    Route::post('/item/{item}/like',   [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/item/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');

    // 購入関連
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/checkout/{item}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/success/{item}', [PurchaseController::class, 'success'])->name('purchase.success');

    // 住所登録
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');
});
//  メール認証関連ルート
// ログイン済みユーザー専用
Route::middleware('auth')->group(function () {

    //  メール認証待ち画面
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // メール認証画面（「認証はこちらから」ボタンで遷移）
    Route::get('/email/verify/guide', function () {
        return view('auth.verify-guide');
    })->name('verification.guide');

    //  認証メール再送信
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました。');
    })->middleware('throttle:6,1')->name('verification.send');
});

// 認証リンクをクリックしたときの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // email_verified_at に時刻を記録
    return redirect('mypage/profile')->with('message', 'メール認証が完了しました！'); // 認証完了後にプロフィールページへ
})->middleware(['auth', 'signed'])->name('verification.verify');
