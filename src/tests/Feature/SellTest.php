<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
    public function test_商品出品画面にて必要な情報を保存できる()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postcode' => '123-4567',
            'address' => '東京都',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('item.png');

        // エラーメッセージに合わせてキー名を 'condition_id' から 'condition' に変更
        // カテゴリも念のため 'category' に合わせています
        $itemData = [
            'name'         => 'テスト商品',
            'description'  => 'テスト用の説明文です。',
            'category'     => 1,
            'condition'    => 1,
            'brand'        => 'テストブランド',
            'price'        => 5000,
            'img_url'      => $file,
        ];

        $response = $this->actingAs($user)->post('/sell', $itemData);

        // 成功していれば、リダイレクト先は元の入力画面ではないはずです
        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'name'         => 'テスト商品',
            'brand'        => 'テストブランド',
            'price'        => 5000,
            'user_id'      => $user->id,
        ]);
    }
}
