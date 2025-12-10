<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab')->toString() ?: 'recommend';
        $q   = trim((string)$request->query('q', ''));

        // 未認証 & likes タブは空表示
        if ($tab === 'likes' && !auth()->check()) {
            $items = Item::query()
                ->whereRaw('1=0')
                ->paginate(24)
                ->withQueryString();

            return view('items.index', compact('items', 'tab', 'q'));
        }

        // いいね済み
        if ($tab === 'likes') {
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
                $query->orderBy('created_at', 'asc');
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

        return view('items.show', compact('item', 'categories'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('items.sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像を storage/app/public/items に保存
        $path = $request->file('image')->store('items', 'public');

        // テスト環境以外は画像をリサイズ
        // if (app()->environment('testing')) {
        // } else {
        //     $imagePath = storage_path('app/public/' . $path);
        //     $manager = new ImageManager(new Driver());
        //     $manager->read($imagePath)
        //         ->cover(600, 600)
        //         ->save($imagePath);
        // }

        //  商品登録（DBには "items/〇〇.jpg" だけを保存）
        $item = new Item();
        $item->user_id = auth()->id();
        $item->title = $validated['title'];
        $item->description = $validated['description'];
        $item->condition = $validated['condition'];
        $item->price = $validated['price'];
        $item->brand = $validated['brand'] ?? null;
        $item->image_path = 'storage/' . $path;
        $item->save();

        // カテゴリの紐付け
        if (isset($validated['categories'])) {
            $item->categories()->sync($validated['categories']);
        }
        return redirect()->route('sell.success');
    }
    public function success()
    {
        return view('items.success');
    }
}
