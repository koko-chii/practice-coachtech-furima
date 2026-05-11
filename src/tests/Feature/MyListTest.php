<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================================================
     * マイリスト一覧取得
     * ========================================================================= */

    /**
     * @testdox マイリスト一覧取得：いいねした商品だけが表示される
     * 手順：1. ユーザーにログインをする 2. マイリストページを開く
     * 期待値：いいねをした商品が表示される
     */
    public function test_マイリスト一覧取得_いいねした商品だけが表示される(): void
    {
        // 1. ユーザーにログインをする
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567'
        ]);
        $otherUser = User::factory()->create();

        $likedItem = Item::create([
            'name' => 'いいねした商品',
            'price' => 1000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/liked.jpg'
        ]);

        $notLikedItem = Item::create([
            'name' => 'いいねしていない商品',
            'price' => 2000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/not-liked.jpg'
        ]);

        $user->likedItems()->attach($likedItem->id);

        // 2. マイリストページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 期待値：いいねをした商品が表示される
        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertSee('items/liked.jpg');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * @testdox マイリスト一覧取得：購入済み商品は「Sold」と表示される
     * 手順：1. ユーザーにログインをする 2. マイリストページを開く 3. 購入済み商品を確認する
     * 期待値：購入済み商品に「Sold」のラベルが表示される
     */
    public function test_マイリスト一覧取得_購入済み商品はSoldと表示される(): void
    {
        // 1. ユーザーにログインをする
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567'
        ]);
        $otherUser = User::factory()->create();

        $soldItem = Item::create([
            'name' => '購入済み商品',
            'price' => 1000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/sold.jpg',
            'is_sold' => true
        ]);

        $user->likedItems()->attach($soldItem->id);

        // 2. マイリストページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 3. 購入済み商品を確認する -> 期待値：購入済み商品に「Sold」のラベルが表示される
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * @testdox マイリスト一覧取得：未認証の場合は何も表示されない
     * 手順：1. マイリストページを開く
     * 期待値：何も表示されない
     */
    public function test_マイリスト一覧取得_未認証の場合は何も表示されない(): void
    {
        // 1. マイリストページを開く（未ログイン状態）
        $response = $this->get('/?tab=mylist');

        // 期待値：何も表示されない
        $response->assertStatus(200);
        $response->assertDontSee('product-card'); // 商品カード等が描画されていないことを確認
    }
}
