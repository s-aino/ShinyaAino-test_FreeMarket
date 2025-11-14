<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ログイン済みユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $item), [
            'body' => 'とても良い商品です！'
        ]);

        $response->assertRedirect(); // 戻り先（back）を確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'とても良い商品です！'
        ]);
    }

    /**
     * @test
     * ログインしていないユーザーはコメントを送信できない
     */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item), [
            'body' => '未ログインコメント'
        ]);

        // 未ログインの場合はログインページへリダイレクトされる
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comments', [
            'body' => '未ログインコメント'
        ]);
    }

    /**
     * @test
     * コメント未入力時はバリデーションエラー
     */
    public function test_validation_error_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $item), [
            'body' => ''
        ]);

        $response->assertSessionHasErrors(['body']);
        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     * コメントが255文字を超えるとバリデーションエラー
     */
    public function test_validation_error_when_comment_exceeds_255_chars()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post(route('comments.store', $item), [
            'body' => $longComment
        ]);

        $response->assertSessionHasErrors(['body']);
        $this->assertDatabaseCount('comments', 0);
    }
}
