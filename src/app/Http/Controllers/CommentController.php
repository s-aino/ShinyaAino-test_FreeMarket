<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    // POST /item/{item}/comments
    public function store(Request $request, $itemId)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:255'],
        ], [
            'body.required' => 'コメントを入力してください',
            'body.max'      => 'コメントは255文字以内で入力してください',
        ]);

        // TODO: DB保存（comments テーブルに user_id, item_id, body）
        return back()->with('message', "商品 {$itemId} にコメントしました（仮）");
    }
}
