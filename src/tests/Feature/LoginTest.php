<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * ログイン画面が表示されるかテスト
     */
    public function test_ログイン画面が表示される()
    {
        // 1. ログイン画面にアクセス
        $response = $this->get('/login');

        // 2. ステータスコードが200（成功）であることを確認
        $response->assertStatus(200);
    }

    public function test_メールアドレスが未入力の場合にエラーが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        // セッションに指示書通りのエラーが含まれているか確認
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_パスワードが未入力の場合にエラーが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_ログイン情報が間違っている場合にエラーが表示される()
    {
        // ユーザーを一人作成しておく
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password', // 間違ったパスワード
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /**
     * 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_正しい情報でログインできる()
    {
        $password = 'password123';
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // 認証されていることを確認
        $this->assertAuthenticatedAs($user);

        // ログイン後のリダイレクト先（例：トップ画面など）を確認
        $response->assertRedirect('/');
    }
}

