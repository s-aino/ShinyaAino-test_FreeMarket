<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_new_item_information()
    {
        // 🧰 ダミーユーザー＆カテゴリ作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $category = Category::factory()->create();

        // 🖼 ダミー画像ファイル
        Storage::fake('public');
        $file = UploadedFile::fake()->create('sample.jpg', 100, 'image/jpeg');
        // 📦 出品データを送信
        $response = $this->actingAs($user)
            ->post(route('items.store'), [
                'title' => 'テスト商品',
                'description' => 'テスト用の商品です',
                'condition' => '良好',
                'price' => 3000,
                'brand' => 'COACHTECH',
                'categories' => [$category->id],
                'image' => $file,
            ]);

        // 🧭 成功後はリダイレクトされる
        $response->assertRedirect(route('sell.success'));

        // ✅ DBに登録されたことを確認
        $this->assertDatabaseHas('items', [
            'title' => 'テスト商品',
            'description' => 'テスト用の商品です',
            'condition' => '良好',
            'price' => 3000,
            'brand' => 'COACHTECH',
        ]);

        // ✅ ストレージに画像が保存されたことを確認
        Storage::disk('public')->assertExists('items/' . $file->hashName());

        // ✅ カテゴリの紐付け確認
        $item = Item::first();
        $this->assertTrue($item->categories->contains($category->id));
    }
}
