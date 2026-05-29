<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    // 購入前商品情報取得機能：購入画面を表示する
    public function showPurchasePage($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if (session('last_item_id') != $item_id) {
            session()->forget('payment_method');
        }

        session(['last_item_id' => $item_id]);

        return view('purchase', compact('item', 'user'));
    }

    // 支払い方法選択機能：Stripeの決済画面に接続する
    public function purchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // Stripeの設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // 指示：コンビニ・カード両対応の決済画面を作成
        $session = Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount'  => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // 決済成功時の戻り先を指定
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url'  => route('purchase.show', ['item_id' => $item->id]),
        ]);

        // 購入時の住所をセッションに一時保存（決済成功後の紐付けに使用）
        session(['shipping_address' => [
            'postcode' => $user->postcode,
            'address'  => $user->address,
            'building' => $user->building,
        ]]);

        // Stripeの決済画面へリダイレクト
        return redirect($session->url);
    }

    // 商品購入機能：決済成功後のDB更新処理
    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $shipping = session('shipping_address');

        // 要件：購入した商品を "sold" に更新
        $item->update(['is_sold' => true]);

        \App\Models\Order::create([
            'user_id'  => $user->id,
            'item_id'  => $item->id,
            'postcode' => $shipping['postcode'] ?? $user->postcode,
            'address'  => $shipping['address'] ?? $user->address,
            'building' => $shipping['building'] ?? $user->building,
        ]);

        session()->forget(['payment_method', 'shipping_address']);

        // 要件：遷移先は商品一覧画面
        return redirect('/')->with('message', '商品を購入しました');
    }

    // 4. 配送先変更機能：送付先住所変更画面を表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        return view('purchase_address', compact('item', 'user'));
    }

    // 配送先変更機能：住所を更新して購入画面に戻る
    public function updateAddress(Request $request, $item_id)
    {
        $user = Auth::user();
        $user->update([
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    // 支払い方法選択機能：JavaScriptからのセッション保存
    public function storePaymentSession(Request $request)
    {
        session(['payment_method' => $request->payment_method]);
        return response()->json(['success' => true]);
    }
}

