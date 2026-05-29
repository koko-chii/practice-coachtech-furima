<?php

namespace Database\Factories;

//larael標準のダミーデーターを自動生成する機能を使うための呼込み
use Illuminate\Database\Eloquent\Factories\Factory;

//Factoryを親子継承したItemFactoryクラス設定はデーターベースへの保存や
// データ大量生成といった基本機能を引き継ぐ
class ItemFactory extends Factory
{
    //ここは商品のダミーデーターとして具体的な項目や値を入れる場所
    public function definition(): array
    {
        //テスト用ダミーデーターとして名前や価格、商品説明、商品状態、
        // 商品画像、売切れかどうかをセットし、
        // 出品者ユーザーも自動で作成されています
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト説明文',
            'condition' => '良好',
            'img_url' => 'https://example.com',
            'is_sold' => false,
        ];
    }
}
