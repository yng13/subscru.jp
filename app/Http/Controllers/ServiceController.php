<?php

namespace App\Http\Controllers;

use App\Models\Service;

// Service モデルが存在する場合
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * サービス一覧を取得するAPI
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 認証済みのユーザーに紐づくサービスのみを取得
        // Auth::user() で認証済みのユーザーモデルを取得し、その services リレーションを使用
        $services = Auth::user()->services()->get();

        // 必要に応じて、ソートやページネーションのクエリパラメータを処理 (現状はフロントエンドでソート)
        // 例: $sortBy = $request->query('sortBy', 'notificationDate');
        // 例: $sortDirection = $request->query('sortDirection', 'asc');
        // $services = $services->sortBy($sortBy, SORT_REGULAR, $sortDirection === 'desc');

        // 取得したサービスデータをJSON形式で返す
        return response()->json([
            'services' => $services,
            'message' => 'サービス一覧を正常に取得しました。',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // リクエストデータのバリデーション
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:contract,trial', // 'contract' または 'trial' のいずれか
            'notification_date' => 'required|date', // 有効な日付形式であること
            'notificationTiming' => 'nullable|integer', // 任意項目で整数
            'memo' => 'nullable|string|max:1000', // 任意項目で文字列、最大1000文字
            // 'category_icon' はUI側で決定されるため、ここでは必須としないか、
            // 必要に応じてバリデーションルールを追加してください
            // 'category_icon' => 'nullable|string|max:50',
        ]);

        // サービスの作成とデータベースへの保存
        try {
            // TODO: 認証ユーザーのIDを紐づける (認証機能実装後に Auth::id() などを使用)
            // 現状は仮で user_id = 1 を設定します
            // Auth::id() で認証済みのユーザーIDを取得
            $service = Service::create([
                'user_id' => Auth::id(), // 認証ユーザーのIDを使用
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
                'notification_date' => $validatedData['notification_date'],
                'notification_timing' => $validatedData['notificationTiming'] ?? 0,
                'memo' => $validatedData['memo'] ?? null,
                'category_icon' => $request->input('category_icon', 'fas fa-question-circle'), // UIから送られてくるか、デフォルト値を設定
            ]);

            // 成功レスポンスを返す
            return response()->json([
                'service' => $service, // 作成されたサービスデータを返すことも可能
                'message' => 'サービスを正常に登録しました。',
            ], 201); // ステータスコード201 (Created)

        } catch (\Exception $e) {
            // エラーレスポンスを返す
            \Log::error('サービスの登録中にエラーが発生しました: ' . $e->getMessage()); // ログ出力
            return response()->json([
                'message' => 'サービスの登録に失敗しました。',
                'error' => $e->getMessage(), // デバッグ用にエラーメッセージを含めることも
            ], 500); // ステータスコード500 (Internal Server Error)
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // TODO: サービス詳細の取得処理 (必要であれば)
        // 現状は Implicit Model Binding で取得したサービスオブジェクトを返す
        // 認証実装後に、このサービスが認証ユーザーのものであるか確認が必要
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Service $service // Implicit Model Binding で渡される
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Service $service)
    {
        // 認証ユーザーがこのサービスを所有しているか確認
        // Implicit Model Binding で取得したサービスが認証ユーザーのものであることを確認
        if ($request->user()->id !== $service->user_id) {
            // ログ出力して不正アクセスを記録
            \Log::warning('Unauthorized attempt to update service.', [
                'user_id' => $request->user()->id,
                'service_id' => $service->id,
                'service_owner_id' => $service->user_id,
            ]);
            return response()->json(['message' => 'このサービスへのアクセス権限がありません。'], 403); // 403 Forbidden レスポンス
        }

        // リクエストデータのバリデーション
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['contract', 'trial'])], // 'contract' または 'trial' のいずれか
            'notification_date' => 'required|date', // 有効な日付形式であること
            'notificationTiming' => 'nullable|integer', // 任意項目で整数
            'memo' => 'nullable|string|max:1000', // 任意項目で文字列、最大1000文字
            'category_icon' => 'nullable|string|max:50', // category_icon も更新対象に含める場合
        ]);

        // サービスの更新
        try {
            // $service->update() メソッドで更新
            $service->update($validatedData);

            // 更新後のサービスデータを返す
            return response()->json([
                'service' => $service, // 更新後のサービスデータを返す
                'message' => 'サービスを正常に更新しました。',
            ], 200); // ステータスコード200 (OK)

        } catch (\Exception $e) {
            // エラーレスポンスを返す
            \Log::error('サービスの更新中にエラーが発生しました: ' . $e->getMessage());
            return response()->json([
                'message' => 'サービスの更新に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Service $service // Implicit Model Binding で渡される
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Service $service)
    {
        // 認証ユーザーがこのサービスを所有しているか確認
        // Implicit Model Binding で取得したサービスが認証ユーザーのものであることを確認
        if (Auth::id() !== $service->user_id) {
            // ログ出力して不正アクセスを記録
            \Log::warning('Unauthorized attempt to delete service.', [
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'service_owner_id' => $service->user_id,
            ]);
            return response()->json(['message' => 'このサービスへのアクセス権限がありません。'], 403); // 403 Forbidden レスポンス
        }

        // サービスの削除
        try {
            $service->delete();

            // 成功レスポンスを返す (削除の場合、通常はコンテンツなしの 204 No Content を返すか、
            // 成功メッセージと共に 200 OK を返します。ここでは 200 OK とメッセージの例を示します。)
            return response()->json([
                'message' => 'サービスを正常に削除しました。',
            ], 200); // または 204 (No Content)

        } catch (\Exception $e) {
            // エラーレスポンスを返す
            \Log::error('サービスの削除中にエラーが発生しました: ' . $e->getMessage());
            return response()->json([
                'message' => 'サービスの削除に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
