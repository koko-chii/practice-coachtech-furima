<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //自動テスト用コメントを作成する場合に使う
    use HasFactory;

    //コメントを作成するとき、ユーザー情報と商品情報を確認して
    // データーベースにコメントと一括保存する
    protected $fillable = [
        'user_id',
        'item_id',
        'comment',
    ];

    //コメントを書いた人とユーザーモデルを紐づける
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //コメントがどの商品についたものか、商品モデルと紐づける
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
