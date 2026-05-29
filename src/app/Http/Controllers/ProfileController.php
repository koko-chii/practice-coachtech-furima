<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        //URLのクエリパラメータからpageキーの値を取得する処理で
        //指定がない場合は初期表示として出品画面を表示するためsellを代入している
        $page = $request->query('page', 'sell');

        //もし購入した商品ページを開いたときは購入した商品を表示し、
        // それ以外のページ、出品した商品ページを開いたときは出品した商品を表示する
        if ($page === 'buy') {
            $items = \App\Models\Order::where('user_id', $user->id)
                    ->with('item')
                    ->get()
                    ->pluck('item');
        } else {
            $items = $user->items()->get();
        }

        return view('mypage', compact('user','items', 'page'));
    }

    //ログインしているユーザーのプロフィール編集画面を表示する
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    //この関数はプロフィール編集画面から送信されたデータをデーターベースに保存し更新する処理
    //プロフィールリクエストのバリデーションを通過した安全なデータで上書き更新する
    public function update(ProfileRequest $request)
    {
        //ユーザーがログインされているかチェックする
        $user = Auth::user();

        //画像がアップロードされたら、サーバーに保存しユーザー情報と紐づける
        if ($request->hasFile('image')) {
        $path = $request->file('image')->store('profiles', 'public');
        $user->img_url = $path;
    }

        //名前や住所をデーターベースに保存し、処理完了のメッセージと共にトップページへ移動する
        $user->fill([
            'name'     => $request->name,
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ])->save();

        return redirect('/')->with('message', 'プロフィールを設定しました。');
    }
}
