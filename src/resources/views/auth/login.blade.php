{{-- アプリ共通の土台（ヘッダーや全体の枠組みが書かれた共通デザイン）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（login.css）を、共通の土台の「CSS置き場」へピンポイントで送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

{{-- 共通の土台にある「メイン中身の置き場（content）」に、以下のログインフォームをカチッとはめ込む指示 --}}
@section('content')
<div class="auth-container">
    <h1 class="auth-title">ログイン</h1>
    
    {{-- ログイン処理を行うURL（route）へ、入力された情報を安全な通信（POST）で送信するフォーム --}}
    {{-- 「novalidate」をつけて、ブラウザ標準のチェックを切り、Laravel側の高度なエラーメッセージを表示させる工夫 --}}
    <form action="{{ route('login') }}" method="POST" class="auth-form" novalidate>
        
        {{-- 【超重要】なりすまし攻撃（CSRF）を防ぐためのセキュリティトークンを自動発行する、Laravel必須のお守り --}}
        @csrf

        {{-- メールアドレスの入力ブロック --}}
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            {{-- 「value="{{ old('email') }}"」のおかげで、パスワードを間違えて画面が戻っても、入力したメールアドレスが消えずに残る親切設計 --}}
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}">
            
            {{-- メールアドレスの入力にミス（未入力や形式エラー）があった場合だけ、エラーメッセージを画面に出す指示 --}}
            @error('email')
                    <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワードの入力ブロック --}}
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            {{-- セキュリティのため、入力した文字が「●●●」と隠れるように type="password" に指定 --}}
            <input type="password" name="password" id="password" class="form-input">
            
            {{-- パスワードの入力にミス（未入力など）があった場合だけ、エラーメッセージを画面に出す指示 --}}
            @error('password')
                    <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- ログイン実行ボタンのエリア --}}
        <div class="form-button">
            <button type="submit" class="btn-submit">ログインする</button>
        </div>
    </form>

    {{-- まだアカウントを持っていないユーザーを、新規会員登録画面（register）へ案内するリンク --}}
    <div class="auth-footer">
        <a href="{{ route('register') }}" class="link-register">会員登録はこちら</a>
    </div>
</div>
@endsection

