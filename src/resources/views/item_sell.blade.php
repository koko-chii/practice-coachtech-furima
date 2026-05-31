{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（item_sell.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/item_sell.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の出品フォームをカチッとはめ込む指示 --}}
@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    {{-- 【エラーの一括表示】もしバリデーション（入力チェック）でエラーが「1つでも（any）」発生した場合 --}}
    @if ($errors->any())
        <div class="error-container">
            <ul class="error-list">
                {{-- 発生したすべてのエラー（all）を上から順番に箇条書きリストとしてループ表示する --}}
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 商品登録処理を行うURL（route）へ、入力情報を安全な通信（POST）で送信するフォーム --}}
    {{-- 【超重要】「enctype="multipart/form-data"」がついているおかげで、画像などのファイルデータをサーバーへ送信できるようになります --}}
    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための暗号化トークン自動発行必須マーク --}}
        @csrf
        
        {{-- 画像アップロードセクション --}}
        <section class="sell-section">
            <div class="form-group-image">
                {{-- ラベルタグで囲むことで、文字をクリックしても裏側の「ファイル選択input」が連動して起動するアクセシビリティ対応 --}}
                <label class="image-select-label">
                    画像を選択する
                    <input type="file" name="img_url" id="img_url" class="file-input">
                </label>
            </div>
        </section>

        {{-- 商品の詳細設定セクション --}}
        <section class="sell-section">
            <h2 class="sell-section-title">商品の詳細</h2>

            {{-- カテゴリー選択（複数選択可能なチェックボックス） --}}
            <div class="form-group">
                <label for="category" class="form-label">カテゴリー</label>
                <div class="category-group">
                    {{-- シーダー等で用意した共通のカテゴリーリストをループ処理で全て並べる --}}
                    @foreach($categories as $category)
                        <label class="category-label">
                            {{-- 「name="category_ids[]"」と末尾に配列の括弧をつけることで、複数選んだカテゴリーIDをまとめてサーバーに送れます --}}
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 商品の状態選択（セレクトボックス） --}}
            <div class="form-group">
                <label for="condition" class="form-label">商品の状態</label>
                <select name="condition" id="condition" class="form-select">
                    <option value="">選択してください</option>
                    <option value="良好">良好</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="状態が悪い">状態が悪い</option>
                </select>
            </div>
        </section>

        {{-- 商品名と説明文セクション --}}
        <section class="sell-section">
            <h2 class="sell-section-title">商品名と説明</h2>

            <div class="form-group">
                <label for="name" class="form-label">商品名</label>
                {{-- エラー時に打ち直さなくて済むよう、お守りの old() 関数を入力値に設定 --}}
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input">
            </div>

            <div class="form-group">
                <label for="brand" class="form-label">ブランド名</label>
                <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="form-input">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">商品の説明</label>
                {{-- textareaタグはvalue属性ではなく、タグとタグの間に old() を挟み込むルール --}}
                <textarea name="description" id="description" class="form-textarea">{{ old('description') }}</textarea>
            </div>
        </section>

        {{-- 販売価格セクション --}}
        <section class="sell-section">
            <h2 class="sell-section-title">販売価格</h2>
            <div class="form-group">
                <label for="price" class="form-label">販売価格</label>
                {{-- 以前CSSでデザインした、¥マークと一体型に見える金額入力欄の構造 --}}
                <div class="price-input-container">
                    <span class="price-symbol">¥</span>
                    {{-- 不正な文字列が入らないよう、数値専用の type="number" に指定 --}}
                    <input type="number" name="price" id="price" value="{{ old('price') }}" class="form-input price-field">
                </div>
            </div>
        </section>

        {{-- 出品確定ボタン --}}
        <div class="form-submit">
            <button type="submit" class="sell-btn">出品する</button>
        </div>
    </form>
</div>

{{-- 選択した画像のプレビュー表示などを制御するJavaScriptファイルを読み込み --}}
<script src="{{ asset('js/sell.js') }}"></script>

@endsection
