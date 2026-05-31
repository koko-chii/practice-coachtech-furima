{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（purchase_address.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/purchase_address.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の住所変更フォームをカチッとはめ込む指示 --}}
@section('content')
<div class="address-container">
    <h1>住所の変更</h1>

    {{-- 【超重要】住所更新を行うURL（route）へ、入力情報を安全な通信（POST）で送信するフォーム --}}
    {{-- 配列の形で「'item_id' => $item->id」を渡すことで、住所を変更した後に『元の商品の購入確認画面』へ迷子にならずに戻れるようにしています --}}
    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための、Laravel必須の暗号化トークン自動発行お守り --}}
        @csrf
        
        {{-- 郵便番号の入力ブロック --}}
        <div>
            <label for="postcode">郵便番号</label>
            {{-- 【こだわり】old()関数の第2引数に「$user->postcode」をセットすることで、画面を開いた瞬間に『現在の登録住所』が最初から自動で入力されている親切設計にしています --}}
            <input type="text" name="postcode" id="postcode" value="{{ old('postcode', $user->postcode) }}" required>
        </div>

        {{-- 住所の入力ブロック --}}
        <div>
            <label for="address">住所</label>
            {{-- 郵便番号と同様に、現在の登録住所を初期値として表示させ、書き換えたい部分だけをユーザーが直せるようにしています --}}
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" required>
        </div>

        {{-- 建物名の入力ブロック --}}
        <div>
            <label for="building">建物名</label>
            {{-- 建物名はデータベースの設計（マイグレーション）時に「nullable（空欄OK）」にしたため、ここでは必須マーク（required）をつけていません --}}
            <input type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
        </div>

        {{-- 更新確定ボタン --}}
        <button type="submit">更新する</button>
    </form>
</div>
@endsection
