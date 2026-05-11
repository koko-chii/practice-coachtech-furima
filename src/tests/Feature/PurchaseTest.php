<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class PurchaseTest extends TestCase
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
     * 商品購入機能
     * ========================================================================= */

    /**
     * @testdox 商品購入機能：「購入する」ボタンを押下すると購入が完了する
     * 手順：1. ユーザーにログインする 2. 商品購入画面を開く 3. 商品を選択して「購入する」ボタンを押下
     * 期待値：購入が完了する
     */
    public function test_商品購入機能_購入するボタンを押下すると購入が完了する(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        // 2. 商品購入画面を開く (対象商品の作成)
        $item = Item::factory()->create(['price' => 1000, 'is_sold' => false]);

        // 3. 商品を選択して「購入する」ボタンを押下 (Stripe決済または注文完了エンドポイントへのリクエスト)
        // ※ 実際の決済成功リダイレクト先（購入完了処理）に合わせてエンドポイントを調整してください
        $response = $this->actingAs($user)->get("/purchase/success/{$item->id}");

        // 期待値：購入が完了する (ステータスコード変更、DBへの注文情報の追加)
        $response->assertStatus(302); 
        $this->assertEquals(1, $item->fresh()->is_sold);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * @testdox 商品購入機能：購入した商品は商品一覧画面にて「sold」と表示される
     * 手順：1. ユーザーにログインする 2. 商品購入画面を開く 3. 商品を選択して「購入する」ボタンを押下 4. 商品一覧画面を表示する
     * 期待値：購入した商品が「sold」として表示されている
     */
    public function test_商品購入機能_購入した商品は商品一覧画面にてsoldと表示される(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        // 2. 商品購入画面を開く 3. 商品を選択して「購入する」ボタンを押下 (購入済み商品をシミュレート)
        $item = Item::factory()->create(['is_sold' => true]);

        // 4. 商品一覧画面を表示する
        $response = $this->get("/");

        // 期待値：購入した商品が「sold」として表示されている
        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    /**
     * @testdox 商品購入機能：「プロフィール/購入した商品一覧」に追加されている
     * 手順：1. ユーザーにログインする 2. 商品購入画面を開く 3. 商品を選択して「購入する」ボタンを押下 4. プロフィール画面を表示する
     * 期待値：購入した商品がプロフィールの購入した商品一覧に追加されている
     */
    public function test_商品購入機能_プロフィール購入した商品一覧に追加されている(): void
    {
        // 1. ユーザーにログインする
        $user = $this->createFullAccessUser();
        // 2. 商品購入画面を開く 3. 商品を選択して「購入する」ボタンを押下 (購入および注文データ作成をシミュレート)
        $item = Item::factory()->create(['name' => '購入済み確認商品']);
        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 4. プロフィール画面を表示する（購入した商品一覧を表示）
        // ※ プロフィールの切り替えタブ仕様に合わせて、URLパラメータ（例: page=buy）を設定しています
        $response = $this->actingAs($user)->get("/mypage?page=buy");

        // 期待値：購入した商品がプロフィールの購入した商品一覧に追加されている
        $response->assertStatus(200);
        $response->assertSee('購入済み確認商品');
    }
}
