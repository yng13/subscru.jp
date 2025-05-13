<?php

namespace App\Http\Controllers;

use App\Models\Service;

// Service モデルが存在する場合
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // データベースからサービスを取得（認証済みのユーザーに紐づくサービスのみを取得することが重要）
        // 例: Auth::user()->services()->get(); のようにリレーションを使用
        // 現状はモックデータを使用しているため、仮に全件取得の例を記載
        // TODO: 認証ユーザーに紐づくサービスのみを取得するように修正
        $services = Service::all(); // 例：Service モデルが存在し、Eloquent を使用する場合

        // 必要に応じて、ソートやページネーションのクエリパラメータを処理
        // 例: $sortBy = $request->query('sortBy', 'notificationDate');
        // 例: $sortDirection = $request->query('sortDirection', 'asc');
        // $services = $services->sortBy($sortBy, SORT_REGULAR, $sortDirection === 'desc');

        // 取得したサービスデータをJSON形式で返す
        return response()->json([
            'services' => $services,
            'message' => 'サービス一覧を正常に取得しました。', // 必要に応じてメッセージも返す
        ], 200); // ステータスコード200 (OK)
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
            $service = Service::create([
                'user_id' => 1, // ★仮のユーザーID★ 認証機能実装後に変更
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
                'notification_date' => $validatedData['notification_date'],
                'notification_timing' => $validatedData['notificationTiming'] ?? 0, // nullの場合はデフォルト値0
                'memo' => $validatedData['memo'] ?? null, // nullの場合はnull
                // 'category_icon' はリクエストに含まれていない場合、または別途設定する場合
                // 'category_icon' => $request->input('category_icon', 'fas fa-question-circle'), // デフォルトアイコン例
                'category_icon' => 'fas fa-question-circle', // 現状は固定アイコンをセット
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
        // TODO: サービス詳細の取得処理
        // 現状は仮のレスポンスを返すか、または空のままにしておきます
        return response()->json($service); // 例: サービスオブジェクトを返す
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // TODO: サービスの更新処理
        // 現状は仮の成功レスポンスを返すか、または空のままにしておきます
        return response()->json([
            'message' => 'サービスを正常に更新しました（実装予定）。',
            'service' => $service // 更新後のサービスデータを返すことも
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // TODO: サービスの削除処理
        // 現状は仮の成功レスポンスを返すか、または空のままにしておきます
        return response()->json([
            'message' => 'サービスを正常に削除しました（実装予定）。',
        ]);
    }
}
