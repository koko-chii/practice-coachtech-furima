{{-- アプリ共通の土台（ヘッダーなど）を丸ごと引き継ぐ記述 --}}
@extends('layouts.app')

{{-- この画面専用のCSS（verify.css）を、共通の土台のCSS置き場へ送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush

{{-- 共通の土台のメインエリアに、以下の認証案内画面をカチッとはめ込む指示 --}}
@section('content')
    <div class="verify-container">
        {{-- ユーザーに次の行動を促すための案内メッセージエリア --}}
        <div class="verify-message">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください。</p>
        </div>

    {{-- メールを確認するためのメインボタン。クリックするとメール認証状態をチェックする画面（または仮画面）へ遷移する --}}
    <div>
        <a href="{{ route('verification.show') }}" class="btn-verify">
            認証はこちらから
        </a>
    </div>

    {{-- 【救済策】「メールが届かない」というユーザーのために、認証メールを再送信するためのフォーム --}}
    <form method="POST" action="{{ route('verification.send') }}">
        {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための暗号化トークン自動発行必須マーク --}}
        @csrf
        <button type="submit" class="btn-resend">認証メールを再送する</button>
    </form>

    {{-- 【状態の検知】もしメールの再送処理（Laravel標準機能）が成功し、セッションに特定のステータスが戻ってきた場合 --}}
    @if (session('status') == 'verification-link-sent')
        {{-- ユーザーに安心してもらうために、「新しく送信しました」という緑色の成功メッセージを画面に飛び出させる --}}
        <p class="success-message">新しい認証メールを送信しました。</p>
    @endif
</main>
@endsection
