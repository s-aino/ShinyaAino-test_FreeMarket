<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     */
    public function test_liked_items_are_displayed()
    {
        // arrange: ユーザーと商品を準備し、1件だけ「いいね」する
        $user = User::factory()->create();
        $likedItem = Item::factory()->create();
        $notLikedItem = Item::factory()->create(); // ← これは非表示になる想定

        $user->likes()->create(['item_id' => $likedItem->id]);

        // act: ログイン状態で likesタブ を取得
        $this->actingAs($user);
        $response = $this->get('/?tab=likes');

        // assert: いいねした商品のみ表示され、他は表示されない
        $response->assertStatus(200);
        $response->assertSee($likedItem->title);
        $response->assertDontSee($notLikedItem->title);
    }

    /**
     * 購入済み商品には「SOLD」ラベルが表示される
     */
    public function test_sold_label_displayed_for_liked_items()
    {
        // arrange: SOLD状態の商品を「いいね」する
        $user = User::factory()->create();
        $soldItem = Item::factory()->create(['status' => 'sold']);
        $user->likes()->create(['item_id' => $soldItem->id]);

        // act: ログイン状態で likesタブ を取得
        $this->actingAs($user);
        $response = $this->get('/?tab=likes');

        // assert: SOLDラベルが表示されている
        $response->assertSee('SOLD');
    }

    /**
     * 未ログインの場合はマイリストは空である
     */
    public function test_guest_user_sees_empty_list()
    {
        // act: ログインせず likesタブ を取得
        $response = $this->get('/?tab=likes');

        // assert: ステータス200で、商品が1件も表示されない
        $response->assertStatus(200);
        $response->assertDontSee('item-card');
    }
}
