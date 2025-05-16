<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    // 各テストメソッドの実行前にデータベースをリフレッシュ

    protected User $user;

    /**
     * 各テストメソッドの実行前に必要なセットアップを行う
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のユーザーを作成し、プロパティに保持
        $this->user = User::factory()->create([
            'password' => bcrypt('password'), // テスト用のパスワードを設定
        ]);

        // Sanctum の stateful API テストに必要な設定
        // 認証済みユーザーとしてリクエストを送信できるようになります
        $this->actingAs($this->user);
    }

    /**
     * 認証済みユーザーがサービス一覧画面にアクセスできるかのテスト
     */
    public function test_authenticated_user_can_view_service_list_page(): void
    {
        $response = $this->get('/my'); // サービス一覧画面のルート (routes/web.php で 'my' に設定されている前提)

        $response->assertStatus(200); // 成功レスポンス (ステータスコード 200) が返されることを確認
    }

    /**
     * 未認証ユーザーがサービス一覧画面にアクセスできないかのテスト
     */
    public function test_unauthenticated_user_cannot_view_service_list_page(): void
    {
        // 明示的に認証を解除する
        //$this->actingAs(null);
        Auth::logout();

        $response = $this->get('/my');

        // 未認証ユーザーはログインページにリダイレクトされることを確認
        $response->assertRedirect('/login'); // Fortify のデフォルトのリダイレクト先('/login') を確認
    }

    /**
     * 認証済みユーザーがサービス一覧APIにアクセスできるかのテスト
     */
    public function test_authenticated_user_can_access_service_list_api(): void
    {
        // テストユーザーに紐づくサービスをいくつか作成
        Service::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/api/services'); // 認証済みユーザーとしてAPIにアクセス

        $response->assertStatus(200) // 成功レスポンスを確認
        ->assertJsonStructure([ // JSON レスポンスの構造を確認 (トップレベルのページネーション構造)
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'name',
                    'type',
                    'notification_date',
                    'notification_timing',
                    'memo',
                    'category_icon',
                    'created_at',
                    'updated_at',
                ]
            ],
            'current_page', // トップレベルに移動
            'first_page_url', // トップレベルに移動
            'from', // トップレベルに移動
            'last_page', // トップレベルに移動
            'last_page_url', // トップレベルに移動
            'links', // トップレベルにそのまま
            'next_page_url', // トップレベルに移動
            'path', // トップレベルに移動
            'per_page', // トップレベルに移動
            'prev_page_url', // トップレベルに移動
            'to', // トップレベルに移動
            'total', // トップレベルに移動
        ])
            ->assertJsonCount(5, 'data'); // 作成したサービスの数だけデータが含まれていることを確認
    }

    /**
     * 未認証ユーザーがサービス一覧APIにアクセスできないかのテスト
     */
    public function test_unauthenticated_user_cannot_access_service_list_api(): void
    {
        // 認証を解除する (Auth::logout() はそのまま)
        Auth::logout();

        // Sanctum の AuthenticateSession ミドルウェアを一時的に無効にする
        $this->withoutMiddleware(\Laravel\Sanctum\Http\Middleware\AuthenticateSession::class);

        $response = $this->get('/api/services');

        // 未認証ユーザーは認証エラー (401 Unauthorized) となることを確認
        $response->assertStatus(401);
    }

    /**
     * 認証済みユーザーがサービスを作成できるかのテスト
     */
    public function test_authenticated_user_can_create_service(): void
    {
        $serviceData = [
            'name' => '新しいサービス',
            'type' => 'contract',
            'notification_date' => '2025-12-31',
            'notification_timing' => 7,
            'memo' => 'テスト用のメモ',
            'category_icon' => 'fas fa-star',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/services', $serviceData); // 認証済みユーザーとしてPOSTリクエスト

        $response->assertStatus(201) // 作成成功 (ステータスコード 201 Created) を確認
        ->assertJson([ // レスポンスに含まれる JSON データの一部を確認
            'message' => 'サービスを正常に登録しました。',
            'service' => [
                'name' => '新しいサービス',
                'user_id' => $this->user->id,
                // 他のフィールドも必要に応じて確認
            ]
        ]);

        // データベースにサービスが登録されたことを確認
        $this->assertDatabaseHas('services', [
            'name' => '新しいサービス',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * 認証済みユーザーが自分のサービスを更新できるかのテスト
     */
    public function test_authenticated_user_can_update_their_own_service(): void
    {
        // テストユーザーに紐づくサービスを作成
        $service = Service::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'name' => '更新されたサービス名',
            'type' => 'trial',
            'notification_date' => '2026-01-01',
            'notification_timing' => 3,
            'memo' => '更新されたメモ',
            'category_icon' => 'fas fa-edit',
        ];

        $response = $this->actingAs($this->user)->putJson("/api/services/{$service->id}", $updatedData); // 認証済みユーザーとしてPUTリクエスト

        $response->assertStatus(200) // 更新成功 (ステータスコード 200 OK) を確認
        ->assertJson([
            'message' => 'サービスを正常に更新しました。',
            'service' => [
                'id' => $service->id,
                'name' => '更新されたサービス名',
                'user_id' => $this->user->id,
                // 他のフィールドも必要に応じて確認
            ]
        ]);

        // データベースのサービスが更新されたことを確認
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => '更新されたサービス名',
            'type' => 'trial',
            'notification_date' => '2026-01-01 00:00:00', // 時刻部分を追加
        ]);
    }

    /**
     * 認証済みユーザーが他のユーザーのサービスを更新できないかのテスト
     */
    public function test_authenticated_user_cannot_update_other_users_service(): void
    {
        // 別のユーザーを作成
        $otherUser = User::factory()->create();
        // 別のユーザーに紐づくサービスを作成
        $otherService = Service::factory()->create(['user_id' => $otherUser->id]);

        $updatedData = [
            'name' => '不正に更新しようとしたサービス名',
            'type' => 'contract',
            'notification_date' => '2026-01-01',
            'notification_timing' => 1,
            'memo' => '不正なメモ',
            'category_icon' => 'fas fa-bug',
        ];

        // テストユーザー (自分) として、他のユーザーのサービスに対するPUTリクエストを送信
        $response = $this->actingAs($this->user)->putJson("/api/services/{$otherService->id}", $updatedData);

        // アクセス拒否 (403 Forbidden) となることを確認
        $response->assertStatus(403);

        // データベースのサービスが更新されていないことを確認
        $this->assertDatabaseMissing('services', [
            'id' => $otherService->id,
            'name' => '不正に更新しようとしたサービス名', // 更新されていないことを確認
        ]);
    }

    /**
     * 認証済みユーザーが自分のサービスを削除できるかのテスト
     */
    public function test_authenticated_user_can_delete_their_own_service(): void
    {
        // テストユーザーに紐づくサービスを作成
        $service = Service::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/services/{$service->id}"); // 認証済みユーザーとしてDELETEリクエスト

        $response->assertStatus(200) // 削除成功 (ステータスコード 200 OK) を確認
        ->assertJson([
            'message' => 'サービスを正常に削除しました。',
        ]);

        // データベースからサービスが削除されたことを確認
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    /**
     * 認証済みユーザーが他のユーザーのサービスを削除できないかのテスト
     */
    public function test_authenticated_user_cannot_delete_other_users_service(): void
    {
        // 別のユーザーを作成
        $otherUser = User::factory()->create();
        // 別のユーザーに紐づくサービスを作成
        $otherService = Service::factory()->create(['user_id' => $otherUser->id]);

        // テストユーザー (自分) として、他のユーザーのサービスに対するDELETEリクエストを送信
        $response = $this->actingAs($this->user)->deleteJson("/api/services/{$otherService->id}");

        // アクセス拒否 (403 Forbidden) となることを確認
        $response->assertStatus(403);

        // データベースにサービスがまだ存在することを確認 (削除されていないことを確認)
        $this->assertDatabaseHas('services', ['id' => $otherService->id]);
    }

    /**
     * サービス一覧APIのページネーション機能のテスト
     */
    public function test_service_list_api_supports_pagination(): void
    {
        // テストユーザーに紐づくサービスを25件作成 (1ページあたりのデフォルトが10件の場合、複数ページになるように)
        Service::factory()->count(25)->create(['user_id' => $this->user->id]);

        // 1ページ目をリクエスト
        $response = $this->actingAs($this->user)->get('/api/services?page=1&pp=10'); // pp=10 で1ページ10件を指定

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data') // 1ページ目に10件データが含まれていることを確認
            ->assertJsonPath('current_page', 1) // 'meta.' を削除
            ->assertJsonPath('per_page', 10) // 'meta.' を削除
            ->assertJsonPath('total', 25); // 'meta.' を削除

        // 2ページ目をリクエスト
        $response = $this->actingAs($this->user)->get('/api/services?page=2&pp=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data') // 2ページ目も10件データが含まれていることを確認
            ->assertJsonPath('current_page', 2); // 'meta.' を削除

        // 3ページ目をリクエスト (残り5件)
        $response = $this->actingAs($this->user)->get('/api/services?page=3&pp=10');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data') // 3ページ目には5件データが含まれていることを確認
            ->assertJsonPath('current_page', 3) // 'meta.' を削除
            ->assertJsonPath('last_page', 3); // 'meta.' を削除

    }

    /**
     * サービス一覧APIのソート機能のテスト
     */
    public function test_service_list_api_supports_sorting(): void
    {
        // ソート順をテストするために、通知対象日が異なるサービスをいくつか作成
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Service A', 'notification_date' => '2025-12-01']);
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Service C', 'notification_date' => '2025-12-15']);
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Service B', 'notification_date' => '2025-11-20']);

        // 通知対象日で昇順ソートをテスト (デフォルト)
        $response = $this->actingAs($this->user)->get('/api/services?sb=notification_date&sd=asc');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Service B') // 最も早い日付のサービスが最初に来ることを確認
            ->assertJsonPath('data.1.name', 'Service A')
            ->assertJsonPath('data.2.name', 'Service C');

        // サービス名で降順ソートをテスト
        $response = $this->actingAs($this->user)->get('/api/services?sb=name&sd=desc');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Service C') // アルファベット順で遅いサービスが最初に来ることを確認
            ->assertJsonPath('data.1.name', 'Service B')
            ->assertJsonPath('data.2.name', 'Service A');

        // 許可されていないソートキーを指定した場合のテスト (デフォルトに戻るか確認)
        $response = $this->actingAs($this->user)->get('/api/services?sb=invalid_column&sd=desc');

        $response->assertStatus(200);
        // デフォルトのソート (notification_date asc) が適用されていることを確認
        // レスポンスデータの順序を直接確認するか、より詳細なデータ検証を行う必要がありますが、ここではステータスコードのみ確認
    }

    /**
     * サービス一覧APIの検索機能のテスト
     */
    public function test_service_list_api_supports_search(): void
    {
        // 検索対象となるサービスとそうでないサービスを作成
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Apple Music']);
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Spotify Premium']);
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'Google Cloud Storage']);

        // 'Music' で検索
        $response = $this->actingAs($this->user)->get('/api/services?q=Music');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data') // 検索結果が1件であることを確認
            ->assertJsonPath('data.0.name', 'Apple Music'); // Apple Music が検索されたことを確認

        // 'Google' で検索
        $response = $this->actingAs($this->user)->get('/api/services?q=Google');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data') // 検索結果が1件であることを確認
            ->assertJsonPath('data.0.name', 'Google Cloud Storage'); // Google Cloud Storage が検索されたことを確認

        // 存在しないキーワードで検索
        $response = $this->actingAs($this->user)->get('/api/services?q=NonExistent');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data'); // 検索結果が0件であることを確認
    }

    /**
     * 認証済みユーザーがiCalフィードURLを取得できるかのテスト
     */
    public function test_authenticated_user_can_get_ical_feed_url(): void
    {
        // UserFactory で ical_token が生成されている前提
        // setUp() で actingAs($this->user) されているので、認証済み状態

        $response = $this->actingAs($this->user)->get('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'ical_feed_url', // iCalフィードURLが含まれていることを確認
            ])
            ->assertJsonPath('user.id', $this->user->id);

        // レスポンスで返されるical_feed_urlは webcal:// に置換されているはず
        // テスト側でも同様のロジックで期待するURLを生成する
        $expectedIcalFeedUrl = route('ical.feed', ['token' => $this->user->ical_token], true);
        $expectedIcalFeedUrl = str_replace('https://', 'webcal://', str_replace('http://', 'webcal://', $expectedIcalFeedUrl));

        // assertJsonFragment の代わりに assertJsonPath を使って、ical_feed_url の値を直接検証
        $response->assertJsonPath('ical_feed_url', $expectedIcalFeedUrl);
        // または、assertJsonFragment を使い続けるならこちら
        // $response->assertJsonFragment(['ical_feed_url' => $expectedIcalFeedUrl]);
    }

    /**
     * 未認証ユーザーがiCalフィードURL取得APIにアクセスできないかのテスト
     */
    public function test_unauthenticated_user_cannot_get_ical_feed_url(): void
    {
        // 認証を解除
        //$this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);
        //$this->user = null;
        Auth::logout();

        $response = $this->get('/api/user');

        // 未認証ユーザーは認証エラー (401 Unauthorized) となることを確認
        $response->assertStatus(401);
    }

    /**
     * iCalフィードに有効なトークンでアクセスできるかのテスト
     */
    public function test_ical_feed_is_accessible_with_valid_token(): void
    {
        // ユーザーがical_tokenを持っているか確認
        $this->assertNotNull($this->user->ical_token, 'Test user must have an ical_token.');
        $this->assertNotEmpty($this->user->ical_token, 'Test user ical_token must not be empty.');

        // テストユーザーに紐づくサービスを作成
        Service::factory()->create(['user_id' => $this->user->id, 'name' => 'iCal Test Service', 'notification_date' => '2025-12-25', 'notification_timing' => 0]);

        // iCalフィードのURLを取得 (トークンは user プロパティから)
        $icalFeedUrl = route('ical.feed', ['token' => $this->user->ical_token], false);

        // iCalフィードのURLにアクセス
        $response = $this->get($icalFeedUrl);

        $response->assertStatus(200) // 成功レスポンスを確認
        ->assertHeader('Content-Type', 'text/calendar; charset=utf-8') // Content-Type を確認
        ->assertHeader('Content-Disposition', 'attachment; filename="subscru_feed.ics"') // ファイル名を確認
        ->assertSeeText('BEGIN:VCALENDAR') // iCalコンテンツの開始を確認
        ->assertSeeText('PRODID:-//Subscru//Subscru Subscription Calendar Feed//EN') // PRODIDを確認
        ->assertSeeText('BEGIN:VEVENT') // イベントの開始を確認
        ->assertSeeText('SUMMARY:iCal Test Serviceの通知対象日') // サービス名がイベントサマリーに含まれていることを確認
        ->assertSeeText('DTSTART;VALUE=DATE:20251225'); // 通知対象日が正しい形式で含まれていることを確認
    }

    /**
     * iCalフィードに無効なトークンでアクセスできないかのテスト
     */
    public function test_ical_feed_is_not_accessible_with_invalid_token(): void
    {
        // 存在しない無効なトークンでiCalフィードのURLを生成
        $invalidToken = 'invalid_ical_token_1234567890abcdef';
        $icalFeedUrl = route('ical.feed', ['token' => $invalidToken], false);

        // 無効なトークンでiCalフィードのURLにアクセス
        $response = $this->get($icalFeedUrl);

        $response->assertStatus(404) // Not Found (404) となることを確認
        ->assertSeeText('Calendar not found.'); // コントローラーで設定したエラーメッセージを確認
    }
}
