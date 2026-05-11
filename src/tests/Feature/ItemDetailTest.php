<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================================================
     * 商品詳細情報取得
     * ========================================================================= */

    /**
     * @testdox 商品詳細情報取得：必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
     * 手順：1. 商品詳細ページを開く
     * 期待値：すべての情報が商品詳細ページに表示されている
     */
    public function test_商品詳細情報取得_必要な情報が表示される(): void
    {
        $otherUser = User::factory()->create();

        // コメントを投稿するユーザーを作成
        $commentUser = User::factory()->create(['name' => 'コメントしたユーザー名']);

        // 指示書の正式なデータ仕様に基づいて商品を登録（img_urlには指示書通りのフルパスを設定）
        $item = Item::create([
            'name' => '腕時計',
            'price' => 15000,
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => '良好',
            'user_id' => $otherUser->id,
            'img_url' => 'Rolax+Mens+Clock.jpg'
        ]);

        // MassAssignment（複数代入）のエラーを回避してカテゴリを作成
        $category = new Category();
        $category->name = 'テストカテゴリ';
        $category->save();

        if (method_exists($item, 'categories')) {
            $item->categories()->attach($category->id);
        }

        // コメントの作成
        Comment::create([
            'comment' => 'テスト用の具体的なコメント内容です。',
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
        ]);

        // 1. 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // 期待値：すべての情報が商品詳細ページに表示されている
        $response->assertStatus(200);
        $response->assertSee('Rolax+Mens+Clock.jpg'); // Bladeの {{ asset('storage/' . $item->img_url) }} で出力される画像URL
        $response->assertSee('腕時計'); // 商品名
        $response->assertSee('Rolax'); // ブランド名
        $response->assertSee('15,000'); // 価格（Blade内の number_format によりカンマ付きで出力される ¥15,000 に適合）
        $response->assertSee('スタイリッシュなデザインのメンズ腕時計'); // 商品説明
        $response->assertSee('良好'); // 商品の状態
        $response->assertSee('テストカテゴリ'); // カテゴリ名

        // コメント情報
        $response->assertSee('コメントしたユーザー名'); // コメントしたユーザー情報
        $response->assertSee('テスト用の具体的なコメント内容です。'); // コメント内容
    }

    /**
     * @testdox 商品詳細情報取得：複数選択されたカテゴリが表示されているか
     * 手順：1. 商品詳細ページを開く
     * 期待値：複数選択されたカテゴリが商品詳細ページに表示されている
     */
    public function test_商品詳細情報取得_複数選択されたカテゴリが表示されているか(): void
    {
        $otherUser = User::factory()->create();

        $item = Item::create([
            'name' => '複数カテゴリ商品',
            'price' => 5000,
            'brand' => 'テストブランド',
            'description' => '商品説明文です。',
            'condition' => '普通',
            'user_id' => $otherUser->id,
            'img_url' => 'amazonaws.com'
        ]);

        // MassAssignment（複数代入）のエラーを回避して複数のカテゴリを作成
        $category1 = new Category();
        $category1->name = 'ファッション';
        $category1->save();

        $category2 = new Category();
        $category2->name = 'メンズ';
        $category2->save();

        $category3 = new Category();
        $category3->name = 'アウター';
        $category3->save();

        if (method_exists($item, 'categories')) {
            $item->categories()->attach([$category1->id, $category2->id, $category3->id]);
        }

        // 1. 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // 期待値：複数選択されたカテゴリが商品詳細ページに表示されている
        $response->assertStatus(200);
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
        $response->assertSee('アウター');
    }
}
