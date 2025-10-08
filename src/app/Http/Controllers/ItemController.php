<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab')->toString() ?: 'recommend'; // recommend | likes
        $q   = trim((string)$request->query('q', ''));

        // 未認証 & likes タブは空表示
        if ($tab === 'likes' && !auth()->check()) {
            $items = Item::query()
                ->whereRaw('1=0')
                ->paginate(24)
                ->withQueryString();

            return view('items.index', compact('items', 'tab', 'q'));
        }

        if ($tab === 'likes') {
            // いいね済み（売切れも含める）
            $items = Item::query()
                ->select(['id', 'title', 'status', 'user_id', 'created_at', 'image_path']) // ← image_urlは入れない
                ->whereHas('likes', fn($qq) => $qq->where('user_id', auth()->id()))
                ->when($q !== '', fn($qq) => $qq->where('title', 'like', "%{$q}%"))
                ->latest()
                ->paginate(24)
                ->withQueryString();
        } else {
            // おすすめ（在庫あり + 自分の出品除外 + 新着順）
            $items = Item::query()
                ->select(['id', 'title', 'status', 'user_id', 'created_at', 'image_path']) // ← image_urlは入れない
                ->when(auth()->check(), fn($qq) => $qq->where('user_id', '!=', auth()->id()))
                // ▼ statusはModelの定数に合わせる。'available' を使うなら定数を用意してそちらに統一。
                ->where('status', Item::STATUS_ACTIVE)
                // ->where('status', 'available')  // ← DBがavailableならこちらに
                ->when($q !== '', fn($qq) => $qq->where('title', 'like', "%{$q}%"))
                ->latest()
                ->paginate(24)
                ->withQueryString();
        }

        return view('items.index', compact('items', 'tab', 'q'));
    }

    public function show(Item $item)
    {
        // 必要な関連を（存在すれば）読み込む
        $with = ['comments.user:id,name', 'category:id,name']; // 単一カテゴリ
        if (Schema::hasTable('category_item')) {               // 多カテゴリのピボットがあるなら
            $with[] = 'categories:id,name';
        }

        $item->loadMissing($with)->loadCount(['likes', 'comments']);

        // 表示用カテゴリ配列（多→単の順で優先）
        $categories = collect();
        if ($item->relationLoaded('categories') && $item->categories) {
            $categories = $item->categories->pluck('name');
        } elseif ($item->relationLoaded('category') && $item->category) {
            $categories = collect([$item->category->name]);
        }

        return view('items.show', compact('item', 'categories'));
    }
    // 出品フォーム（新規）
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        // フォームで old() を使いやすいように空の Item を渡してもOK
        $item = new Item();
        return view('items.create', compact('categories', 'item'));
    }

    // 出品保存（POST /sell）
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            // …他の必須項目…
            'category_id' => ['required', 'integer', 'exists:categories,id'], // ★単一カテゴリ
        ]);

        $validated['user_id'] = auth()->id();
        $item = Item::create($validated);          // Item::$fillable に category_id があること！

        return redirect()->route('items.show', $item)->with('success', '出品しました');
    }

    // 編集フォーム（任意）
    public function edit(Item $item)
    {
        $this->authorize('update', $item); // 使っていれば
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('items.edit', compact('item', 'categories'));
    }

    // 更新（任意）
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item); // 使っていれば
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            // …他の必須項目…
            'category_id' => ['required', 'integer', 'exists:categories,id'], // ★単一カテゴリ
        ]);

        $item->update($validated);
        return redirect()->route('items.show', $item)->with('success', '更新しました');
    }
}
