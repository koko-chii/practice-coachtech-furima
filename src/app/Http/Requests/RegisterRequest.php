<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//laravel標準のFormRequestを親子継承した会員登録機能のバリデーション設計図を作成
class RegisterRequest extends FormRequest
{
    //会員登録機能なので誰でも権限通過しで可能
    public function authorize(): bool
    {
        return true;
    }

    //システムの安全性とユーザーの入力ミスを防ぐ会員登録のルール設定
    //名前入力必須、文字列20字以内
    //メールアドレス必須、メール形式で255字以内、未登録アドレス
    //パスワード入力必須、8文字以上、確認用パスワードとの入力一致
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:20'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => 'お名前を入力してください',
            'name.max'           => 'お名前は20文字以内で入力してください',
            'email.required'     => 'メールアドレスを入力してください',
            'email.email'        => 'メールアドレスはメール形式で入力してください',
            'email.unique'       => 'このメールアドレスは既に登録されています',
            'password.required'  => 'パスワードを入力してください',
            'password.min'       => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードと一致しません',
        ];
    }

    protected function prepareForValidation()
    {
        // 特殊な処理が必要なければ、メッセージのキー指定だけで十分です。
    }
}
