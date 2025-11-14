<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済ユーザーがコンビニ払いで商品を購入できる
     */
    public function test_user_can_purchase_item_with_conveni_payment()
    {
        // 準備：ユーザー・住所・他人の商品
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 実行：コンビニ払いで購入
        $response = $this->actingAs($user)->post(route('purchase.checkout', $item), [
            'payment_method' => 'conveni',
            'address_id' => $address->id,
        ]);

        // ✅ 成功ビュー表示
        $response->assertStatus(200);
        $response->assertViewIs('purchase.success');
        $response->assertViewHas('item', $item);

        // ✅ DB確認：orders登録 & itemがsoldになる
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    /**
     * 出品者本人は自分の商品を購入できない
     */
public function test_seller_cannot_purchase_own_item()
{
    $user = User::factory()->create();
    $item = Item::factory()->create(['user_id' => $user->id, 'status' => 'active']);
    $address = Address::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('purchase.checkout', $item), [
        'payment_method' => 'conveni',
        'address_id' => $address->id,
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('orders', ['item_id' => $item->id]);
}

public function test_cannot_purchase_sold_item()
{
    $user = User::factory()->create();
    $seller = User::factory()->create();
    $item = Item::factory()->create(['user_id' => $seller->id, 'status' => 'sold']);
    $address = Address::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('purchase.checkout', $item), [
        'payment_method' => 'conveni',
        'address_id' => $address->id, 
    ]);

    $response->assertStatus(403);
}

    /**
     * 未ログインユーザーは購入ページにアクセスできない
     */
    public function test_guest_is_redirected_to_login_when_accessing_purchase_page()
    {
        $item = Item::factory()->create();
        $response = $this->get(route('purchase.show', $item));
        $response->assertRedirect(route('login'));
    }
}
