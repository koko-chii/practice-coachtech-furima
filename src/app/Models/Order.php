<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // データーベースにある全ての項目を保存・更新する
    protected $guarded = [];

    // 商品とオーダーのリレーションの記述がしてあり、
    // 注文データーから紐づいている商品を取得できるようにする設定
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
