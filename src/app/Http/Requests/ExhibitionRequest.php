<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//laravelのFormRequest機能を継承した出品時のバリデーションクラスを作成記述
class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    //出品時に必要なデータの整合せと安全性を守るため、各項目に適切な制限を設ける
    public function rules(): array
    {
        return [
            // 商品名は入力必須、文字列で255字以内
            'name'        => ['required', 'string', 'max:255'],
             // 商品の説明は入力必須、文字列で255文字以内
            'description' => ['required', 'string', 'max:255'],
             // 商品画像は必須で、ファイルの拡張子は.jpegまたは.pngのみの2MB以内
            'img_url'     => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            // 商品のカテゴリーは入力必須。複数選択可能。
            'category_ids'    => ['required'],
              // 商品の状態：入力必須。プルダウンで選択のため選択もれのみ防ぐ
            'condition'   => ['required'],
             // ブランド名は入力必須ではないが、入力する場合は文字列で255字以内
            'brand'       => ['nullable', 'string', 'max:255'],
            // 商品価格は入力必須、数値型で0円以上
            'price'       => ['required', 'integer', 'min:0'],
        ];
    }

    //エラーの場合は日本語でメッセージを表示する
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
