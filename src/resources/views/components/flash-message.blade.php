{{-- アプリ全体の「CSS置き場」へ、この部品専用の「flash-message.css」を自動で送り込んで読み込ませる設定 --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/flash-message.css') }}">
@endpush

{{-- 【状態の検知】もしコントローラー側から「処理が成功したメッセージ（message）」が裏側で届いていた場合 --}}
@if (session('message'))
    {{-- 事前にCSSで定義した、目に優しい薄緑色（.flash-message）のお知らせ枠を画面の一番上に飛び出させる --}}
    <div id="flash-message" class="flash-message">
        {{-- コントローラーから送られてきた「商品を削除しました」などの具体的な文字をここに表示 --}}
        {{ session('message') }}
        {{-- 【JavaScriptの大技】「×」ボタンがクリックされた瞬間に、このお知らせ枠全体の表示（display）を「none（消去）」に切り替える仕掛け --}}
        <button onclick="this.parentElement.style.display='none'" class="flash-close">×</button>
    </div>
@endif

{{-- 【状態の検知】もしコントローラー側から「バリデーションや処理の失敗エラー（error）」が届いていた場合 --}}
@if (session('error'))
    {{-- 警告色である薄赤色（.flash-error）のお知らせ枠を画面の一番上に飛び出させる --}}
    <div id="flash-error" class="flash-error">
        {{-- 「決済に失敗しました」などのエラー文言をここに表示 --}}
        {{ session('error') }}
        {{-- 成功時と同じく、クリックされたら自分を包んでいる親要素（parentElement）を丸ごと一瞬で非表示にする --}}
        <button onclick="this.parentElement.style.display='none'" class="flash-close">×</button>
    </div>
@endif

