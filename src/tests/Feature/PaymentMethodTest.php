<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 支払い方法を「コンビニ払い」に選択した場合、ビュー上で正しく反映される
     */
    public function test_user_can_select_conveni_payment_method()
    {
        // 準備：ユーザー／住所／商品
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 実行：購入ページへアクセス
        $response = $this->actingAs($user)->get(route('purchase.show', $item));

        // 初期状態では未選択を想定（'__'など）
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払い');
    }

    /**
     * 「カード支払い」を選択した場合、hidden inputに正しく反映される
     */
    public function test_user_can_select_card_payment_method_and_reflect_in_form()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 「カード支払い」でPOST（checkout）
        $response = $this->actingAs($user)->post(route('purchase.checkout', $item), [
            'payment_method' => 'card',
            'address_id' => $address->id,
        ]);

        // 結果：リダイレクト（Stripe決済画面 or 成功ビューへの遷移）
        $response->assertStatus(302);

        // DBにはまだ登録されない（Stripe経由のため）
        $this->assertDatabaseMissing('orders', [
            'item_id' => $item->id,
        ]);
    }

    /**
     * 不正な支払い方法を選択した場合、バリデーションエラーになる
     */
    public function test_invalid_payment_method_validation_error()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 存在しない支払い方法でPOST
        $response = $this->actingAs($user)->from(route('purchase.show', $item))->post(route('purchase.checkout', $item), [
            'payment_method' => 'bitcoin', // ❌ 想定外
            'address_id' => $address->id,
        ]);

        // バリデーションエラー確認
        $response->assertSessionHasErrors(['payment_method']);
    }
}
