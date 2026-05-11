<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase
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

    /* =========================================================================
     * コメント送信機能
     * ========================================================================= */

    /**
     * @testdox コメント送信機能：ログイン済みのユーザーはコメントを送信できる
     * 手順：1. ユーザーにログインする 2. コメントを入力する 3. コメントボタンを押す
     * 期待値：コメントが保存され、コメント数が増加する
     */
    public function test_コメント送信機能_ログイン済みのユーザーはコメントを送信できる(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 2. コメントを入力する 3. コメントボタンを押す
        $response = $this->actingAs($user)->post("/comment/{$item->id}/comment", [
            'comment' => '仕様書に準拠したテストコメントです'
        ]);

        // 期待値：コメントが保存され、コメント数が増加する
        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'comment' => '仕様書に準拠したテストコメントです',
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
        
        // コメント数が増加しているか（1件になっているか）確認
        $this->assertEquals(1, $item->comments()->count());
    }

    /**
     * @testdox コメント送信機能：ログイン前のユーザーはコメントを送信できない
     * 手順：1. コメントを入力する 2. コメントボタンを押す
     * 期待値：コメントが送信されない
     */
    public function test_コメント送信機能_ログイン前のユーザーはコメントを送信できない(): void
    {
        $item = Item::factory()->create();

        // 1. コメントを入力する 2. コメントボタンを押す（未ログイン状態）
        $response = $this->post("/comment/{$item->id}/comment", [
            'comment' => '未ログインコメント'
        ]);

        // 期待値：コメントが送信されない（ログイン画面等へリダイレクト）
        $response->assertRedirect('/login');
        $this->assertEquals(0, $item->comments()->count());
    }

    /**
     * @testdox コメント送信機能：コメントが入力されていない場合、バリデーションメッセージが表示される
     * 手順：1. ユーザーにログインする 2. コメントボタンを押す
     * 期待値：バリデーションメッセージが表示される
     */
    public function test_コメント送信機能_コメントが入力されていない場合バリデーションメッセージが表示される(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 2. コメントボタンを押す（空の状態で送信）
        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/comment/{$item->id}/comment", [
                'comment' => ''
            ]);

        // 期待値：バリデーションメッセージが表示される（エラーを伴って元の画面に戻る）
        $response->assertStatus(302);
        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * @testdox コメント送信機能：コメントが255字以上の場合、バリデーションメッセージが表示される
     * 手順：1. ユーザーにログインする 2. 255文字以上のコメントを入力する 3. コメントボタンを押す
     * 期待値：バリデーションメッセージが表示される
     */
    public function test_コメント送信機能_コメントが255字以上の場合バリデーションメッセージが表示される(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 2. 255文字以上のコメントを入力する 3. コメントボタンを押す
        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/comment/{$item->id}/comment", [
                'comment' => str_repeat('あ', 256)
            ]);

        // 期待値：バリデーションメッセージが表示される（エラーを伴って元の画面に戻る）
        $response->assertStatus(302);
        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['comment']);
    }
}
