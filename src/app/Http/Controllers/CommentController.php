<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // ゲストが押したとき：下書きを保存してログインへ
    public function prepare(Request $request, Item $item)
    {
        // バリデもここで済ませる（255文字以内）
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:255'],
        ], [
            'body.required' => 'コメントを入力してください。',
            'body.string'   => 'コメントの形式が正しくありません。',
            'body.max'      => 'コメントは255文字以内で入力してください。',
        ]);

        // 下書きをセッションに保存
        session(['pending_comment' => [
            'item_id' => $item->id,
            'body'    => $validated['body'],
        ]]);

        // ログイン後の戻り先（コメント欄へスクロール）
        session(['url.intended' => route('items.show', $item) . '#comments']);

        return redirect()->route('login')
            ->with('info', 'コメント送信にはログインが必要です。ログイン後に自動投稿します。');
    }
    // POST /item/{item}/comments
    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:255'],
        ], [
            'body.required' => 'コメントを入力してください',
            'body.string'   => 'コメントの形式が正しくありません。',
            'body.max'      => 'コメントは255文字以内で入力してください',
        ]);
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);
        // TODO: DB保存（comments テーブルに user_id, item_id, body）
        return back()->with('success', 'コメントを投稿しました。');
    }
}
