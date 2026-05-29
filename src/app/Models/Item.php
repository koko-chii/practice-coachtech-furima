<?php

namespace App\Models;

//laravel標準のデーターベースの情報を取得・保存・更新・削除等の操作できる機能をこのファイルで使用
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Comment;

//laravel標準のModel機能を親子継承したItemModel
class Item extends Model
{
    //自動テスト商品登録を使う場合に使用する
    use HasFactory;

    //商品登録と同時に商品名、価格、ブランド名、商品説明、商品画像、商品状態、ユーザー情報、売切れ等の
    //一括保存を許可された項目名リスト（オーダー）
    protected $fillable = [
        'name','price','brand','description','img_url','condition','user_id','is_sold'
    ];

    //この商品に投稿されたたくさんのコメントデーターを取得する。1対多の紐づけ。
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    //この商品にされた沢山のいいね情報をデーターベースから取得する。多対多の使用実現。
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    //この商品を出品したユーザー情報を取得する。1対多の紐づけ。
    public function user()

    {
        return $this->belongsTo(User::class);
    }

    //この商品のカテゴリー情報を取得する。多対多のリレーション設定。
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
