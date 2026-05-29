<?php

namespace App\Http\Middleware;

use Closure;                 // 次の処理へ進むためのバトン（クロージャ）
use Illuminate\Http\Request;  // 画面から届いたリクエストデータ（URLや入力内容）を扱う道具
use Illuminate\Support\Facades\Auth; // ログインしているユーザーの情報を扱う道具

// プロフィール登録が完了しているかを厳しくチェックする「関門（ミドルウェア）」クラス
class EnsureProfileIsCompleted
{
    public function handle(Request $request, Closure $next)
    {
        // 今ログインしているユーザーの情報を丸ごと引っ張ってきて、変数「$user」に保存する
        $user = Auth::user();

        //「ログインしている」かつ「その人の郵便番号（postcode）が空っぽ」の場合
        if ($user && empty($user->postcode)) {
            // プロフィール編集画面へ強制的にリダイレクトさせる
            return redirect()->route('profile.edit');
        }

        // 無事にチェックを突破した場合、次の処理（本来行きたかったページ）へバトンを渡す
        return $next($request);
    }
}