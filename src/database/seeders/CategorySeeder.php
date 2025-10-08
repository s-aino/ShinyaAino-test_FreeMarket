<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // UIに出ているカテゴリ（必要分だけでOK）
        $catalog = [
            'ファッション' => 'fashion',
            '家電'        => 'electronics',
            'インテリア'  => 'interior',
            'レディース'  => 'ladies',
            'メンズ'      => 'mens',
            'コスメ'      => 'cosmetics',
            '本'          => 'books',
            'ゲーム'      => 'games',
            'スポーツ'    => 'sports',
            'キッチン'    => 'kitchen',
            'ハンドメイド' => 'handmade',
            'アクセサリー' => 'accessories',
            'おもちゃ'    => 'toys',
            'ベビー・キッズ' => 'kids',
            'その他'      => 'others',
        ];
        $catIds = [];
        foreach ($catalog as $name => $slug) {
            $cat = \App\Models\Category::updateOrCreate(
                ['name' => $name],   // ← name は unique
                ['slug' => $slug]    // 既存なら slug を更新
            );
            $catIds[$name] = $cat->id; // 以降の割当で使用
        }
        // 10商品の割当（タイトルで雑にマップ。IDで指定してもOK）
        $map = [
            '腕時計'     => 'アクセサリー',
            'HDD'        => '家電',
            '玉ねぎ3束'   => 'キッチン',
            '革靴'       => 'メンズ',
            'ノートPC'   => '家電',
            'マイク'     => '家電',
            'ショルダーバッグ' => 'ファッション',
            'タンブラー' => 'キッチン',
            'コーヒーミル' => 'キッチン',
            'メイクセット' => 'コスメ',
        ];

        foreach ($map as $title => $catName) {
            $item = Item::where('title', $title)->first();
            if ($item && isset($catIds[$catName])) {
                $item->update(['category_id' => $catIds[$catName]]);
            }
        }
    }
}
