<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller<?php

//このファイルがどのフォルダに置いてあるかを示す住所
namespace App\Http\Controllers;

//他のフォルダにあるファイルを、このファイル内で使うための宣言
//本来なら毎回 App\Models\Itemのようにコード内に書く必要があるのを呼び出せる
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

//Controller(親)要素を引き継いでCommentController(子)を作る
//laravelのControllerにはWebページを表示したりデータを送ったりする機能が備わっている
//extends Controllerを書くことで
// returun(戻り値)や
// redirect(別の画面へとばす)
// $requesst(データ受取ための変数)を使えるようになる
class CommentController extends Controller
{
    public function storeComment(CommentRequest $request, $item_id)
    {
        //もしログイン認証していなかったら、ログイン画面に自動転送させる処理
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        //指定された商品が存在するか確認し、
        // ログイン中のユーザーが
        // 投稿したコメントを$item->comments()->create というリレーション（紐付け）機能で
        // その商品に紐付けし、データーベースに保存する処理
        $item = Item::findOrFail($item_id);
        $item->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        //コメントの保存をしたら、back()でユーザーがもともといた画面に引き返さる
        //with()という仕組みでコメントを投稿しましたというメッセージを一緒に受け渡す
        return back()->with('message', 'コメントを投稿しました');
    }

}

{
    public function storeComment(CommentRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($item_id);
        $item->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return back()->with('message', 'コメントを投稿しました');
    }

}
