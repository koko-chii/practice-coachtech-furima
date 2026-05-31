{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（index.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の商品一覧画面をカチッとはめ込む指示 --}}
@section('content')
<div class="index-container">
    
    {{-- 画面上のタブ切り替えエリア（おすすめ／マイリスト） --}}
    <div class="index-tabs">
    {{-- 【こだわり】現在URLにある検索キーワードなどの情報（query）を維持したまま、タブ（tab）の指定だけをリセットして「おすすめ」のURLを組み立てる記述 --}}
    {{-- URLに「?tab=」がついていない（!has）場合は、このタブを光らせる（activeクラスを付与） --}}
    <a href="/?{{ http_build_query(array_merge(request()->query(), ['tab' => null])) }}"
        class="tab-item {{ !request()->has('tab') ? 'active' : '' }}">おすすめ</a>

    {{-- 【こだわり】同じく検索キーワードなどの情報を維持したまま、タブの指定だけを「mylist」に書き換えたURLを組み立てる記述 --}}
    {{-- URLの「tab」の値が「mylist」の時は、このタブを光らせる（activeクラスを付与） --}}
    <a href="/?{{ http_build_query(array_merge(request()->query(), ['tab' => 'mylist'])) }}"
        class="tab-item {{ request()->get('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

    {{-- 商品をタイル状に並べるエリア --}}
    <div class="product-grid">
        {{-- コントローラーから届いた複数の商品データ（$items）から、1件ずつ商品（$item）を取り出して繰り返し表示する --}}
        @foreach($items as $item)
            {{-- 各商品カード全体をリンクにし、クリックするとその商品の詳細画面（/item/商品ID）に飛べるように設定 --}}
            <a href="/item/{{ $item->id }}" class="product-card">
                <div class="product-image-wrapper">
                    {{-- データベースに保存されている画像パス（img_url）を、Laravelの公開フォルダ（storage）のパスと合体させて画像を表示 --}}
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">

                    {{-- 【状態の検知】もしこの商品の「is_sold（売切フラグ）」が真（true / 1）だった場合だけ --}}
                    @if($item->is_sold)
                        {{-- 画像の上に重なるように「Sold」のバッジを表示させる --}}
                        <div class="sold-badge">Sold</div>
                    @endif
                </div>
                {{-- 商品の名前を表示 --}}
                <p class="product-name">{{ $item->name }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
