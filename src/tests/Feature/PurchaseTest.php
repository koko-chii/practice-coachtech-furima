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

    private function createFullAccessUser()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'name' => 'テストユーザー'
        ]);
    }

    /** @test */
    public function test_商品購入画面が表示される()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    /** @test */
    public function test_購入ボタンを押すとStripe決済へリダイレクトされる()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create(['price' => 1000]);

        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'card'
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('stripe.com', $response->headers->get('Location'));
    }

    /** @test */
    public function test_購入した商品は商品一覧画面にてsoldと表示される()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create(['is_sold' => true]); // 売り切れ状態

        $response = $this->get("/");

        $response->assertStatus(200);
        // 実装に合わせて「sold」や「Sold Out」など調整してください
        $response->assertSee('sold');
    }

    /** @test */
    public function test_購入した商品がプロフィール画面の購入した商品一覧に追加されている()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 注文データを作成（購入済み状態）
        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 購入履歴ページ（例: /mypage?tab=buy）へアクセス
        $response = $this->actingAs($user)->get("//mypage?page=buy");

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    /** @test */
    public function test_決済成功後に商品が売り切れ状態になり注文情報が保存される()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create(['is_sold' => false]);

        session(['shipping_address' => [
            'postcode' => '111-2222',
            'address' => '大阪府大阪市',
            'building' => 'テストビル'
        ]]);

        $response = $this->actingAs($user)->get("/purchase/success/{$item->id}");

        $response->assertRedirect('/');

        // DBの状態確認
        $this->assertEquals(1, $item->fresh()->is_sold);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function test_支払い方法を選択すると小計画面に反映される()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 1. 支払い方法を選択（コンビニ払いを選択したと仮定）
        // ※実装に合わせて POST 先やパラメータ名を調整してください
        $response = $this->actingAs($user)
            ->from("/purchase/{$item->id}")
            ->post("/purchase/{$item->id}", [
                'payment_method' => 'konbini'
            ]);

        // 2. 小計画面（購入画面）を再表示
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        // 3. 選択した支払い方法が画面に表示されているか確認
        // 表示される文言（例: コンビニ払い）に合わせて調整してください
        $response->assertSee('コンビニ払い');
    }

       /** @test */
    public function test_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 1. 送付先住所を変更（正しいURLにPOST）
        $newAddress = [
            'postcode' => '999-8888',
            'address' => '大阪府大阪市北区梅田',
            'building' => 'テストビル101'
        ];

        // URLを /purchase/address/{item_id} に修正
        $this->actingAs($user)
            ->post("/purchase/address/{$item->id}", $newAddress);

        // 2. 商品購入画面を表示して、変更が反映されているか確認
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('999-8888');
        $response->assertSee('大阪府大阪市北区梅田');
        $response->assertSee('テストビル101');
    }
}
