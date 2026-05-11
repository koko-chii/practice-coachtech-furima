<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テストユーザーを作成（住所登録・メール認証・過去設定データを適用）
     */
    private function createVerifiedUser()
    {
        return User::factory()->create([
            'name' => 'テスト太郎',
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'img_url' => 'profiles/test_avatar.jpg'
        ]);
    }

    /* =========================================================================
     * ユーザー情報取得
     * ========================================================================= */

    /**
     * @testdox ユーザー情報取得：必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     * 手順：1. ユーザーにログインする 2. プロフィールページを開く
     * 期待値：プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
     */
    public function test_ユーザー情報取得_必要な情報が取得できる(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createVerifiedUser();

        // 出品した商品と購入した商品を事前に準備
        $myListedItem = Item::factory()->create([
            'name' => '私が出品した商品',
            'user_id' => $user->id
        ]);

        $myPurchasedItem = Item::factory()->create(['name' => '私が購入した商品']);
        Order::create([
            'user_id' => $user->id,
            'item_id' => $myPurchasedItem->id,
        ]);

        // 2. プロフィールページを開く
        $response = $this->actingAs($user)->get('/mypage');

        // 期待値：プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
        $response->assertStatus(200);
        $response->assertSee('profiles/test_avatar.jpg');
        $response->assertSee('テスト太郎');

        // タブ切り替え等に合わせて出品一覧・購入一覧が表示されることを検証
        $responseSell = $this->actingAs($user)->get('/mypage?page=sell');
        $responseSell->assertSee('私が出品した商品');

        $responseBuy = $this->actingAs($user)->get('/mypage?page=buy');
        $responseBuy->assertSee('私が購入した商品');
    }

    /* =========================================================================
     * ユーザー情報変更
     * ========================================================================= */

    /**
     * @testdox ユーザー情報変更：変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     * 手順：1. ユーザーにログインする 2. プロフィールページを開く
     * 期待値：各項目の初期値が正しく表示されている
     */
    public function test_ユーザー情報変更_変更項目が初期値として過去設定されていること(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createVerifiedUser();

        // 2. プロフィール設定画面（編集画面）を開く
        $response = $this->actingAs($user)->get('/mypage/profile');

        // 期待値：各項目の初期値が正しく表示されている
        $response->assertStatus(200);
        $response->assertSee('profiles/test_avatar.jpg');
        $response->assertSee('テスト太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区');
    }
}
