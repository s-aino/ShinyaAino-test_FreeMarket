<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string', 'max:255'],
        ], [
            'body.required' => 'コメントを入力してください。',
            'body.string'   => 'コメントの形式が正しくありません。',
            'body.max'      => 'コメントは255文字以内で入力してください。',
        ]);

        // エラー時：コメント欄の位置へ戻す
        if ($validator->fails()) {
            return redirect()->to(route('items.show', $item) . '#comment-body')
                ->withErrors($validator)
                ->withInput();
        }

        // 保存
        $validated = $validator->validated();

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);
        return back()->with('message', 'コメントを投稿しました。');
    }
}
