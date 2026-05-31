<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    //プロフィールはweb.phpでログイン認証やメール認証を通過しているので
    // ここで二重の権限はせず無条件で誰でも通過する
    public function authorize(): bool
    {
        return true;
    }

    //プロフィール登録や更新は、安全性や利便性を両立させたルール設定
    // 画像は空欄でもよいが送信された場合はデータ形式jpeg,png
    //名前入力必須、文字列で20字以内
    //郵便番号必須、数字のハイフンありで3字-4字
    //住所は文字列で入力必須
    //建物名は空欄でもよいが入力がある場合は文字列
    public function rules(): array
    {
        return [
            'image'  => 'nullable|image|mimes:jpeg,png',
            'name'     => 'required|string|max:20',
            'postcode' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address'  => 'required|string',
            'building' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image'     => 'プロフィール画像には画像ファイルを指定してください。',
            'image.mimes'     => 'プロフィール画像の拡張子は .jpeg もしくは .png のみ有効です。',
            'name.required'     => 'お名前を入力してください',
            'name.max'          => 'お名前は20文字以内で入力してください。',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）。',
            'address.required'  => '住所を入力してください',
        ];
    }
}
