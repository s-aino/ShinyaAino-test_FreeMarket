<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

class MailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_verification_notice_and_guide_pages()
    {
        // 認証済みユーザーを作成（メール未認証）
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 認証案内画面（verify-email.blade.php）にアクセスできること
        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');

        // 「認証はこちらから」ボタン先のガイド画面（verify-guide.blade.php）にもアクセスできること
        $response = $this->actingAs($user)->get(route('verification.guide'));
        $response->assertStatus(200);
        $response->assertSee('Mailtrap');
        $response->assertSee('受信メール内のリンクから認証を完了します。');
    }

    /** @test */
    public function user_can_resend_verification_mail()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 認証メール再送信ルートにPOST
        $response = $this->actingAs($user)->post(route('verification.send'));
        $response->assertRedirect(); // リダイレクトされること
        $response->assertSessionHas('message', '認証メールを再送しました。');
    }

    /** @test */
    public function user_is_redirected_to_profile_after_verification()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 署名付きURLを生成（5分間有効）
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(5),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 署名付きURLでアクセス
        $response = $this->actingAs($user)->get($signedUrl);

        // 成功後にプロフィール設定ページへ遷移する
        $response->assertRedirect(route('profile.edit'));
    }
}
