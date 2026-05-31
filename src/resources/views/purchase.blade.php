{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（purchase.blade.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.blade.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の購入確認フォームをカチッとはめ込む指示 --}}
@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <!-- 左側：詳細設定エリア（商品情報・支払い方法・配送先） -->
        <div class="purchase-main">
            <!-- 商品情報表示ブロック -->
            <div class="item-info">
                <div class="item-image">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-detail">
                    <h1>{{ $item->name }}</h1>
                    {{-- 「number_format()」のおかげで、価格の数字に「3桁ごとのカンマ（,）」が自動で付く親切設計 --}}
                    <p>¥ {{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr>

            <!-- 支払い方法セクション -->
            <div class="selection-section">
                <h2>支払い方法</h2>
                <div class="select-wrapper">
                    <select name="payment_method_select" id="payment-select" class="select-box">
                        {{-- セッションに支払い方法が保存されていない場合は、「選択してください」を初期値（selected）にする --}}
                        <option value="" disabled {{ !session('payment_method') ? 'selected' : '' }}>選択してください</option>
                        {{-- 画面が切り替わっても、ユーザーが直前に選んだ支払い方法をセッションから読み取って自動で選択状態にする仕掛け --}}
                        <option value="コンビニ払い" {{ session('payment_method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="カード支払い" {{ session('payment_method') == 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                </div>
            </div>

            <hr>

            <!-- 配送先セクション -->
            <div class="selection-section">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>配送先</h2>
                    {{-- 配送先変更画面へ行くためのボタンリンク。どの商品を買おうとしているかのID（item_id）を引数として引き渡す --}}
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="change-link">変更する</a>
                </div>
                {{-- 現在ログインしているユーザーの配送先住所をデータベースから動的に表示 --}}
                <div class="address-display">
                    <p>〒 {{ $user->postcode }}</p>
                    <p>{{ $user->address }}{{ $user->building }}</p>
                </div>
            </div>

            <hr>
        </div>

        <!-- 右側：決済確認エリア（ボタンをボックスの外に配置） -->
        <div class="purchase-side-area">
            {{-- 内訳を表示する白い枠線のボックス --}}
            <div class="purchase-side-box">
                <table class="summary-table">
                    <tr>
                        <th>商品代金</th>
                        <td>¥ {{ number_format($item->price) }}</td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        {{-- 【こだわり】セッションにデータがあればそれを表示し、無ければ（?:）「未選択」と出すことで直感的に伝える工夫 --}}
                        <td id="display-payment">{{ session('payment_method') ?: '未選択' }}</td>
                    </tr>
                </table>
            </div>

            {{-- 実際の購入確定処理（POST通信）をコントローラー（purchase.store）へ送るためのフォーム --}}
            <form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="POST" class="purchase-form">
                {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための暗号化トークン自動発行必須マーク --}}
                @csrf
                {{-- JavaScriptと連動させ、ユーザーが選んだ支払い方法の文字をこの隠し入力項目（hidden）にカチッと当てはめて裏側で送信する仕組み --}}
                <input type="hidden" name="payment_method" id="hidden-payment" value="{{ session('payment_method') }}">

                {{-- 【こだわり】セッションに支払い方法が残っているときだけボタンを押せるようにし、未選択のときは「disabled」でクリックを徹底ガードする安全設計 --}}
                <button type="submit" class="purchase-button" id="purchase-btn" {{ session('payment_method') ? '' : 'disabled' }}>
                    購入する
                </button>
            </form>
        </div>
    </div>
</div>

{{-- セレクトボックスの選択変更を検知して、右側の内訳や隠し項目に一瞬で文字をコピー・連動させるためのJavaScriptファイルを読み込み --}}
<script src="{{ asset('js/purchase.js') }}"></script>
@endsection
