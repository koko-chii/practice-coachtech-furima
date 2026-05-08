<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtechフリマ</title>

    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-message.css') }}">

    @stack('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo-link">
                <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" class="header__logo">
            </a>

            <div class="header__search">
                <form action="/" method="GET" class="header__search-form">
                    <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">

                    <div class="header__search-container">
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="なにをお探しですか？" class="header__search-input">

                        @if(request('keyword'))
                            <a href="{{ url('/') }}?tab={{ request('tab', 'recommend') }}" class="header__search-clear">×</a>
                        @endif
                    </div>
                </form>
            </div>

            <nav class="header__nav">
                <ul class="header__nav-list">
                    @if(Auth::check())
                        <li class="header__nav-item">
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

    @include('components.flash-message')

    <main class="main">
        @yield('content')
    </main>

    <script src="{{ asset('js/common.js') }}"></script>
    @yield('scripts')

</body>
</html>
