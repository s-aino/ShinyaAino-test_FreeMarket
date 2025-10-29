<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ① カテゴリ一覧（UIに出すカテゴリ）
        $catalog = [
            'ファッション' => 'fashion',
            '家電' => 'electronics',
            'インテリア' => 'interior',
            'レディース' => 'ladies',
            'メンズ' => 'mens',
            'コスメ' => 'cosmetics',
            '本' => 'books',
            'ゲーム' => 'games',
            'スポーツ' => 'sports',
            'キッチン' => 'kitchen',
            'ハンドメイド' => 'handmade',
            'アクセサリー' => 'accessories',
            'おもちゃ' => 'toys',
            'ベビー・キッズ' => 'kids',
        ];

        // カテゴリ登録（既存なら更新）
        $catIds = [];
        foreach ($catalog as $name => $slug) {
            $cat = Category::updateOrCreate(
                ['name' => $name],
                ['slug' => $slug]
            );
            $catIds[$name] = $cat->id;
        }

        // ✅ ② ダミー商品のカテゴリ割り当て（複数カテゴリ対応）
        $map = [
            '腕時計' => ['アクセサリー', 'ファッション'],
            'HDD' => ['家電'],
            '卓上ミラー' => ['コスメ', 'インテリア'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'コーヒーメーカー' => ['キッチン', '家電'],
            'タンブラー' => ['キッチン', 'スポーツ'],
            'メイクセット' => ['コスメ', 'レディース'],
            'おもちゃセット' => ['おもちゃ', 'ベビー・キッズ'],
        ];

        foreach ($map as $itemTitle => $categoryNames) {
            $item = Item::where('title', $itemTitle)->first();
            if (!$item) {
                continue; // ダミー商品が存在しない場合スキップ
            }

            // カテゴリ名からIDを取り出し
            $ids = [];
            foreach ((array)$categoryNames as $catName) {
                if (isset($catIds[$catName])) {
                    $ids[] = $catIds[$catName];
                }
            }

            // 中間テーブルに紐づけ
            if (!empty($ids)) {
                $item->categories()->sync($ids);
            }
        }

        // ✅ 完了ログ
        echo "✅ CategorySeeder: カテゴリと商品紐づけを登録しました。\n";
    }
}
