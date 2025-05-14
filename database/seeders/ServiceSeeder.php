<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードを使用する場合
use App\Models\Service; // Serviceモデルを使用する場合
use App\Models\User; // Userモデルを使用する場合（user_id 登録のため）
use Illuminate\Support\Facades\Hash; // Userのパスワードハッシュ化のため (必要であれば)
use Illuminate\Support\Str; // Str ファサードを追加

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テストユーザーを作成または取得 (サービスの user_id に紐づけるため)
        // 実際のアプリケーションでは、ユーザーが存在することを前提とするか、
        // 別のシーダーでユーザーを作成します。ここでは簡単な例として一つ作成します。
        $user = User::firstOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'), // パスワードはハッシュ化
                'ical_token' => Str::random(60), // ★ここを追加/修正★
                // その他のユーザー関連カラムがあれば追加
            ]
        );

        // 既存のサービスデータをクリア (オプション)
        // DB::table('services')->truncate(); // 全件削除してから投入したい場合

        // モックデータに基づいてサービスを登録
        // ... (以下サービス登録部分は変更なし) ...
        Service::create([
            'user_id' => $user->id, // 作成したユーザーのIDを紐づけ
            'name' => 'Netflix',
            'type' => 'contract',
            'notification_date' => '2025-05-12', // 例: 今日の日付など適切な日付に変更
            'notification_timing' => 7,
            'memo' => '年払い契約、次回更新時に解約を検討...',
            'category_icon' => 'fas fa-music',
        ]);

        Service::create([
            'user_id' => $user->id, // 作成したユーザーのIDを紐づけ
            'name' => 'Google Drive',
            'type' => 'trial',
            'notification_date' => '2026-01-15',
            'notification_timing' => 3,
            'memo' => 'トライアル終了前に容量を確認...',
            'category_icon' => 'fas fa-cloud',
        ]);

        Service::create([
            'user_id' => $user->id, // 作成したユーザーのIDを紐づけ
            'name' => 'Spotify',
            'type' => 'contract',
            'notification_date' => '2025-11-20',
            'notification_timing' => 0,
            'memo' => 'ファミリープランを契約中...',
            'category_icon' => 'fas fa-music',
        ]);

        Service::create([
            'user_id' => $user->id, // 作成したユーザーのIDを紐づけ
            'name' => 'AWS S3',
            'type' => 'contract',
            'notification_date' => '2026-03-01',
            'notification_timing' => 30,
            'memo' => 'バックアップ用ストレージ...',
            'category_icon' => 'fas fa-database',
        ]);

        Service::create([
            'user_id' => $user->id, // 作成したユーザーのIDを紐づけ
            'name' => 'Adobe Creative Cloud',
            'type' => 'contract',
            'notification_date' => '2025-10-10',
            'notification_timing' => 1,
            'memo' => '年間プラン、期限が近い...',
            'category_icon' => 'fas fa-paint-brush',
        ]);
    }
}
