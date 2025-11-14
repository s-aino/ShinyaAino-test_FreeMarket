<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全商品を取得できる
     */
    public function test_can_get_all_items()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * 購入済み商品には「Sold」が表示される
     */
    public function test_sold_label_displayed_for_purchased_items()
    {
        // arrange: 売却済みの商品を作る
        $item = Item::factory()->create([
            'status' => 'sold', // ← これが重要！
        ]);

        // act: 一覧ページを取得
        $response = $this->get('/');

        // assert: SOLDラベルが表示されている
        $response->assertSee('SOLD');
    }
    /**
     * 自分が出品した商品は一覧に表示されない
     */
    public function test_own_items_are_not_listed()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/');

        $response->assertDontSee($ownItem->title);
    }
}
