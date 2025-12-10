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
     * 住所変更ページにアクセスできる
     */
    public function test_user_can_access_address_edit_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('purchase.address.edit', $item));

        $response->assertStatus(200);
        $response->assertSee('住所の変更');
        $response->assertSee('郵便番号');
    }

    /**
     * 新しい住所を登録し、一時住所として保存される
     */
    public function test_user_can_update_address_and_temp_address_is_saved()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        // POST → 一時住所作成
        $response = $this->actingAs($user)
            ->post(route('purchase.address.update', $item), [
                'postal'   => '123-4567',
                'address'  => '東京都渋谷区1-1-1',
                'building' => 'テストビル202',
            ]);

        // リダイレクト確認
        $response->assertRedirect(route('purchase.show', ['item' => $item->id]));
        $response->assertSessionHas('temp_address_id');

        // DB 確認
        $this->assertDatabaseHas('addresses', [
            'user_id'      => $user->id,
            'postal'       => '123-4567',
            'line1'        => '東京都渋谷区1-1-1',
            'line2'        => 'テストビル202',
            'is_temporary' => true,
        ]);
    }

    /**
     * 登録した住所が購入ページに反映される
     */
    public function test_address_reflects_on_purchase_page()
    {
        $user   = User::factory()->create();
        $seller = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        // デフォルト住所を登録
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'postal'  => '987-6543',
            'line1'   => '大阪府大阪市中央区2-2-2',
            'line2'   => 'テストハイツ101',
            'is_default' => true,
        ]);

        // 購入ページへアクセス
        $response = $this->actingAs($user)
            ->get(route('purchase.show', $item));
        $response->assertStatus(200);

        // postal 表示（ハイフン入れた形式）
        $response->assertSee('987-6543');

        // line1 表示
        $response->assertSee('大阪府大阪市中央区2-2-2');
    }
}
