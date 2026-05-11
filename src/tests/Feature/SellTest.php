<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================================================
     * 出品商品情報登録
     * ========================================================================= */

    /**
     * @testdox 出品商品情報登録：商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     * 手順：1. ユーザーにログインする 2. 商品出品画面を開く 3. 各項目に適切な情報を入力して保存する
     * 期待値：各項目が正しく保存されている
     */
    public function test_出品商品情報登録_商品出品画面にて必要な情報が保存できること(): void
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
        ]);

        // 2. 商品出品画面を開く (GETリクエストで表示確認が必要な場合はここで行います)
        $responseGet = $this->actingAs($user)->get('/sell');
        $responseGet->assertStatus(200);

        // 画像アップロードのダミーを設定
        Storage::fake('public');
        $file = UploadedFile::fake()->image('item.png');

        // 3. 各項目に適切な情報を入力して保存する (POSTリクエストを送信)
        // ※ 実際のコントローラーが期待するキー名（例: category_id か category かなど）に合わせて適宜調整してください
        $itemData = [
            'category'    => 1,
            'condition'   => 1,
            'name'        => '仕様書テスト商品',
            'brand'       => 'テストブランド',
            'description' => '商品の説明文がここに入ります。',
            'price'       => 5000,
            'img_url'     => $file,
        ];

        $responsePost = $this->actingAs($user)->post('/sell', $itemData);

        // 保存後のリダイレクト処理を確認
        $responsePost->assertStatus(302);

        // 期待値：各項目が正しく保存されている
        $this->assertDatabaseHas('items', [
            'name'         => '仕様書テスト商品',
            'brand'        => 'テストブランド',
            'description'  => '商品の説明文がここに入ります。',
            'price'        => 5000,
            'user_id'      => $user->id,
        ]);
    }
}
