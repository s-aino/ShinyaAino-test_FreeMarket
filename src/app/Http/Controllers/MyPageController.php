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

        // 住所など必要であればここで取得
        $address = $user->address;
        // ->where('is_default', true)->first();

        // profile/edit.blade.php を表示
        return view('profile.edit', compact('user', 'address'));
    }
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        // 画像アップロード（現状維持）
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        // 基本情報
        $user->name = $request->name;
        if (is_null($user->onboarded_at)) {
            $user->onboarded_at = now();
        }
        $user->save();

        // --- 住所更新 or 新規作成 ---
        $data = [
            'postal' => $request->postal,
            'line1' => $request->address,
            'line2' => $request->building,
            'is_default' => true,
        ];

        // Userモデル: hasMany(Address::class)
        $user->address()->updateOrCreate([], $data);
        $user->load('address');


        return redirect()->route('profile.edit')
            ->with('message', 'プロフィールを更新しました。');
    }
}
