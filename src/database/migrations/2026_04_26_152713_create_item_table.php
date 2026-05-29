<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//laravelの新しい仕様で重複エラー防止のためクラス名は定義せず直接returnで記述してある
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //商品情報を保存するための項目をデーターベース内に作成する設計図
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('price');
            $table->string('brand')->nullable();
            $table->text('description');
            $table->string('img_url');
            $table->string('condition');
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    //itemテーブルが存在したら設計図を取消しデーターベースを元に戻すための記述
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
