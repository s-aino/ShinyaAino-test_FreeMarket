<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Carbon\Carbon;
use Stripe\PaymentIntent;

class PurchaseController extends Controller
{
    // 購入ページ表示 (purchase.show)
    public function show(Item $item)
    {
        // ✅ ログインしていなければ、戻り先URLを記録してログイン画面へ
        if (!auth()->check()) {
            Session::put('url.intended', route('purchase.show', ['item_id' => $item->id]));
            return redirect()->route('login');
        }
        $address = auth()->user()->address; // nullでもOK
        return view('purchase.show', compact('item', 'address'));
    }

    // 購入処理 (purchase.store)
    public function store(Request $request, Item $item)
    {
        $user = $request->user();

        // 自分の出品は購入不可
        if ($item->user_id === $user->id) {
            abort(403, '自分の出品は購入できません。');
        }

        // 売切れチェック
        if (method_exists($item, 'isSold') && $item->isSold()) {
            abort(403, 'この商品は売り切れです。');
        }

        // 住所取得
        $address = $user->address()->first();

        // ここに購入処理（注文作成や在庫更新など）を書く予定
        // いまは仮でリダイレクト
        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '購入処理を実行しました。');
    }

    // 住所変更画面
    public function editAddress(Item $item)
    {
        $address = auth()->user()->address ?? new Address();
        return view('purchase.address', compact('item', 'address'));
    }


    public function updateAddress(AddressRequest $request, Item $item)
    {
        $user = auth()->user();

        // 単一住所として上書き
        $user->address()->updateOrCreate(
            [], // user_idは自動で補われる
            [
                'postal' => $request->postal_code,
                'line1'  => $request->line1,
                'line2'  => $request->line2,
            ]
        );

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '住所を更新しました。');
    }
    public function checkout(Item $item)
    {
        // Stripe初期設定
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // 商品情報
        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->title,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url' => route('purchase.show', ['item' => $item->id]),
        ]);

        // Stripe決済ページへリダイレクト
        return redirect($session->url);
    }
    // コンビニ支払い（ダミー用）
    public function konbini(Item $item)
    {
        // ダミーで購入完了処理
        $item->update(['status' => 'sold']);

        // 成功ページへ
        return view('purchase.success', compact('item'))
            ->with('message', 'コンビニ支払いが完了しました（ダミー）');
    }
    public function success(Item $item)
    {
        $user = auth()->user();

        // 既に購入済みでない場合のみ登録
        $existingOrder = Order::where('buyer_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if (!$existingOrder) {
            Order::create([
                'buyer_id'   => $user->id,
                'item_id'    => $item->id,
                'address_id' => optional($user->address)->id,
                'price'      => $item->price,
                'qty'        => 1,
                'status'     => 'paid',
                'ordered_at' => now(),
            ]);

            // ✅ 確実に status を更新（Eloquentではなく直接クエリ）
            Item::where('id', $item->id)->update(['status' => Item::STATUS_SOLD]);
        }

        return view('purchase.success', compact('item'));
    }

    // public function konbiniCheckout(Item $item)
    // {
    //     Stripe::setApiKey(config('services.stripe.secret'));

    //     // 支払金額（円単位で整数指定）
    //     $amount = $item->price;

    //     // PaymentIntentを作成
    //     $paymentIntent = PaymentIntent::create([
    //         'amount' => $amount,
    //         'currency' => 'jpy',
    //         'payment_method_types' => ['konbini'],
    //         'description' => "商品ID: {$item->id} の購入（コンビニ払い）",
    //         'metadata' => [
    //             'item_id' => $item->id,
    //             'user_id' => auth()->id(),
    //         ],
    //     ]);

    //     return view('purchase.konbini', [
    //         'item' => $item,
    //         'clientSecret' => $paymentIntent->client_secret,
    //     ]);
    // }

    // public function konbiniSuccess(Item $item)
    // {
    //     // 成功画面。実際にはWebhookで更新するのが正ですが、まずは手動で。
    //     $item->update(['status' => 'sold']);
    //     return view('purchase.konbini_success', compact('item'));
    // }
}
