<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service; // Serviceモデルを使用
use App\Models\User; // Userモデルを使用（user_id 登録のため）
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ServiceFactory; // Service Factory を使用するために追加

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テストユーザーを作成または取得 (サービスの user_id に紐づけるため)
        $user = User::firstOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'), // パスワードはハッシュ化
                'ical_token' => Str::random(60),
            ]
        );

        // 既存のサービスデータをクリア (オプション - 必要であればコメント解除)
        // DB::table('services')->truncate();

        // === ここから修正 ===
        // Service Factory を使用して30件のサービスデータを作成し、テストユーザーに紐づける
        Service::factory()->count(30)->create([
            'user_id' => $user->id,
        ]);
        // === ここまで修正 ===


        // モックデータに基づいてサービスを登録 (上記でFactoryを使用するので、これらの行は不要になります)
        /*
        Service::create([
            'user_id' => $user->id,
            'name' => 'Netflix',
            'type' => 'contract',
            'notification_date' => '2025-05-12',
            'notification_timing' => 7,
            'memo' => '年払い契約、次回更新時に解約を検討...',
            'category_icon' => 'fas fa-music',
        ]);

        Service::create([
            'user_id' => $user->id,
            'name' => 'Google Drive',
            'type' => 'trial',
            'notification_date' => '2026-01-15',
            'notification_timing' => 3,
            'memo' => 'トライアル終了前に容量を確認...',
            'category_icon' => 'fas fa-cloud',
        ]);

        Service::create([
            'user_id' => $user->id,
            'name' => 'Spotify',
            'type' => 'contract',
            'notification_date' => '2025-11-20',
            'notification_timing' => 0,
            'memo' => 'ファミリープランを契約中...',
            'category_icon' => 'fas fa-music',
        ]);

        Service::create([
            'user_id' => $user->id,
            'name' => 'AWS S3',
            'type' => 'contract',
            'notification_date' => '2026-03-01',
            'notification_timing' => 30,
            'memo' => 'バックアップ用ストレージ...',
            'category_icon' => 'fas fa-database',
        ]);

        Service::create([
            'user_id' => $user->id,
            'name' => 'Adobe Creative Cloud',
            'type' => 'contract',
            'notification_date' => '2025-10-10',
            'notification_timing' => 1,
            'memo' => '年間プラン、期限が近い...',
            'category_icon' => 'fas fa-paint-brush',
        ]);
        */
    }
}
