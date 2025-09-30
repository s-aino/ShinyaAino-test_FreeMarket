<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Item; // モデル準備できたら有効化

class ItemController extends Controller
{
    // GET /sell
    public function create()
    {
        return view('items.create');
    }

    // POST /sell
    public function store(Request $request)
    {
        // 最小バリデーション（要件に合わせて後で拡張）
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'integer', 'min:1'],
            'category_id' => ['nullable', 'integer'],
            'image_path'  => ['nullable', 'string', 'max:255'],
        ]);

        // いまはDB保存を必須にしない（提出前に差し替えOK）
        // Item::create($validated + ['user_id' => auth()->id()]);

        return redirect()->route('mypage.sales')->with('message', '出品を受け付けました（仮）');
    }
}
