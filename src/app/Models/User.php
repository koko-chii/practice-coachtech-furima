<?php

namespace App\Models;

//laravel標準のメール認証機能ルールをこのファイルに呼びだす
//（実際のメール認証はこのコードではなく別ファイル（.env）でlaravel推奨のMailpitと連携させている）
//メール認証済かの確認、認証済みの場合は更新、未認証の場合は認証メールの送信、登録完了の場合はアドレス取得
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//自作のUserモデルと被らないようas Authenticatableを末尾につけたlaravel標準User機能は
//ログインとパスワードの暗号化の機能がそなわっており、このファイルに呼び出し、web.phpのAuth機能を使用している
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//ログイン機能（Authenticatable）を親子継承し、メール認証機能（MustVerifyEmail）を必須にしたUserクラス
class User extends Authenticatable implements MustVerifyEmail
{
    //テスト用のダミーデーター作成機能と（HasFactory）、メールの通知機能（Notifiable）を設定
    use HasFactory, Notifiable;

    //ユーザーから送信された名前・メールアドレス・パスワード・郵便番号・住所・建物名を一括で保存・更新する許可
    protected $fillable = [
        'name',
        'email',
        'password',
        'postcode',
        'address',
        'building',
    ];

    //パスワードやログイン用トークン(ログインメモリ)を非公開にし、ハッキングを防ぐ
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //データーベースから取得したデータ形式を自動変換(casts)する設定
    //メール認証日時を扱いやすくデータ変換し、パスワードを暗号化する
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //中間テーブルのlikesテーブルを間にはさんで、いいねされた商品情報を全部取得する
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes');
    }

    //ログインユーザーとそのユーザーが出品した商品一覧を紐づける1対多のリレーション設定
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    //ログインユーザーとそのユーザーの購入商品一覧を中間テーブルのorderテーブルを介して紐づける
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'orders', 'user_id', 'item_id');
    }
}
