<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $request->body,
        ]);

        return back()->with('message', 'コメントを投稿しました。');
    }
}
