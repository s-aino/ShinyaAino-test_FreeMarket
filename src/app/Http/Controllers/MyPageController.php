<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\Item;

class MyPageController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        // 出品した商品（そのままでOK）
        $sellingItems = $user->items()
            ->with('categories')
            ->latest()
            ->get();

        // 購入した商品（Order経由 → Itemコレクションへ変換）
        $purchasedItems = \App\Models\Order::where('buyer_id', $user->id)
            ->with('item')     // itemリレーションを一度にロード
            ->latest()
            ->get()
            ->pluck('item');   // ← ここでItemのコレクションだけ取り出す！

        return view('mypage.show', compact('user', 'sellingItems', 'purchasedItems'));
    }
    public function edit()
    {
        $user = auth()->user();

        $address = $user->address ?? new \App\Models\Address([
            'postal' => '',
            'line1' => '',
            'line2' => '',
        ]);

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

        // 住所更新（既存行があれば上書き）
        $user->address()->updateOrCreate(
            ['user_id' => $user->id, 'is_default' => true],
            [
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
