<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id(); // ID (Primary Key, Auto-increment)

            // ユーザー認証を想定し、user_id を追加
            // users テーブルへの外部キー制約を設定
            // 'users' テーブルが存在しない場合は、先に users テーブルを作成するマイグレーションを実行する必要があります
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('name'); // サービス名
            $table->string('type'); // 種別 ('contract' または 'trial')
            $table->date('notification_date'); // 通知対象日
            $table->integer('notification_timing')->default(0); // 通知タイミング (日数), デフォルト0日（当日）
            $table->text('memo')->nullable(); // メモ (任意項目なのでnullable)
            $table->string('category_icon')->nullable(); // カテゴリアイコン (任意項目なのでnullable)

            $table->timestamps(); // created_at と updated_at カラム
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
