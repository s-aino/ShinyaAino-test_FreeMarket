<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザーが住所変更画面を開けることを確認
     */
    public function test_user_can_access_address_edit_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('purchase.address.edit', $item));
        $response->assertStatus(200);
        $response->assertSee('住所の変更');
        $response->assertSee('郵便番号');
    }

    /**
     * ユーザーが新しい住所を登録し、セッションに一時住所が保存される
     */
    public function test_user_can_update_address_and_temp_address_is_saved()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        // 住所登録前
        $this->assertDatabaseCount('addresses', 0);
        // dd(route('purchase.address.update', $item));
        // POSTリクエスト（住所更新）
        $response = $this->actingAs($user)->post(route('purchase.address.update', $item), [
            'postal' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル202',
        ]);

        // リダイレクト確認
        $response->assertRedirect(route('purchase.show', ['item' => $item->id]));
        $response->assertSessionHas('temp_address_id');

        // DB確認
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'postal' => '123-4567',
            'line1' => '東京都渋谷区1-1-1',
            'line2' => 'テストビル202',
            'is_tempORary' => true,
        ]);
    }

    /**
     * 登録した住所が購入ページに反映される
     */
    public function test_address_reflects_on_purchase_page()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $address = Address::factory()->create([
            'user_id' => $user->id,
            'postal' => '987-6543',
            'line1' => '大阪府大阪市中央区2-2-2',
            'line2' => 'テストハイツ101',
        ]);

        $response = $this->actingAs($user)->get(route('purchase.show', $item));

        $response->assertStatus(200);
        $response->assertSee('987-6543');
        $response->assertSee('大阪府大阪市中央区2-2-2');
    }
}
