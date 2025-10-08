<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;

class MyPageController extends Controller
{
    // GET /mypage
    public function show()
    {
        $user = auth()->user();
        return view('mypage.show', compact('user'));
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
        $address = $user->defaultAddress()->first() ?? new Address;

        return view('profile.edit', compact('user', 'address'));
    }
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        // 画像
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        // 基本
        $user->name = $request->name;
        if (is_null($user->onboarded_at)) {
            $user->onboarded_at = now();
        }
        $user->save();

        // 住所（デフォルト住所を作成/更新）
        $addr = $user->defaultAddress()->first();

        $data = [
            'postal'     => $request->postal,
            // 画面を「1行住所 + 建物名」にしている場合は line1/line2 に詰め替え
            'prefecture' => $request->prefecture ?? null,
            'city'       => $request->city ?? null,
            'line1'      => $request->address,
            'line2'      => $request->building,
            'phone'      => $request->phone ?? null,
            'is_default' => true,
        ];

        if (!$addr) {
            $user->addresses()->create($data);
        } else {
            $addr->fill($data)->save();
        }

        return redirect()->route('mypage.show')->with('message', 'プロフィールを更新しました。');
    }
}
