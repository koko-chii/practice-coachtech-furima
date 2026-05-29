<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //いいねの情報を保存するためのテーブルをデーターベースに新しく作成する設計図
    public function up(): void
    {
        //いいねしたユーザー情報と対象商品情報、いいねした日時を保存する項目の新規作成
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */

    //設計図の取消をする際、いいねテーブルを削除し元通りにするための記述
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
