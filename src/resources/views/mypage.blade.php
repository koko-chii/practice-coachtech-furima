{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSSを共通の土台へ送り込む設定。トップページのCSSを賢く使い回しているのがポイント！ --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}"> {{-- 商品一覧用のグリッドやタブの見た目を再利用 --}}
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}"> {{-- プロフィールヘッダー専用の見た目を適用 --}}
@endpush

{{-- 共通の土台のメインエリアに、以下のマイページ中身をカチッとはめ込む指示 --}}
@section('content')
<div class="main-container">
    
    {{-- ユーザーのアイコンと名前、編集ボタンが並ぶ上部ヘッダーエリア --}}
    <div class="profile-header">
        <div class="profile-info">
            {{-- ユーザーの丸いアバター画像を表示する枠 --}}
            <div class="profile-image">
                {{-- 【状態の検知】もしユーザーがプロフィール画像を登録していた（img_urlが空ではない）場合だけ表示 --}}
                @if($user->img_url)
                    <img src="{{ asset('storage/' . $user->img_url) }}" alt="プロフィール画像">
                @endif
            </div>
            {{-- 現在ログインしているユーザーの名前をデータベースから動的に表示 --}}
            <h1 class="user-name">{{ $user->name }}</h1>
        </div>
        {{-- プロフィール編集画面（profile.edit）へ移動するためのボタンリンク --}}
        <a href="{{ route('profile.edit') }}" class="btn-profile-edit">プロフィールを編集</a>
    </div>

    {{-- 「出品した商品」と「購入した商品」の一覧を切り替えるタブエリア --}}
    <div class="index-tabs">
        {{-- URLのパラメータ（?page=）が「buy」ではない場合（つまり、最初から開いた時やpage=sellの時）に、このタブを光らせる（activeクラス付与） --}}
        <a href="/mypage?page=sell" class="tab-item {{ request('page') != 'buy' ? 'active' : '' }}">出品した商品</a>
        {{-- URLのパラメータ（?page=）が「buy」の時に、このタブを光らせる（activeクラス付与） --}}
        <a href="/mypage?page=buy" class="tab-item {{ request('page') == 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    {{-- 条件に合わせてコントローラーが絞り込んでくれた商品データ（$items）を、タイル状に並べるエリア --}}
    <div class="product-grid">
        {{-- 届いた商品リストから1件ずつデータを取り出して、繰り返し画面に並べるループ処理 --}}
        @foreach($items as $item)
            {{-- 各商品カード全体をリンクにし、クリックするとその商品の詳細画面（/item/商品ID）に飛べるように設定 --}}
            <a href="/item/{{ $item->id }}" class="product-card">
                <div class="product-image-wrapper">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    {{-- 【状態の検知】もしその商品がすでに売り切れていたら、画像の上に「Sold」バッジを重ねて表示 --}}
                    @if($item->is_sold)
                        <span class="sold-badge">Sold</span>
                    @endif
                </div>
                <p class="product-name">{{ $item->name }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
