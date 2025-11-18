<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;



class LikeController extends Controller
{
    public function store(Item $item): RedirectResponse
    {
        $item->likes()->firstOrCreate(['user_id' => auth()->id()]);
        return redirect()->route('items.show',  ['item' => $item->id]);
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->likes()->where('user_id', auth()->id())->delete();
        return redirect()->route('items.show',  ['item' => $item->id]);
    }
}
