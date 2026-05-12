<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるかどうかを判断
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * リクエストに適用するバリデーションルールを定義
     */
    public function rules(): array
    {
        return [
            // 商品名：入力必須
            'name'        => ['required', 'string', 'max:255'],
             // 商品の説明：入力必須、255文字以内（仕様書の制限）
            'description' => ['required', 'string', 'max:255'],
             // 商品画像：入力必須、画像ファイル、拡張子は.jpegまたは.pngのみ
            'img_url'     => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            // 商品のカテゴリー：入力必須（仕様書の制限）
            'category_ids'    => ['required'],
              // 商品の状態：入力必須
            'condition'   => ['required'],
             // ブランド名：必須ではない
            'brand'       => ['nullable', 'string', 'max:255'],
            // 商品価格：入力必須、数値型、0円以上
            'price'       => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * エラーメッセージの日本語化定義
     */
    public function messages(): array
    {
        return [
            'name.required'        => '商品名を入力してください。',
            'description.required' => '商品の説明を入力してください。',
            'description.max'      => '商品の説明は255文字以内で入力してください。',
            'img_url.required'     => '商品画像を選択してください。',
            'img_url.image'        => '画像ファイルを選択してください。',
            'img_url.mimes'        => '商品画像の拡張子は .jpeg もしくは .png のみ有効です。',
            'category_ids.required'    => '商品のカテゴリーを選択してください。',
            'condition.required'   => '商品の状態を選択してください。',
            'price.required'       => '販売価格を入力してください。',
            'price.integer'        => '販売価格は数値で入力してください。',
            'price.min'            => '販売価格は0円以上で入力してください。',
        ];
    }
}
