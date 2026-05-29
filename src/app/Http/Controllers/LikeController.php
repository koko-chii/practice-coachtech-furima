<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    //商品に対していいねを付けたり外したりするための仕組み
    public function toggleLike($item_id)
    {
        //ログインユーザーが未認証かチェックし、未認証の場合はエラー表示になる記述だが
        //実際の動きはlaravelが安全にログイン画面へ誘導している
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        //認証されたユーザーの場合、いいねを付けたり外したりできる
        $user->likedItems()->toggle($item_id);

        //商品といいね登録とユーザーは紐づいている
        $is_liked = $user->likedItems()->where('item_id', $item_id)->exists();

        //指定した商品に($item_id)いいねのカウントがデーターベースから集計し、
        // 変数 $like_countに代入する処理
        $like_count = \App\Models\Item::findOrFail($item_id)->likedByUsers()->count();

        // いいねの切替結果と、合計いいね数は、JavaScriptに送り返す処理
        //これにより、リロードせずともリアルタイムにいいねを動作し確認できる
        return response()->json([
            'is_liked' => $is_liked,
            'like_count' => $like_count
        ]);
    }
}
