<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================================================
     * 商品検索機能
     * ========================================================================= */

    /**
     * @testdox 商品検索機能：「商品名」で部分一致検索ができる
     * 手順：1. 検索欄にキーワードを入力 2. 検索ボタンを押す
     * 期待値：部分一致する商品が表示される
     */
    public function test_商品検索機能_商品名で部分一致検索ができる(): void
    {
        $this->seedSearchItems();

        // 1. 検索欄にキーワードを入力 2. 検索ボタンを押す（GETリクエストにキーワードを含める）
        $response = $this->get('/?keyword=PC');

        // 期待値：部分一致する商品が表示される
        $response->assertStatus(200);
        $response->assertSee('ノートPC');
        $response->assertDontSee('腕時計');
        $response->assertDontSee('玉ねぎ3束');
    }

    /**
     * @testdox 商品検索機能：検索状態がマイリストでも保持されている
     * 手順：1. ホームページで商品を検索 2. 検索結果が表示される 3. マイリストページに遷移
     * 期待値：検索キーワードが保持されている
     */
    public function test_商品検索機能_検索状態がマイリストでも保持されている(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567'
        ]);

        $this->seedSearchItems();

        // 検索対象となる商品をあらかじめユーザーのマイリストに登録しておく
        $pcItem = Item::where('name', 'ノートPC')->first();
        if ($pcItem) {
            $user->likedItems()->attach($pcItem->id);
        }

        // 1. ホームページで商品を検索 2. 検索結果が表示される 3. マイリストページに遷移
        // （キーワードを維持したままマイリストタブへアクセス）
        $response = $this->actingAs($user)->get('/?keyword=PC&tab=mylist');

        // 期待値：検索キーワードが保持されている（入力欄やクエリ等に反映され、絞り込みが維持されている）
        $response->assertStatus(200);
        $response->assertSee('PC');
        $response->assertSee('ノートPC');
        $response->assertDontSee('腕時計');
    }

    /**
     * 検索テスト用のデータ投入メソッド
     */
    private function seedSearchItems(): void
    {
        $otherUser = User::factory()->create();

        Item::create([
            'name' => 'ノートPC',
            'price' => 50000,
            'description' => 'ハイスペックPC',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/pc.jpg'
        ]);

        Item::create([
            'name' => '腕時計',
            'price' => 15000,
            'description' => '高級時計',
            'condition' => '普通',
            'user_id' => $otherUser->id,
            'img_url' => 'items/watch.jpg'
        ]);

        Item::create([
            'name' => '玉ねぎ3束',
            'price' => 300,
            'description' => '新鮮な野菜',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'items/onion.jpg'
        ]);
    }
}
