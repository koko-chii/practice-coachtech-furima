{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（register.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の登録フォームをカチッとはめ込む指示 --}}
@section('content')
<div class="auth-container">
    <h1 class="auth-title">会員登録</h1>
    
    {{-- 新規登録処理を行うURL（route）へ、入力情報を安全な通信（POST）で送信するフォーム --}}
    {{-- 「novalidate」でブラウザ標準の簡易チェックをオフにし、Laravel側の正確なメッセージを表示させる --}}
    <form action="{{ route('register') }}" method="POST" class="auth-form" novalidate>
        
        {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための、Laravel必須の暗号化トークン自動発行お守り --}}
        @csrf

        {{-- ユーザー名の入力ブロック --}}
        <div class="form-group">
            <label for="name" class="form-label">ユーザー名</label>
            {{-- 「value="{{ old('name') }}"」により、エラーで画面が戻っても入力した名前が消えずに残る設計 --}}
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- メールアドレスの入力ブロック --}}
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            {{-- ユーザー名と同様に、失敗時に入力内容を復元する old() 関数を設置 --}}
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワードの入力ブロック --}}
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" class="form-input">
            
            {{-- パスワード欄のエラーチェック --}}
            @error('password')
                {{-- 【こだわり】エラー内容が「不一致」以外（未入力や文字数不足など）の場合だけ、この欄の真下にエラーを出す --}}
                @if($message !== 'パスワードと一致しません')
                    <p class="error-message">{{ $message }}</p>
                @endif
            @enderror
        </div>

        {{-- 確認用パスワードの入力ブロック --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
            
            {{-- 【こだわり】Laravelの仕様上、不一致エラー（confirmed）は本来「password」側に紐づいて届く --}}
            @error('password')
                {{-- あえてそれを確認用パスワードのすぐ真下でキャッチし、「一致しません」というエラーだけをここにピンポイントで出す --}}
                @if($message === 'パスワードと一致しません')
                    <p class="error-message">{{ $message }}</p>
                @endif
            @enderror
        </div>

        {{-- 登録実行ボタンのエリア --}}
        <div class="form-button">
            <button type="submit" class="btn-submit">登録する</button>
        </div>
    </form>

    {{-- すでにアカウントを持っているユーザーを、ログイン画面（login）へ案内するリンク --}}
    <div class="auth-footer">
        <a href="{{ route('login') }}" class="link-login">ログインはこちら</a>
    </div>
</div>
@endsection
