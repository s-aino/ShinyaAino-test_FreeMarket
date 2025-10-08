<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Comment;

class PostPendingCommentAfterLogin
{
    public function handle(Login $event): void
    {
        // 下書き（セッション）を取り出して消す
        $data = session()->pull('pending_comment');
        if (!$data) return;

        $itemId = $data['item_id'] ?? null;
        $body   = trim((string)($data['body'] ?? ''));

        if ($itemId && $body !== '' && mb_strlen($body) <= 255) {
            Comment::create([
                'item_id' => $itemId,
                'user_id' => $event->user->id,
                'body'    => $body,
            ]);
            session()->flash('success', 'コメントを投稿しました。');

            // 戻り先未設定ならコメント欄へ
            if (!session()->has('url.intended')) {
                session(['url.intended' => route('items.show', $itemId) . '#comments']);
            }
        }
    }
}
