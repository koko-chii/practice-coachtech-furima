<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;

//トップ画面にアクセスしたら商品一覧画面(item.index)を表示する。
Route::get('/', [ItemController::class, 'index'])->name('item.index');
//'/item/{item_id}'にアクセスしたらアイテム詳細画面('item.show')を表示する
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

//ログインとメール認証が完了したユーザーがアクセスできるグループ
// '/mypage/profile'にアクセスしたらプロフィール登録・更新画面を表示し、データーベースに保存・更新する
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

//ログインとメール認証とプロフィール登録を完了したユーザーが通過できるグループ
Route::middleware(['auth', 'verified', 'ensure.profile.completed'])->group(function () {

    //ログイン完了ユーザーが'/mypage'にアクセスしたら商品一覧画面のマイページタブが表示させる
    Route::get('/mypage', [ProfileController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('mypage');

    //'/sell'にアクセスしたら出品画面('sell')を表示させる。
    // 出品ボタンが押されたらデーターベースに保存する('item.store')
    Route::get('/sell', [SellController::class, 'sell'])->name('sell');
    Route::post('/sell', [SellController::class, 'store'])->name('item.store');

    //'/purchase/{item_id}'にアクセスしたら購入画面('purchase.show')を表示させる
    //商品購入ボタンを押したら、データーベースに保存する('purchase.store')
    //購入完了したら、購入完了画面を表示する('purchase.success')
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchasePage'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.store');
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');

    //'/comment/{item_id}/comment'コメント送信画面が押されたら、データーベースに保存する('comment.store')
    Route::post('/comment/{item_id}/comment', [CommentController::class, 'storeComment'])->name('comment.store');

    //'/like/{item_id}/like'いいねボタンを押したら、いいねのON/OFFが切り替えられる
    Route::post('/like/{item_id}/like', [LikeController::class, 'toggleLike'])->name('like.toggle');

    //'/purchase/address/{item_id}'に配送先住所変更ボタンを押したら、変更画面を表示する
    //配送先住所を更新したら、データーベースに保存・更新される
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    //'/purchase/payment/store-session'支払方法の選択をしたら、そのお情報をサーバーにセッション記録する(一時補保存)'storePaymentSession'
    Route::post('/purchase/payment/store-session', [PurchaseController::class, 'storePaymentSession']);
});

//メール認証完了していないログインユーザーはメール認証画面にもどす
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//ログインユーザーがメール認証ボタンを押したら、開発用のメール受信箱（Mailpit）の画面へ遷移する
Route::get('/email/go-to-mailhog', function () {
    return redirect('http://localhost:8025');
})->middleware('auth')->name('verification.show');
