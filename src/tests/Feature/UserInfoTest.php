<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserInfoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーがマイページにアクセスできる
     */
    public function test_user_can_access_mypage()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('mypage.show'));
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('出品した商品');
        $response->assertSee('購入した商品');
    }

    /**
     * プロフィール画像とユーザー名が正しく表示される
     */
    public function test_user_profile_image_and_name_are_displayed()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image_path' => 'profile_images/sample.png',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.show'));
        $response->assertSee('テストユーザー');
        $response->assertSee('profile_images/sample.png');
    }

    /**
     * 出品商品と購入商品が正しく表示される
     */
    public function test_user_selling_and_purchased_items_are_displayed()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // 出品商品
        $sellingItem = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '出品テスト商品',
        ]);

        // 購入商品（Order 経由）
        $purchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => '購入テスト商品',
        ]);

        Order::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.show'));

        // 出品した商品・購入した商品が一覧に表示されているか
        $response->assertSee('出品テスト商品');
        $response->assertSee('購入テスト商品');
        $response->assertStatus(200);
    }
}
