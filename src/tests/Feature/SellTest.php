<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Testing\FileFactory;
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

        // 2. 商品出品画面を開く
        $responseGet = $this->actingAs($user)->get('/sell');
        $responseGet->assertStatus(200);

        // 外部キー制約エラーを避けるため、テスト環境用のダミーカテゴリを作成
        $category = new Category();
        $category->name = 'ファッション';
        $category->save();

        // 【GDライブラリ未対応環境対策】image() を使わず、テキストファイルを画像として作成（偽装）
        Storage::fake('public');
        $file = UploadedFile::fake()->create('Armani_Mens_Clock.png', 100, 'image/png');

        // 3. 各項目に適切な情報を入力して保存する（仕様書「腕時計」の正式ダミーデータ）
        $itemData = [
            'category_ids' => [$category->id], // 必須カテゴリーを選択
            'condition'    => '良好',
            'name'         => '腕時計',
            'brand'        => 'Rolax',
            'description'  => 'スタイリッシュなデザイン of メンズ腕時計',
            'price'        => 15000,
            'img_url'      => $file,
        ];

        $responsePost = $this->actingAs($user)
            ->from('/sell')
            ->post('/sell', $itemData);

        // バリデーションエラーが発生していた場合、セッションの中身を出力する
        if (session()->has('errors')) {
            $responsePost->dumpSession();
        }

        // 保存後のリダイレクト処理を確認
        $responsePost->assertStatus(302);

        // 期待値：各項目が正しく保存されている（仕様書データと一致するか検証）
        $this->assertDatabaseHas('items', [
            'name'         => '腕時計',
            'price'        => 15000,
            'brand'        => 'Rolax',
            'description'  => 'スタイリッシュなデザイン of メンズ腕時計',
            'condition'    => '良好',
            'user_id'      => $user->id,
        ]);
    }
}
