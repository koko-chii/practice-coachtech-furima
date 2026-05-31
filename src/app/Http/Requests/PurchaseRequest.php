<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;
//laravelが用意している認証しているユーザーの情報を取得する機能をこのファイルで使用する
use Illuminate\Support\Facades\Auth;

//laravelが用意しているFormRequest機能を購入時に親子継承するクラス(設計図)設定
class PurchaseRequest extends FormRequest
{
    //ログイン認証はミドルウェアで済んでいるので誰でも権限あり通過可能
    public function authorize(): bool
    {
        return true;
    }

    //購入時の支払い方法はプルダウンで選択することが必須
    public function rules(): array
    {
        return [
            'payment_method' => ['required'],
        ];
    }

    //エラーの場合メッセージを日本語で表示する設定
    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }

    //購入時に($this->route('item_id')で商品情報をデーターベースから探し出し
    // user_id === Auth::idでユーザー情報とログイン情報の確認し、自分が出品した商品購入を防ぎ、
    // 売切れ商品の二重購入も防ぐ
    // エラーの場合それぞれの日本語のエラーメッセージを設定
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $item = Item::findOrFail($this->route('item_id'));

            if ($item->user_id === Auth::id()) {
                $validator->errors()->add('error', '自分の商品は購入できません');
            }

            if ($item->is_sold) {
                $validator->errors()->add('error', 'この商品は既に売り切れています');
            }
        });
    }
}

