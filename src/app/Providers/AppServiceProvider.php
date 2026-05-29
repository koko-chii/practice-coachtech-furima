<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider; // すべての準備室（サービスプロバイダー）の土台となる設計図

// アプリ全体の共通の準備・設定を行うための「起動準備室」クラス
class AppServiceProvider extends ServiceProvider
{
    /**
     * アプリが本格的に動き出す「前」に、使いたい道具（機能）をシステムに登録する場所（レジスター）
     * ※今回は何も登録するものがないので、中身は空っぽ（//）になっています。
     */
    public function register(): void
    {
        //
    }

    /**
     * アプリのすべての準備が整った「直後」に、自動的に実行される初期設定の場所（ブート）
     * 画面を表示したり、ルーティングが動いたりする前に、この中の中身が必ず最初に実行されます。
     */
    public function boot(): void
    {
        // アプリ専用の翻訳ファイル（言葉の切り替えデータ）を、指定したフォルダから自動で読み込む設定
        // 「src/lang」フォルダの中にある翻訳データを「default」という名前のグループとしてシステムにセットしています。
        $this->loadTranslationsFrom(base_path('src/lang'), 'default');
    }
}
