<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

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
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        $user->name     = $request->name;
        $user->postal   = $request->postal;
        $user->address  = $request->address;
        $user->building = $request->building;
        if (is_null($user->onboarded_at)) {
            $user->onboarded_at = now(); // 初回設定完了フラグ
        }
        $user->save();

        return redirect()->route('mypage.show')->with('message', 'プロフィールを更新しました。');
    }
}
