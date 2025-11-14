<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 「商品名」で部分一致検索ができる
     */
    public function test_can_search_items_by_partial_name()
    {
        // arrange: テストデータ作成
        $item1 = Item::factory()->create(['title' => '革靴']);
        $item2 = Item::factory()->create(['title' => '黒い靴']);
        $item3 = Item::factory()->create(['title' => 'ジャケット']);

        // act
        $response = $this->get(route('items.index', ['q' => '靴']));

        // assert
        $response->assertStatus(200);
        $response->assertSee($item1->title);
        $response->assertSee($item2->title);
        $response->assertDontSee($item3->title);
    }
    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_query_is_preserved_on_mylist_tab()
    {
        // arrange: ユーザーと商品を準備
        $user = User::factory()->create();
        $item = Item::factory()->create(['title' => 'レザーバッグ']);
        $user->likes()->create(['item_id' => $item->id]);

        // act: ログイン後、「バッグ」で検索した状態でマイリストを開く
        $this->actingAs($user);
        $response = $this->get('/?tab=likes&q=バッグ');

        // assert: キーワードが保持されている
        $response->assertStatus(200);
        $response->assertSee('レザーバッグ');
        $response->assertSee('バッグ'); // 入力欄valueに残っている
    }
}
