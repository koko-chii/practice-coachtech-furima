<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemSearchRequest extends FormRequest
{
    //検索機能については、ログイン認証されていなくても誰でも可能
    public function authorize()
    {
        return true;
    }

    //商品検索機能は空欄での全検索が考慮されており、
    // 部分一致のキーワードが入力された場合文字列255字以内
    public function rules()
    {
        return [
            'tab' => 'nullable|string',
            'keyword' => 'nullable|string|max:255',
        ];
    }

    //検索された商品タブを取得（指定が無い場合はおすすめを取得）
    // コントローラーがデーターベースから商品を検索して表示させる前の準備
    public function getTab()
    {
        return $this->query('tab', 'recommend');
    }
    //画面から入力された検索キーワードを取得する（部分一致検索）準備
    public function getKeyword()
    {
        return $this->query('keyword');
    }
}
