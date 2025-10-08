<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function create($itemId)
    {
        return view('purchase.confirm', ['itemId' => $itemId]);
    }
    public function store(Request $r, $itemId)
    {
        return redirect()->route('mypage.purchases')->with('message', "商品 {$itemId} を購入（仮）");
    }
    public function editAddress($itemId)
    {
        return view('purchase.address', ['itemId' => $itemId, 'address' => null]);
    }
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
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'   => '郵便番号はハイフンありの8文字で入力してください',
            'prefecture.required' => '住所（都道府県）を入力してください',
            'city.required'       => '住所（市区町村）を入力してください',
            'address_line1.required' => '住所（番地）を入力してください',
        ]);
        return back()->with('message', '住所を保存しました（仮）');
    }
}
