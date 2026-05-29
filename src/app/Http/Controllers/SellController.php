<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class SellController extends Controller
{
    //商品出品画面を表示させるための処理が書かれている
    public function sell()
    {
        //エロクアント(Eloquent)というlaravelの仕組みを使い、
        // データーベースのcategoriesテーブルから、データを全て取得し、
        // 変数$categoriesに代入する
        $categories = Category::all();

        //item_sell.blade.phpをブラウザに表示し、カテゴリーのデーターを画面へ一緒に送る
        return view('item_sell', compact('categories'));
    }

    //出品画面から送信された入力データに対してバリデーション（入力チェック）を行い、
    // 安全性が確認できたデータを受け取って保存をする一塊
    public function store(ExhibitionRequest $request)
    {
        // コントローラー側では、万が一のシステムエラーを防ぐための安全策として
        // if 文により画像があるか確認し、画像をデーターベースに保存する
        $path = null;
        if ($request->hasFile('img_url')) {
            $path = $request->file('img_url')->store('items', 'public');
        }

        //エロクアント(Eloquent)のItemモデルクラスを使い、
        // 送信された出品情報をデータベースの items テーブルに保存する
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'img_url' => $path,
            'condition' => $request->condition,
            'brand' => $request->brand,
            'is_sold' => false,
        ]);

        //カテゴリーが選択されれば、保存した商品に対し複数のカテゴリーを紐づけ（中間テーブルへ保存）
        // 出品完了メッセージと共にトップページへリダイレクトさせる
        if ($request->category_ids) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect('/')->with('message', '商品を出品しました');
    }
}
