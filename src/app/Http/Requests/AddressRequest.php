<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるかどうかを判断
     */
    public function authorize(): bool
    {
        // ログイン中のユーザーのみ許可するため、true に変更します
        return true;
    }

    /**
     * リクエストに適用するバリデーションルールを定義
     */
    public function rules(): array
    {
        return [
            // 郵便番号：入力必須、かつ「数字3桁 - 数字4桁」の合計8文字を正規表現でチェック
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            // 住所：入力必須
            'address'  => ['required', 'string'],
        ];
    }

    /**
     * エラーメッセージの日本語化定義
     */
    public function messages(): array
    {
        return [
            'postcode.required' => '郵便番号は入力必須です。',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）。',
            'address.required'  => '住所は入力必須です。',
        ];
    }
}
