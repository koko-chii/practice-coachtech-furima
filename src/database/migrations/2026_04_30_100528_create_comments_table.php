<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //コメントが書かれたときデーターベースに保存するため、
    // ユーザー情報・対象商品情報、コメント255字内、コメント日時の項目を
    // 新しく保存した設計図
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('comment', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    //やり直しが必要な場合、コメントテーブル設計図を削除し元に戻す記述
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
