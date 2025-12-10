<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\Item;

class MyPageController extends Controller
{
    // 出品した商品と購入した商品
    public function show()
    {
        $user = auth()->user();

        $sellingItems = $user->items()
            ->with('categories')
            ->latest()
            ->get();

        $purchasedItems = \App\Models\Order::where('buyer_id', $user->id)
            ->with('item')
            ->latest()
            ->get()
            ->pluck('item');

        return view('mypage.show', compact('user', 'sellingItems', 'purchasedItems'));
    }
    public function edit()
    {
        $user = auth()->user();

        // デフォルトの住所を取得（なければ null）
        $address = $user->addresses()
            ->where('is_default', true)
            ->first();

        // なければ空の住所モデルを用意
        if (!$address) {
            $address = new \App\Models\Address([
                'postal' => '',
                'line1' => '',
                'line2' => '',
            ]);
        }

        return view('profile.edit', compact('user', 'address'));
    }
    
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        // 画像アップロード
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        // 基本情報更新
        $user->name = $request->name;

        if (is_null($user->onboarded_at)) {
            $user->onboarded_at = now();
        }

        $user->save();


        // --- 住所更新（hasMany対応 + デフォルト1件に統一）---

        // 1. 既存住所は全部 is_default = false にリセット
        $user->addresses()->update(['is_default' => false]);

        // 2. デフォルト住所を更新 or 作成
        $user->addresses()->updateOrCreate(
            ['is_default' => true], // デフォルト住所を検索
            [
                'user_id' => $user->id,
                'postal' => $request->postal,
                'line1' => $request->address,
                'line2' => $request->building,
                'is_default' => true,
            ]
        );


        return redirect()
            ->route('mypage.show')
            ->with('message', 'プロフィールを更新しました。');
    }
}
