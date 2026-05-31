<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\ItemSearchRequest;
//ユーザーが入力した値(コメントパスワード画像等)を安全に丸ごと受取るための宣言
use Illuminate\Http\Request;
//ログインしているかチェックし、ログインユーザーの情報を取得できる命令
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    //ユーザーから送られてきた検索データーに
    // ItemuSearchReqestのバリデーションチェックを行い、
    // 安全なデータを受取るための設定
    public function index(ItemSearchRequest $request)
    {
        //ログイン中のユーザーが、メール認証と住所登録が未完了であれば、
        // それぞれ続きの画面へリダイレクトさせる処理
        $user = Auth::user();
        if ($user) {
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if (empty($user->postcode)) {
                return redirect('/mypage/profile');
            }
        }

        //ユーザーが選択したタブの情報を取得して＄tabに入れ下準備する処理
        $tab = $request->getTab();
        //ユーザーが検索窓に入力したキーワードの文字列を取得し、
        // $keywordに入れ判断材料をアプリに取り込む処理
        $keyword = $request->getKeyword();
        //それらを掛け合わせ、データーベースから検索するため、空っぽの受け皿を準備している
        $query = Item::query();

        //マイリストタブを選択したら、認証ユーザーはいいねした商品を表示し、
        // 未認証ユーザーは何も表示しないように制御している
        if ($tab === 'mylist') {
            if ($user) {
                $query = $user->likedItems()->where('items.user_id', '!=', $user->id);
            } else {
                $query->where('id', 0);
            }
        //マイリストタブでないなら、ログインユーザーには、自分が出品した商品は表示させない処理
        //'!=', $user->id ユーザーに紐づいていないものだけの条件を受け皿($qery)に足に足す
        } else {
            if ($user) {
                $query->where('user_id', '!=', $user->id);
            }
        }

        //検索窓に($keyword)入力したキーワードが('%' . $keyword . '%')商品名(name)に
        // 一部でも含まれている(LIKE)商品だけを絞り込む
        if ($keyword) {
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }
        //一致した商品を表示する
        $items = $query->get();

        //indexテンプレートファイルを呼び出し、$itemsデーターと、
        // ユーザーが選択したタブやキーワード情報を、画面に引き渡し表示させる処理
        return view('index', [
            'items' => $items,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
    }

    //引数の($item_id)商品識別番号で指定された商品の詳細画面を表示させる関数
    public function show($item_id)
    {
        //ログインユーザーがメール認証と住所登録が未完了の場合は、手続き画面へ誘導する
        //そのチェックを通過、又は未ログイン等の一般ユーザーに指定された商品詳細画面を表示する
        $user = Auth::user();

        if ($user) {
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if (empty($user->postcode)) {
                return redirect('/mypage/profile');
            }
        }

        $item = Item::findOrFail($item_id);

        return view('item_detail', compact('item'));
    }
}
