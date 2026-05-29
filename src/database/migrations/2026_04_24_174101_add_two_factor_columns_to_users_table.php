<?php

//データーベースの履歴や設計図を管理するMigrationファイルを呼出す
use Illuminate\Database\Migrations\Migration;
//ブループリントはデーターベーステーブルの中に、
// 文字列や数字の項目、空欄OKルールなど具体的に書き込むツール
use Illuminate\Database\Schema\Blueprint;
//データーベースに新しくテーブルを作る変更するといった実行の司令塔を呼出す
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //usersテーブルに2要素認証の機能導入を今後する場合、必要な3つの項目を追加
    //シークレットキー、リカバリーコード(緊急用予備パスワード)、2要素認証完了日時
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //開発のやり直しや新機能リリース時のバグ対応等で取り消す場合がある
        //追加した2要素認証の3つの項目をテーブルから削除しやり直せるようにする設定
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
