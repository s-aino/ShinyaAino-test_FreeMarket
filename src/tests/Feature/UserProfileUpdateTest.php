<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ✅ メール認証ミドルウェアを完全に無効化
        // $this->withoutMiddleware(['verified']);
        // $this->withoutMiddleware([
        //     \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        // ]);
    }

    /** @test */
    public function profile_edit_page_is_displayed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200)
            ->assertSee('プロフィール設定');
    }

    /** @test */
    public function user_can_update_basic_information()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'name' => 'テスト太郎',
            'postal' => '123-4567',
            'address' => '東京都渋谷区テスト1-1-1',
            'building' => 'テストビル',
        ];

        $response = $this->post(route('profile.update'), $data);

        // ✅ 正しいリダイレクト（mypage.show）
        $response->assertRedirectToRoute('mypage.show')
            ->assertSessionHas('message', 'プロフィールを更新しました。');
        // ✅ DB確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テスト太郎',
        ]);
    }

    /** @test */
    public function user_can_upload_profile_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $data = [
            'name' => '画像テスト',
            'postal' => '987-5403',
            'address' => '大阪市北区テスト町',
            'building' => '大阪テストビル',
            'profile_image' => $file,
        ];

        $response = $this->post(route('profile.update'), $data);

        // ✅ 正しいリダイレクト（mypage.show）
        $response->assertRedirectToRoute('mypage.show')
            ->assertSessionHas('message', 'プロフィールを更新しました。');
        // ✅ 画像ファイルの保存確認
        Storage::disk('public')->assertExists('profile_images/' . $file->hashName());

        // ✅ DB確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'profile_image_path' => 'profile_images/' . $file->hashName(),
        ]);
    }
}
