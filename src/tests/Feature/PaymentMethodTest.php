<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodTest extends TestCase
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
     * 支払い方法選択機能
     * ========================================================================= */

    /**
     * @testdox 支払い方法選択機能：小計画面で変更が反映される
     * 手順：1. 支払い方法選択画面を開く 2. プルダウンメニューから支払い方法を選択する
     * 期待値：選択した支払い方法が正しく反映される
     */
    public function test_支払い方法選択機能_小計画面で変更が反映される(): void
    {
        // 1. 支払い方法選択画面を開く（事前準備）
        $user = $this->createFullAccessUser();
        $item = Item::factory()->create();

        // 2. プルダウンメニューから支払い方法を選択する
        // ※実装に合わせて POST先URL や パラメータ名、選択値（例: 'konbini', 'card'など）を調整してください
        $response = $this->actingAs($user)
            ->from("/purchase/{$item->id}")
            ->post("/purchase/{$item->id}", [
                'payment_method' => 'konbini'
            ]);

        // 小計画面（購入画面）を再表示して確認
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        // 期待値：選択した支払い方法が正しく反映される
        $response->assertStatus(200);

        // 画面上に選択した決済手段（例: コンビニ払い、あるいは設定したテキスト）が表示されているか確認
        // ※ 実際のBladeの表示文字列に合わせて「コンビニ払い」などの文言を変更してください
        $response->assertSee('コンビニ払い');
    }
}
