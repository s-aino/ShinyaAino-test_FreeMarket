🎯 目的

購入画面の「購入する」ボタン押下後に、Stripeの決済画面（Checkout）へ遷移できるようにする。
※本日は カード支払いのみ対応。
コンビニ支払い・注文データ保存は次ステップで実施予定。

🧩 手順一覧
手順	内容	所要時間目安
①	Stripe SDK導入	10分
②	APIキー設定	10分
③	コントローラ編集	50分
④	ルート・ビュー追加	30分
⑤	動作確認	20分
① Stripe SDK 導入
composer require stripe/stripe-php

② .env にAPIキーを追記

Stripe公式にログイン →
https://dashboard.stripe.com/test/apikeys

下記2行を .env に追加（既存の末尾でOK）：

STRIPE_SECRET_KEY=sk_test_****************************
STRIPE_PUBLIC_KEY=pk_test_****************************


保存後、反映：

php artisan config:clear

③ PurchaseController に決済処理を追加
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

public function store(Request $request, Item $item)
{
    // Stripe初期化
    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    // Checkoutセッションを作成
    $session = StripeSession::create([
        'payment_method_types' => ['card'], // ← 今回はカードのみ
        'line_items' => [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->name,
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('purchase.success'),
        'cancel_url'  => route('purchase.cancel'),
    ]);

    // Stripe決済画面へリダイレクト
    return redirect($session->url);
}

④ ルートとビューを追加
routes/web.php
// 決済成功・キャンセル
Route::get('/purchase/success', function () {
    return view('purchase.success');
})->name('purchase.success');

Route::get('/purchase/cancel', function () {
    return view('purchase.cancel');
})->name('purchase.cancel');

resources/views/purchase/success.blade.php
@extends('layouts.app')

@section('title', '購入完了')
@section('content')
<div class="card">
  <h1>✅ 決済が完了しました！</h1>
  <p>ご購入ありがとうございました。</p>
  <a href="{{ route('items.index') }}" class="btn">トップに戻る</a>
</div>
@endsection

resources/views/purchase/cancel.blade.php
@extends('layouts.app')

@section('title', '決済キャンセル')
@section('content')
<div class="card">
  <h1>⚠️ 決済をキャンセルしました。</h1>
  <p>もう一度お試しください。</p>
  <a href="{{ route('items.index') }}" class="btn">トップに戻る</a>
</div>
@endsection

⑤ 動作確認

1️⃣ .env の STRIPE_SECRET_KEY が正しいか再確認
2️⃣ php artisan serve または Docker で環境起動
3️⃣ 購入画面（/purchase/{item_id}）で「購入する」ボタンをクリック
4️⃣ Stripeのテスト決済画面（checkout.stripe.com）に遷移したら成功！

✅ 今夜の完了ライン

「購入する」→ Stripe の決済ページが開く

成功 or キャンセル後、完了ページに戻る

DB登録（orders）はまだ行わない

💬 次ステップ（次回予定）
ステップ	内容
Step3	Stripe Webhook受信 or success画面で orders テーブルに登録
Step4	コンビニ決済対応 (payment_method_types に konbini 追加)
Step5	注文履歴・マイページからの確認
🧭 今夜のポイントまとめ

Stripeは「サーバー側がセッションを作ってURLを返す」仕組み

まずは 「決済ページに飛ぶ」ことが動けばOK！

住所・注文テーブルの紐づけはこのあと自然に行う

💡 次回用キーワード
WebhookController, Order::create(), StripeSession::retrieve()

このまま docs/stripe_setup.md にコピペして保存OKです。
次回は「決済完了後、ordersテーブルに注文情報を登録する」Step③を一緒に進めましょう。

📅 進行提案
今夜：Step①〜②
明日：Step③（注文保存）


🧾 最短動作確認チェックリスト（今夜用）

今夜は「Stripeの決済ページが開く」ことがゴールです。
実際に支払い完了まではテスト用カードを使って行います。

✅ 事前確認（コード・設定）
No	確認項目	期待結果
1	composer require stripe/stripe-php 実行済み	vendor/stripe/stripe-php/ が生成されている
2	.env に STRIPE_SECRET_KEY と STRIPE_PUBLIC_KEY を追記済み	正しいキーが保存されている
3	PurchaseController に Stripeの Session::create() コードを記述済み	storeメソッドで $session->url にリダイレクトしている
4	routes/web.php に success / cancel ルートを追加済み	/purchase/success, /purchase/cancel にアクセスできる
5	php artisan optimize:clear 実行済み	キャッシュがリセットされている
✅ 動作確認手順
ステップ	操作	期待結果
1	購入ページ（/purchase/{item_id}）を開く	商品情報と「購入する」ボタンが表示されている
2	「購入する」ボタンを押す	Stripe の checkout.stripe.com ページへ遷移する
3	Stripe テストカード番号 4242 4242 4242 4242（CVC任意、有効期限未来）を入力	支払い完了画面へ遷移する
4	success_url（＝/purchase/success）が表示される	✅「決済が完了しました！」が見える
5	cancel_url（＝/purchase/cancel）を確認	✅「決済をキャンセルしました。」が見える
✅ トラブル発生時チェックポイント
症状	確認項目	修正箇所
Stripe画面に飛ばない	.env の秘密鍵が無効 or 誤字	STRIPE_SECRET_KEY を再確認
500エラーが出る	use Stripe\Stripe; use Stripe\Checkout\Session; 記述忘れ	PurchaseController の先頭に追記
成功画面に戻らない	success/cancelルート未設定	web.php に再追加
テストカードがエラー	有効期限・CVC形式を再確認	Stripe入力欄を確認
✅ 今夜のゴール

 「購入する」ボタンでStripe Checkoutが開く

 成功・キャンセルページの遷移が確認できる

 DB登録はまだ行わない（次ステップ）

💬 次回予告

Step③「ordersテーブルに購入履歴を登録する」
決済完了後、WebhookControllerを作成し、Stripeの返却情報を処理する流れへ。