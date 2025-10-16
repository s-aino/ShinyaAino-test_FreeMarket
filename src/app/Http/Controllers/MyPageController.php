<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;

class MyPageController extends Controller
{
    // GET /mypage
    public function show(Request $r)
    {
        $user = $r->user();
        $address = $user->address()->first();   // is_default=true の1件
        return view('mypage.show', compact('user', 'address'));
    }
    // GET /mypage/purchases
    public function purchases()
    {
        // TODO: ユーザーの購入履歴を取得して渡す
        return view('mypage.purchases');
    }

    // GET /mypage/sales
    public function sales()
    {
        // TODO: ユーザーの出品一覧を取得して渡す
        return view('mypage.sales');
    }
    public function edit(Request $request)
    {
        $user = $request->user();
        $address = $user->address()->first() ?? new Address;
        $avatarSrc = $user->avatar_url;
        return view('profile.edit', compact('user', 'address'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        // 画像アップロード（現状ロジック維持）
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        // 基本情報（現状ロジック維持）
        $user->name = $request->name;
        if (is_null($user->onboarded_at)) {
            $user->onboarded_at = now();
        }
        $user->save();

        // --- ここから住所：デフォルト住所を作成/更新（is_default=true を常に1つ）---
        // 入力名 -> DBカラム名のマッピング（あなたのスキーマに合わせ済み）
        $data = [
            'postal'     => $request->postal,                // 例: 123-4567
            'prefecture' => $request->prefecture ?? null,    // 都道府県
            'city'       => $request->city ?? null,          // 市区町村
            'line1'      => $request->address,               // 住所（番地）
            'line2'      => $request->building,              // 建物名（任意）
            'phone'      => $request->phone ?? null,         // 任意
            'is_default' => true,
        ];

        // これ1行で「is_default=1 のレコードを作成or更新」
        $request->user()->address()->updateOrCreate(['is_default' => true], $data);        // --- 住所ここまで -------------------------------------------------------

        return redirect()->route('mypage.show')->with('message', 'プロフィールを更新しました。');
    }
}
