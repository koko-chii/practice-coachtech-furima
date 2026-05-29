<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //会員登録時の情報から4項目を後から追加するための設定
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('postcode')->nullable()->after('email'); // 郵便番号
            $table->string('address')->nullable()->after('postcode'); // 住所
            $table->string('building')->nullable()->after('address'); // 建物名
            $table->string('img_url')->nullable()->after('building');
        });
    }

    /**
     * Reverse the migrations.
     */

    //追加の4項目を取り消す必要がある場合の設定
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['postcode', 'address', 'building', 'img_url']);
        });
    }
};
