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
        Schema::table('users', function (Blueprint $table) {
            // iCalフィード用のユニークなトークンカラムを追加
            // null許容にし、デフォルトはnullとします。
            // unique() でトークンが一意であることを保証します。
            $table->string('ical_token', 64)->nullable()->unique()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ロールバック時にカラムを削除
            $table->dropColumn('ical_token');
        });
    }
};
