<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Like;

class LikeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * ログインしたユーザーが商品に「いいね」できる
     */
    public function test_user_can_like_an_item()
    {
        // arrange：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // act：ログインしていいね実行
        $response = $this->actingAs($user)->post(route('likes.store', $item));

        // assert：DBにレコードが作成されている
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ページ遷移も確認
        $response->assertRedirect(route('items.show', $item));
    }

    /**
     * @test
     * すでにいいね済みの場合は色が変化している（いいね状態で表示）
     */
    public function test_liked_icon_is_displayed_when_item_is_liked()
    {
        // arrange：ユーザーと商品を作成し、いいね登録
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // act：詳細ページを取得
        $response = $this->actingAs($user)->get(route('items.show', $item));

        // assert：「is-on」クラスが存在する（ONアイコン）
        $response->assertSee('is-on');
    }

    /**
     * @test
     * 再度いいねを押すと、いいねが解除される
     */
    public function test_user_can_unlike_an_item()
    {
        // arrange：ユーザーと商品を作成し、いいね登録
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // act：ログインしてDELETEリクエスト送信
        $response = $this->actingAs($user)->delete(route('likes.destroy', $item));

        // assert：DBから削除されている
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ページ遷移確認
        $response->assertRedirect(route('items.show', $item));
    }
}
