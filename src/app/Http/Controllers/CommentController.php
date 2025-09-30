<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // コメント投稿
    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'body' => ['required','string','max:255'],
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'body'    => $validated['body'],
        ]);

        return back()->with('message','コメントしました');
    }
}
