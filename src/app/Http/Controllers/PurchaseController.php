<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Order;           // 無ければ後で作成
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // 購入ページ表示（purchase.confirm）

    public function create(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = $request->user();

        // 自分の出品は購入不可
        if ($item->user_id === $user->id) {
            abort(403, '自分の出品は購入できません。');
        }

        // 売切れガード（isSold() が無ければ削除でOK）
        if (method_exists($item, 'isSold') && $item->isSold()) {
            abort(403, 'この商品は売り切れです。');
        }

        // 既定住所（is_default=true）
        $address = $request->user()->address()->first();
        // 選択中の支払い方法（バリデ戻り対応）
        $selectedPayment = old('payment_method');

        return view('purchase.confirm', compact('item', 'address'));
    }

    // 購入確定（Stripeは後続で差し替え）
    public function store(Request $r, $itemId)
    {
        // 支払い方法は必須（conveni / card）
        $r->validate([
            'payment_method' => 'required|in:conveni,card',
        ], [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in'       => '支払い方法の選択が不正です。',
        ]);

        $user = $r->user();
        $item = Item::findOrFail($itemId);

        if ($item->user_id === $user->id) abort(403);
        if (method_exists($item, 'isSold') && $item->isSold()) abort(403);

        $address = $user->address; // hiddenで送らず、保存済みの住所を採用
        if (!$address) {
            return back()->withErrors(['address' => '配送先を登録してください。'])->withInput();
        }

        DB::transaction(function () use ($user, $item, $r, $address) {
            if (class_exists(Order::class)) {
                Order::firstOrCreate(
                    ['item_id' => $item->id], // 1商品=1注文
                    [
                        'user_id'        => $user->id,
                        'address_id'     => $address->id,
                        'payment_method' => $r->payment_method, // conveni/card
                        'status'         => 'paid',             // ダミー
                        'paid_at'        => now(),
                    ]
                );
            }
            // 商品をSOLD扱いへ（カラムがあれば反映）
            if ($item->isFillable('status'))  $item->status  = 'sold';
            if ($item->isFillable('sold_at')) $item->sold_at = now();
            $item->save();
        });

        // あなたの遷移に合わせて
        return redirect()->route('mypage.purchases')
            ->with('message', '商品を購入しました。');
    }

    // 配送先フォーム表示（purchase.address）
    public function editAddress($itemId)
    {
        $item    = Item::findOrFail($itemId);
        $address = auth()->user()->address; // null 可

        return view('purchase.address', [
            'item'    => $item,
            'address' => $address,
        ]);
    }

    // 配送先保存（作成 or 更新）
    public function updateAddress(Request $r, $itemId)
    {
        $r->validate([
            'postal_code'   => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'prefecture'    => ['required', 'string', 'max:50'],
            'city'          => ['required', 'string', 'max:100'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
        ], [
            'postal_code.required'   => '郵便番号を入力してください。',
            'postal_code.regex'      => '郵便番号はハイフンありの8文字で入力してください。',
            'prefecture.required'    => '都道府県を入力してください。',
            'city.required'          => '市区町村を入力してください。',
            'address_line1.required' => '住所（番地）を入力してください。',
        ]);

        $user = $r->user();
        // hasOne( Address ) 前提：条件配列は空でupdateOrCreate
        $user->address()->updateOrCreate([], [
            'postal_code'   => $r->postal_code,
            'prefecture'    => $r->prefecture,
            'city'          => $r->city,
            'address_line1' => $r->address_line1,
            'address_line2' => $r->address_line2,
            'phone'         => $r->phone,
        ]);

        return redirect()->route('purchase.create', $itemId)
            ->with('message', '住所を保存しました。');
    }
}
