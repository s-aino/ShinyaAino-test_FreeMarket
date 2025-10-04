<?php

namespace App\Http\Controllers;
use App\Models\Item;
use Illuminate\Http\Request;
// use App\Models\Item; // ← DB接続に切り替える時に使う

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::query()
            ->select(['id','title','price','image_path','status'])
            ->latest('id')
            ->paginate(20);

        return view('items.index', compact('items'));
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }
}
