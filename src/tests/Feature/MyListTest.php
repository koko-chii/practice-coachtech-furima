<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. いいねした商品だけが表示される
     */
    public function test_いいねした商品だけが表示される()
    {
        // ユーザーと、他人が出品した商品を作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $likedItem = Item::create([
            'name' => 'いいねした商品',
            'price' => 1000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/test1.jpg'
        ]);

        $notLikedItem = Item::create([
            'name' => 'いいねしていない商品',
            'price' => 2000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/test2.jpg'
        ]);

        // 【要調整】いいねのリレーション名（likes や favoriteItems など）
        $user->likedItems()->attach($likedItem->id);

        // 【要調整】マイリストを表示するURL（例: /?page=mylist や /mylist など）
        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * 2. 購入済み商品は「Sold」と表示される
     */
    public function test_マイリストでも購入済み商品はSoldと表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $soldItem = Item::create([
            'name' => '購入済み商品',
            'price' => 1000,
            'description' => 'テスト',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/test3.jpg',
            // 【要調整】売り切れを判定するカラム名や状態
            'is_sold' => true
        ]);

        // いいねを付けてマイリストに載せる
        $user->likedItems()->attach($soldItem->id);

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * 3. 未認証の場合は何も表示されない
     */
    public function test_未ログイン状態ではマイリストに何も表示されない()
    {
        // ログインせずにアクセス
        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        // 商品名が表示されるべき場所に、何も表示されていないことを確認
        // （「いいねした商品」という文字列が画面に存在しないことを確認）
        $response->assertDontSee('いいねした商品');
    }
}
