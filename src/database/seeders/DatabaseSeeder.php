<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */

    //アプリ起動した直後、開発者やテストユーザーがすぐログインして動作確認できるよう
    // データーベースに1件テストアカウントを確実に用意するための記述
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト太郎',
                'password' => Hash::make('password123'),
            ]
        );

        // 商品の初期データを入れる処理（ItemSeeder）を呼び出す
        $this->call([
            ItemSeeder::class,
        ]);
    }
}
