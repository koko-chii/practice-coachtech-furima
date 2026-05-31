@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endpush

@section('content')
<div class="detail-container">
    {{-- 左側：画像 --}}
    <div class="detail-image">
        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
    </div>

    {{-- 右側：詳細情報 --}}
    <div class="detail-info">
        <h1 class="detail-name">{{ $item->name }}</h1>
        <p class="detail-brand">{{ $item->brand ?? 'ブランド名なし' }}</p>
        <p class="detail-price">
            ¥{{ number_format($item->price) }} <span>(税込)</span>
        </p>

        {{-- アクション（いいね・コメントアイコン） --}}
        <div class="detail-actions">
            <div class="action-item">
                <button type="button" class="like-button" id="like-btn" data-item-id="{{ $item->id }}">
                    @if(Auth::check() && Auth::user()->likedItems->contains($item->id))
                        <img src="{{ asset('img/liked.png') }}" alt="いいね済み" class="heart-icon" id="heart-img">
                    @else
                        <img src="{{ asset('img/HeartLogo.png') }}" alt="いいね" class="heart-icon" id="heart-img">
                    @endif
                </button>
                <span class="like-count" id="like-count">{{ $item->likedByUsers()->count() }}</span>
            </div>

            <div class="action-item">
                <img src="{{ asset('img/CommentLogo.png') }}" alt="コメント" class="comment-icon">
                <span class="count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        {{-- 購入ボタン --}}
        @if($item->is_sold)
            <button class="btn-purchase is-sold" disabled style="background-color: #888; cursor: not-allowed;">売り切れました</button>
        @else
            <a href="/purchase/{{ $item->id }}" class="btn-purchase">購入手続きへ</a>
        @endif

        <div class="detail-description">
            <h2 class="section-title">商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

        <div class="detail-info-section">
            <h2 class="section-title">商品情報</h2>
            <table class="info-table">
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        @if($item->categories && $item->categories->count() > 0)
                            @foreach($item->categories as $category)
                                <span class="category-tag">{{ $category->name }}</span>
                            @endforeach
                        @else
                            <span>カテゴリーなし</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>商品の状態</th>
                    <td>{{ $item->condition }}</td>
                </tr>
            </table>
        </div>

                {{-- コメント一覧表示ブロック --}}
        <div class="detail-comments">
            {{-- 【リレーション】この商品（item）に紐づいているコメントデータを数えて（count）件数を動的に表示 --}}
            <h2 class="section-title">コメント({{ $item->comments->count() }})</h2>
            
            {{-- この商品に投稿されたすべてのコメント（$item->comments）を、上から順に1件ずつ取り出してループ表示 --}}
            @foreach($item->comments as $comment)
                <div class="comment-item">
                    {{-- コメントしたユーザーのプロフィール（画像・名前）エリア --}}
                    <div class="comment-user">
                        <div class="user-icon">
                            {{-- 【2段階リレーション】コメント（comment）から、それを書いたユーザー（user）の画像パス（img_url）を芋づる式に取得 --}}
                            {{-- もしユーザーがプロフィール画像を登録していた（img_urlが空ではない）場合だけ表示する --}}
                            @if($comment->user->img_url)
                                <img src="{{ asset('storage/' . $comment->user->img_url) }}" alt="ユーザー画像" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @endif
                        </div>
                        {{-- 【2段階リレーション】同じくコメントに紐づくユーザーの「名前（name）」を動的に表示 --}}
                        <span class="user-name">{{ $comment->user->name }}</span>
                    </div>
                    {{-- 実際に書き込まれたコメントの本文テキストを表示 --}}
                    <div class="comment-text">
                        {{ $comment->comment }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- コメント入力欄セクション --}}
        <div class="comment-form-section">
            <h2 class="section-title">商品へのコメント</h2>
            
            {{-- 【状態の検知】もしこの商品の売切フラグ（is_sold）が真（true / 1）だった場合 --}}
            @if($item->is_sold)
                {{-- トラブルや混乱を防ぐために、コメントの入力欄自体を完全に隠し、警告文だけを画面に表示する --}}
                <p style="color: #ff5a5f; font-weight: bold;">※この商品は売り切れているため、コメントできません。</p>
            {{-- 商品がまだ売れていない（販売中）の場合だけ、以下の入力フォームを表示する --}}
            @else
                {{-- コメント保存処理を行うURL（route）へ、どの商品に対するコメントかの情報（item_id）を添えてPOST通信で送信するフォーム --}}
                <form action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST">
                    {{-- 【セキュリティ】なりすまし攻撃（CSRF）を防ぐための、Laravel必須の暗号化トークン自動発行必須マーク --}}
                    @csrf
                    {{-- コメントを入力する大きなテキストエリア。ブラウザ側での未入力送信を防ぐため「required」を設定 --}}
                    <textarea name="comment" class="comment-textarea" required></textarea>
                    
                    {{-- もし入力チェック（バリデーション）で文字数制限などのミスがあった場合だけ、エラーメッセージを真下に表示 --}}
                    @error('comment')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    {{-- コメント送信確定ボタン --}}
                    <button type="submit" class="btn-comment-submit">コメントを送信する</button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- いいねボタン（ハート）を押した時に、画面をリロードせずに裏側でデータを切り替えるためのJavaScriptファイルを読み込み --}}
<script src="{{ asset('js/like.js') }}"></script>
@endsection
