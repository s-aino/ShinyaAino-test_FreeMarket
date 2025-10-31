<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\ExhibitionRequest;
// use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
                // ->where('status', Item::STATUS_ACTIVE)
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
        // 関連データを読み込み（コメントは古い順）
        $item->loadMissing([
            'comments' => function ($query) {
                $query->orderBy('created_at', 'asc'); // ← 追加：古い順に表示
            },
            'comments.user'
        ])
            ->loadCount(['likes', 'comments']);
        // カテゴリの取得（複数対応）
        $categories = collect();
        if ($item->relationLoaded('categories') && $item->categories) {
            $categories = $item->categories->pluck('name');
        } elseif ($item->relationLoaded('category') && $item->category) {
            $categories = collect([$item->category->name]);
        }

        // ビューを返す
        return view('items.show', compact('item', 'categories'));
    }
    // 出品フォーム（新規）
    public function create()
    {
        $categories = Category::all(); // 全カテゴリを取得
        return view('items.sell', compact('categories'));
    }

    // 出品保存（POST /sell）
    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // ① 画像を storage/app/public/items に保存
        $path = $request->file('image')->store('items', 'public');
        // $path の中身は "items/ファイル名.jpg"

        // ② Intervention Image でサイズ調整
        $imagePath = storage_path('app/public/' . $path);
        $manager = new ImageManager(new Driver());
        $manager->read($imagePath)
            ->cover(600, 600)
            ->save($imagePath);

        // ③ 商品登録（DBには "items/〇〇.jpg" だけを保存）
        $item = new Item();
        $item->user_id = auth()->id();
        $item->title = $validated['title'];
        $item->description = $validated['description'];
        $item->condition = $validated['condition'];
        $item->price = $validated['price'];
        $item->brand = $validated['brand'] ?? null;
        $item->image_path = 'storage/' . $path;  // ← storage を付けるのがポイント
        $item->save();

        // ④ カテゴリの紐付け（もしあれば）
        if (isset($validated['categories'])) {
            $item->categories()->sync($validated['categories']);
        }
        return redirect()->route('sell.success');
    }
    public function success()
    {
        return view('items.success');
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
        return redirect()->route('items.show', $item)->with('message', '更新しました');
    }
}
