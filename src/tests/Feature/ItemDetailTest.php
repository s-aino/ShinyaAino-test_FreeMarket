<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品詳細ページが正常に表示され、情報が含まれている
     */
    public function test_item_detail_page_displays_correct_information()
    {
        // arrange
        $user = User::factory()->create();

        $item = Item::factory()->create([
            'title' => 'テストスニーカー',
            'price' => 8800,
            'description' => '履き心地の良いテストスニーカーです。',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        // カテゴリを2つ作成し中間テーブルで紐付け
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));

        // コメントを1件登録
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'body' => 'とても良い商品でした！',
        ]);

        // act
        $response = $this->get(route('items.show', $item));

        // assert
        $response->assertStatus(200);
        $response->assertSee('テストスニーカー');
        $response->assertSee('8,800');
        $response->assertSee('履き心地の良いテストスニーカーです。');
        $response->assertSee('とても良い商品でした！');

        // カテゴリ名が2つとも出ているか確認
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    /**
     * 売却済み商品の場合、SOLDバッジが表示される
     */
    public function test_sold_item_shows_sold_badge()
    {
        $item = Item::factory()->create([
            'title' => '売却済みアイテム',
            'status' => 'sold',
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }
}
