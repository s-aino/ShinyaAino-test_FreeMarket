<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request, Item $item)
    {
        // ① $request->validate() を Validator に置き換え（エラー時の戻り先を自分で決めるため）
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string', 'max:255'],
        ], [
            'body.required' => 'コメントを入力してください。',
            'body.string'   => 'コメントの形式が正しくありません。',
            'body.max'      => 'コメントは255文字以内で入力してください。',
        ]);

        // ② エラー時：#comments 付きで戻す（＝コメント欄の位置にとどまる）
        if ($validator->fails()) {
            return redirect()->to(route('items.show', $item) . '#comment-body')
                ->withErrors($validator)
                ->withInput();
            // Laravel 9/10 以降なら ↓ でもOK（好みで）
            // return back()->withErrors($validator)->withInput()->withFragment('comments');
        }

        // ③ 成功時は従来どおり
        $validated = $validator->validated();

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);
        return back()->with('success(,コメントを投稿しました');
    }
}
