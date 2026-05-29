<?php

namespace App\Models;

//laravel標準のModel、
//データーベースのテーブルとプログラムを結びつけSQLを直接書かず
// PHPのコードでデーターを操作する機能(ORマッパー)をこのファイルで使用することで
//バグの少ない綺麗なコードが書けるようになる
use Illuminate\Database\Eloquent\Model;
//必要な場合はlaravel標準のHasFactory自動テストダミーデーター作成機能を使用できる
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    //自動テスト用のカテゴリーを作成する場合に使用する
    use HasFactory;

    //CategoryモデルとItemモデルの間の多対多(belongsToMany)のリレーショ(データ連携)を定義。
    // 1つの商品に複数のカテゴリーが付き、1つのカテゴリーに複数の商品が属する
    // コントローラー側から『このカテゴリーに属する商品一覧をすべて取得する』という処理を、
    // Laravelの機能（Eloquent）でシンプルに実装できる
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
