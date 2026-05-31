<?php

namespace App\Http\Requests;

//laravelが用意したバリデーションチェックのファイルの住所venderの奥深くにある
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    //このひと塊は配送先住所変更のデータが送られてきた場合の権限をチェックし、
    //ログイン認証済みユーザーの画面のため、全員が変更可能となっている
    public function authorize(): bool
    {
        return true;
    }

    //このひと塊はバリデーションルールを設定しており
    // チェックをクリアしたデータは次のデーター保存にすすむ
    public function rules(): array
    {
        return [
            // 郵便番号は入力必須かつ「数字3桁 - 数字4桁」の合計8文字のバリデーション
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            // 住所は入力必須
            'address'  => ['required', 'string'],
        ];
    }

    //バリデーションチェックに通過できないデータは以下のエラーメッセージを
    // 日本語で表示する指定をしている
    public function messages(): array
    {
        return [
            'postcode.required' => '郵便番号は入力必須です。',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）。',
            'address.required'  => '住所は入力必須です。',
        ];
    }
}
