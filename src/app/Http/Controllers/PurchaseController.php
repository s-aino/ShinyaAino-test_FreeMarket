<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Carbon\Carbon;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    // 購入ページ表示
    public function show(Item $item)
    {
        // 未ログイン → ログイン画面へ
        if (!auth()->check()) {
            session()->put('url.intended', route('purchase.show', ['item_id' => $item->id]));
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 一時住所（セッション）があれば優先
        $tempAddress = session('temp_address_id')
            ? Address::find(session('temp_address_id'))
            : null;

        // 一時住所がなければ登録住所を使用
        $address = $tempAddress ?? $user->address;

        return view('purchase.show', compact('item', 'address'));
    }

    // 配送先編集フォーム
    public function editAddress(Item $item)
    {
        $address = auth()->user()->address ?? new Address([
            'postal' => '',
            'line1' => '',
            'line2' => '',
        ]);

        return view('purchase.address', compact('item', 'address'));
    }

    // 配送先更新（今回の配送先）
    public function updateAddress(AddressRequest $request, Item $item)
    {
         $user = auth()->user();

        // 一時住所を新規作成（今回の配送先）
        $tempAddress = $user->address()->create([
            'postal' => $request->postal,
            'line1' => $request->address,
            'line2' => $request->building,
            'is_temporary' => true,
        ]);

        // セッションに保存
        session(['temp_address_id' => $tempAddress->id]);

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '今回の配送先を変更しました。')
            ->with('temp_address_id', $tempAddress->id);
    }

    // 購入処理 (カード / コンビニ)
    public function checkout(Request $request, Item $item)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $user = $request->user();

        // バリデーション
        $request->validate([
            'payment_method' => 'required|in:card,conveni',
            'address_id' => 'required|exists:addresses,id',
        ]);

        // 自分の出品は購入不可
        if ($item->user_id === $user->id) {
            abort(403, '自分の出品は購入できません。');
        }

        // 売り切れチェック 
        if ($item->isSold()) {
            abort(403, 'この商品は売り切れです。');
        }

        // --- 支払い方法で分岐 ---
        $method = $request->input('payment_method');
        $types = [];

        // カード払い（Stripe）
        if ($method === 'card') {
            $types = ['card'];
            $successUrl = route('purchase.success', ['item' => $item->id]);
            $cancelUrl = route('purchase.show', ['item' => $item->id]);

            // 💳 Stripe セッション作成
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => $types,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->title],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            return redirect($session->url);
        }

        // コンビニ払い
        if ($method === 'conveni') {
            $existingOrder = Order::where('buyer_id', $user->id)
                ->where('item_id', $item->id)
                ->first();

            if (!$existingOrder) {
                Order::create([
                    'buyer_id' => $user->id,
                    'item_id' => $item->id,
                    'address_id' => $request->input('address_id'),
                    'price' => $item->price,
                    'qty' => 1,
                    'status' => 'paid',
                    'ordered_at' => now(),
                ]);
            }

            // 商品を売り切れに更新
            $item->update(['status' => 'sold']);

            return view('purchase.success', compact('item'))
                ->with('message', '購入が完了しました（コンビニ払い）');
        }

        // その他（戻す）
        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '支払い方法を選択してください。');
    }

    // Stripeカード払い 成功時の処理
    public function success(Item $item)
    {
        $user = auth()->user();

        $existingOrder = Order::where('buyer_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if (!$existingOrder) {
            Order::create([
                'buyer_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => optional($user->address)->id,
                'price' => $item->price,
                'qty' => 1,
                'status' => 'paid',
                'ordered_at' => now(),
            ]);

            $item->update(['status' => 'sold']);
        }

        return view('purchase.success', compact('item'))
            ->with('message', '決済が完了しました。');
    }

}