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
        $user = auth()->user();

        // ✅ セッションに一時住所があればそれを優先
        $tempAddress = session('temp_address_id')
            ? \App\Models\Address::find(session('temp_address_id'))
            : null;

        // ✅ 一時住所があればそれを、なければ登録住所を使用
        $address = $tempAddress ?? $user->address;

        return view('purchase.show', compact('item', 'address'));
    }

    // 住所変更画面
    public function editAddress(Item $item)
    {
        $address = auth()->user()->address ?? new Address();
        return view('purchase.address', compact('item', 'address'));
    }


    // 住所変更更新（今回のみの配送先）
    public function updateAddress(AddressRequest $request, Item $item)
    {
        $user = auth()->user();

        // 一時住所として新規登録
        $tempAddress = $user->address()->create([
            'postal'        => $request->postal_code,
            'line1'         => $request->line1,
            'line2'         => $request->line2,
            'is_temporary'  => true, // ← 重要！
        ]);

        // 購入ページで使う一時住所IDをセッションに保存
        session(['temp_address_id' => $tempAddress->id]);

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '今回の配送先を変更しました。');
    }

    public function checkout(Request $request, Item $item)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $user = $request->user();

        // --- バリデーション ---
        $request->validate([
            'payment_method' => 'required|in:card,conveni',
            'address_id' => 'required|exists:addresses,id',
        ]);

        // --- 自分の出品は購入不可 ---
        if ($item->user_id === $user->id) {
            abort(403, '自分の出品は購入できません。');
        }

        // --- 売り切れチェック ---
        if (method_exists($item, 'isSold') && $item->isSold()) {
            abort(403, 'この商品は売り切れです。');
        }

        // --- 支払い方法で分岐 ---
        $method = $request->input('payment_method');
        $types = [];

        if ($method === 'card') {
            $types = ['card'];
            $successUrl = route('purchase.success', ['item' => $item->id]);
            $cancelUrl = route('purchase.show', ['item' => $item->id]);

            // 💳 Stripe セッション作成（この時点ではDB登録しない）
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

            // Stripe画面へ遷移（ここではまだDB登録しない）
            return redirect($session->url);
        }

        // --- 🏪 コンビニ払い ---
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
            $item->update(['status' => 'sold']);

            return view('purchase.success', compact('item'))
                ->with('message', '購入が完了しました（コンビニ払い）');
        }

        // fallback
        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '支払い方法を選択してください。');
    }
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

    public function pending(Item $item)
    {
        return view('purchase.pending', compact('item'));
    }

    public function tempAddress(Request $request, Item $item)
    {
        $validated = $request->validate([
            'postal_code' => 'required|string|max:10',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
        ]);

        // 一時住所を保存（既存住所を上書きしない）
        $address = \App\Models\Address::create([
            'user_id' => auth()->id(),
            'postal' => $validated['postal_code'],
            'line1' => $validated['line1'],
            'line2' => $validated['line2'],
            'is_temporary' => true,
        ]);

        // 一時住所IDをセッションに保存
        session(['temp_address_id' => $address->id]);

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('message', '今回の配送住所を登録しました');
    }
}
