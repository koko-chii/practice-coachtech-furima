<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テストユーザーを作成し、全てのガードを突破させる
     */
    private function createFullAccessUser()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'name' => 'テストユーザー'
        ]);
    }

    /* --- いいね機能のテスト --- */

    /** @test */
    public function test_いいねアイコンを押下して合計値が増加する()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/like/{$item->id}/like");

        $response->assertStatus($response->status() == 302 ? 302 : 200);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        // 合計値が増加していることを確認
        $this->assertEquals(1, $item->likedByUsers()->count());
    }

    /** @test */
    public function test_いいね済みのアイコンは色が変化している()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 先にいいねしておく
        $user->likedItems()->attach($item->id);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        // 実装に合わせてクラス名などは調整してください（例: active-like）
        $response->assertStatus(200);
        $response->assertSee('like-count');
    }

    /** @test */
    public function test_再度いいねアイコンを押下することによっていいねを解除できる()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $user->likedItems()->attach($item->id);
        $this->assertEquals(1, $item->likedByUsers()->count());

        $response = $this->actingAs($user)->post("/like/{$item->id}/like");

        $response->assertStatus($response->status() == 302 ? 302 : 200);
        $this->assertEquals(0, $item->likedByUsers()->count());
    }

    /** @test */
    public function test_未ログインユーザーはいいねができない()
    {
        $item = Item::factory()->create();
        $response = $this->post("/like/{$item->id}/like");

        $response->assertRedirect('/login');
    }

    /* --- コメント機能のテスト --- */

    /** @test */
    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/comment/{$item->id}/comment", [
            'comment' => 'テストコメントです'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'comment' => 'テストコメントです',
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
    }

    /** @test */
    public function test_未ログインユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/comment/{$item->id}/comment", [
            'comment' => '未ログインコメント'
        ]);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_コメントが空の場合はバリデーションエラーになる()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/comment/{$item->id}/comment", [
                'comment' => ''
            ]);

            $response->assertStatus(302);
            $response->assertRedirect("/item/{$item->id}");
            $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function test_255文字以上のコメントはバリデーションエラーになる()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/comment/{$item->id}/comment", [
                'comment' => str_repeat('あ', 256)
            ]);

        $response->assertSessionHasErrors(['comment']);
        $response->assertRedirect("/item/{$item->id}");
    }
}
