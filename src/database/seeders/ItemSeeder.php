<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1) 出品者ユーザー（なければ作成）
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            ['name' => 'デモ出品者', 'password' => Hash::make('password123')]
        );
        $sellerId = $seller->id;

        // items 側のユーザー列名（user_id or seller_id）を自動判定
        $userCol = Schema::hasColumn('items', 'user_id')
            ? 'user_id'
            : (Schema::hasColumn('items', 'seller_id') ? 'seller_id' : null);

        // 2) status 型を自動判定（ENUM/INT どちらでもOKに）
        $statusAvailable = 0; // INT系の既定
        $statusSold      = 1;

        $col = DB::selectOne("SHOW COLUMNS FROM items WHERE Field = 'status'");
        $type = $col ? strtolower($col->Type) : '';

        if (strpos($type, 'enum(') !== false) {
            // enum('available','sold',...) を抽出
            if (preg_match("/enum\\((.*)\\)/", $type, $m)) {
                $opts = str_getcsv($m[1], ',', "'"); // クォート対応
                $opts = array_map(fn($s) => trim($s), $opts);

                // available 候補
                $statusAvailable = $opts[0] ?? 'available';
                foreach ($opts as $o) {
                    $l = strtolower($o);
                    if (in_array($l, ['available', 'active', 'in_stock', 'open', 'listed'], true)) {
                        $statusAvailable = $o;
                        break;
                    }
                }
                // sold 候補
                $statusSold = $opts[0] ?? $statusAvailable;
                foreach ($opts as $o) {
                    if (strtolower($o) === 'sold') {
                        $statusSold = $o;
                        break;
                    }
                }
                if (count($opts) > 1 && $statusSold === $statusAvailable) {
                    $statusSold = $opts[1];
                }
            } else {
                $statusAvailable = 'available';
                $statusSold      = 'sold';
            }
        } elseif (!preg_match('/int|bool/', $type)) {
            // 文字列だけど enum じゃない場合の保険
            $statusAvailable = '0';
            $statusSold      = '1';
        }

        // 3) category_id 対応：必要なら既定カテゴリを用意して紐付け
        $categoryCol = Schema::hasColumn('items', 'category_id') ? 'category_id' : null;
        $categoryId = null;
        if ($categoryCol) {
            if (Schema::hasTable('categories')) {
                $categoryId = DB::table('categories')->value('id');
                if (!$categoryId) {
                    // 一番シンプルな作成（name列が無くても試行、だめなら id=1 前提に）
                    try {
                        $payload = [];
                        if (Schema::hasColumn('categories', 'name')) $payload['name'] = 'その他';
                        if (Schema::hasColumn('categories', 'title') && !isset($payload['name'])) $payload['title'] = 'その他';
                        if (Schema::hasColumn('categories', 'created_at')) $payload['created_at'] = $now;
                        if (Schema::hasColumn('categories', 'updated_at')) $payload['updated_at'] = $now;
                        $categoryId = DB::table('categories')->insertGetId($payload ?: ['id' => 1]);
                    } catch (\Throwable $e) {
                        $categoryId = 1; // 最後の手段
                    }
                }
            } else {
                $categoryId = 1; // テーブルが無い場合は 1 を入れる（FKが無ければ通る）
            }
        }

        // 4) ダミー商品
        $rows = [
            ['title' => '腕時計', 'price' => 15000, 'brand' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image_path' => 'items/sample1.jpg', 'status' => $statusAvailable, 'condition' => '良好'],
            ['title' => 'HDD', 'price' => 5000, 'brand' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'image_path' => 'items/sample2.jpg', 'status' => $statusAvailable, 'condition' => '目立った傷や汚れなし'],
            ['title' => '玉ねぎ3束', 'price' => 300, 'brand' => null, 'description' => '新鮮な玉ねぎ3束のセット', 'image_path' => 'items/sample3.jpg', 'status' => $statusAvailable, 'condition' => 'やや傷や汚れあり'],
            ['title' => '革靴', 'price' => 4000, 'brand' => null, 'description' => 'クラシックなデザインの革靴', 'image_path' => 'items/sample4.jpg', 'status' => $statusAvailable, 'condition' => '状態が悪い'], 
            ['title' => 'ノートPC', 'price' => 45000, 'brand' => null, 'description' => '高性能ノートパソコン', 'image_path' => 'items/sample5.jpg', 'status' => $statusAvailable, 'condition' => '良好'],
            ['title' => 'マイク', 'price' => 8000, 'brand' => null, 'description' => '高音質のレコーディング用マイク', 'image_path' => 'items/sample6.jpg', 'status' => $statusAvailable, 'condition' => '目立った傷や汚れなし'],
            ['title' => 'ショルダーバッグ', 'price' => 3500, 'brand' => null, 'description' => 'おしゃれなショルダーバッグ', 'image_path' => 'items/sample7.jpg', 'status' => $statusAvailable, 'condition' => 'やや傷や汚れあり'],
            ['title' => 'タンブラー', 'price' => 500, 'brand' => null, 'description' => '使いやすいタンブラー', 'image_path' => 'items/sample8.jpg', 'status' => $statusAvailable, 'condition' => '状態が悪い'],
            ['title' => 'コーヒーミル', 'price' => 4000, 'brand' => 'Starbacks', 'description' => '手動のコーヒーミル', 'image_path' => 'items/sample9.jpg', 'status' => $statusAvailable, 'condition' => '良好'],
            ['title' => 'メイクセット', 'price' => 2500, 'brand' => null, 'description' => '便利なメイクアップセット', 'image_path' => 'items/sample10.jpg', 'status' => $statusAvailable, 'condition' => '目立った傷や汚れなし'],
        ];

        // 5) 実在カラムだけ挿入（brand/condition 無くてもOK）
        $allowed = array_flip(Schema::getColumnListing('items'));

        foreach ($rows as $r) {
            if ($userCol)     $r[$userCol] = $sellerId;
            if ($categoryCol) $r[$categoryCol] = $categoryId;

            $data = array_intersect_key($r, $allowed);
            if (isset($allowed['created_at'])) $data['created_at'] = $now;
            if (isset($allowed['updated_at'])) $data['updated_at'] = $now;

            DB::table('items')->updateOrInsert(['title' => $r['title']], $data);
        }
    }
}
