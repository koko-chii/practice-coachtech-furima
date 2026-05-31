<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- 【重要】JavaScriptなどで非同期通信（Ajaxなど）を行う際、安全な通信であることを示すためのセキュリティ用トークンを発行する設定 --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtechフリマ</title>

    {{-- アプリ全体で使う標準的なリセットCSSや、通知枠用のCSSをここで一括読み込みする --}}
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-message.css') }}">

    {{-- 各子画面（ログイン画面など）から送られてくる、その画面専用のCSS（@pushしたコード）をここに合流させる着地ポイント --}}
    @stack('css')
</head>

<body>
    {{-- アプリ共通のヘッダーエリア --}}
    <header class="header">
        <div class="header__inner">
            {{-- アプリのロゴ。クリックするといつでもトップページ（/）に戻れるリンク --}}
            <a href="/" class="header__logo-link">
                <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" class="header__logo">
            </a>

            {{-- 検索バーエリア：キーワードを打ち込んで検索ボタン（Enter）を押すとトップページに送信される --}}
            <div class="header__search">
                <form action="/" method="GET" class="header__search-form">
                    {{-- 【こだわり】検索した時に、今見ているタブ（おすすめ/マイリスト）の状態を壊さずに維持するための隠し入力項目 --}}
                    <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">

                    <div class="header__search-container">
                        {{-- 「value="{{ request('keyword') }}"」のおかげで、検索した後に検索窓にそのキーワードが残る親切設計 --}}
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="なにをお探しですか？" class="header__search-input">

                        {{-- 【こだわり】もし検索キーワードが入力されている時だけ、検索を1秒でリセットできる「×」クリアボタンを表示する --}}
                        @if(request('keyword'))
                            <a href="{{ url('/') }}?tab={{ request('tab', 'recommend') }}" class="header__search-clear">×</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ナビゲーションメニュー（ログイン状態によって中身が全自動で切り替わる仕組み） --}}
            <nav class="header__nav">
                <ul class="header__nav-list">
                    {{-- 【状態の検知】もしユーザーがすでに「ログインしている状態」だった場合 --}}
                    @if(Auth::check())
                        <li class="header__nav-item">
                            {{-- ログアウト用のフォーム。安全のためにPOST通信でログアウト処理URL（/logout）へ送信する --}}
                            <form action="/logout" method="POST" class="header__logout-form">
                                @csrf
                                <button type="submit" class="header__nav-link header__logout-btn">ログアウト</button>
                            </form>
                        </li>
                        <li class="header__nav-item">
                            <a href="/mypage" class="header__nav-link">マイページ</a>
                        </li>
                        <li class="header__nav-item">
                            <a href="/sell" class="header__sell-btn">出品</a>
                        </li>
                    {{-- 【状態の検知】ユーザーがまだ「ログインしていない状態（ゲスト）」だった場合 --}}
                    @else
                        <li class="header__nav-item">
                            <a href="/login" class="header__nav-link">ログイン</a>
                        </li>
                        <li class="header__nav-item">
                            <a href="/register" class="header__nav-link">会員登録</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    {{-- さきほど作成した、処理の成功・失敗を通知する「フラッシュメッセージ」の部品をここに埋め込む --}}
    @include('components.flash-message')

    {{-- アプリのメイン本文エリア --}}
    <main class="main">
        {{-- 各子画面（@section('content')に書かれた中身）が、ここに全自動でカチッとはめ込まれて表示される最重要スポット --}}
        @yield('content')
    </main>

    {{-- アプリ共通で動くJavaScriptファイルを読み込み --}}
    <script src="{{ asset('js/common.js') }}"></script>
    {{-- CSSの@stackと同じく、各子画面専用のJavaScriptコードをここに合流させる着地ポイント --}}
    @yield('scripts')

</body>
</html>
