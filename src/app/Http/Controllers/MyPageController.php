<?php

namespace App\Http\Controllers;

class MyPageController extends Controller
{
    // GET /mypage
    public function show()
    {
        return view('mypage.show');
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
}
