<?php

namespace App\Http\Requests;

//laravelの認証パッケージLoginRequest as FortifyLoginRequestをこのファイルで使用する
//自作するファイルとの名前の一致を防ぐためas FortifyLoginRequestを末尾につけている
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Models\User;
//laravelが用意したHashファイル(パスワード暗号化ツール)を
// このLoginRequestファイルで使用できるようにしている
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FortifyLoginRequest
{
    //これはログインを行うための処理なので、未認証のユーザーも全員がアクセスできる
    public function authorize(): bool
    {
        return true;
    }

    //ログインルールは、メール形式のメールアドレス必須、パスワード必須を設定
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    //エラーの場合、日本語のメッセージを表示
    public function messages(): array
    {
        return [
            'email.required'    => 'メールアドレスを入力してください',
            'email.email'       => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
