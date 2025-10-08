<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


class LikeController extends Controller
{
    public function store(Item $item): RedirectResponse
    {
        $item->likes()->firstOrCreate(['user_id' => auth()->id()]);
        return back();
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->likes()->where('user_id', auth()->id())->delete();
        return back();
    }
}