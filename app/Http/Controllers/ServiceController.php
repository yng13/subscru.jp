<?php

namespace App\Http\Controllers;

use App\Models\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * サービス一覧を取得するAPI (ページネーション & ソート & 検索対応)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 認証済みのユーザーに紐づくサービスのみを取得
        $query = Auth::user()->services();

        // === 検索キーワードのクエリパラメータを処理 (パラメータ名を短縮) ===
        // パラメータ名を 'q' (query) に変更
        $searchTerm = $request->query('q');

        // 検索キーワードが存在する場合、サービス名で絞り込み (部分一致)
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        // =========================================================

        // === ソートのクエリパラメータを処理 (パラメータ名を短縮) ===
        // パラメータ名を 'sb' (sortBy) と 'sd' (sortDirection) に変更
        $sortBy = $request->query('sb', 'notification_date'); // デフォルトは notification_date
        $sortDirection = $request->query('sd', 'asc'); // デフォルトは昇順

        // 許可するソート可能なカラムを定義 (カラム名自体は変更しない)
        $allowedSortColumns = ['name', 'type', 'notification_date', 'notification_timing'];

        // ソートキーが許可されたカラムに含まれているかチェック
        if (!in_array($sortBy, $allowedSortColumns)) {
            // 許可されていない場合はデフォルトのソートキーに戻す
            $sortBy = 'notification_date';
        }

        // ソート方向が 'asc' または 'desc' であるかチェック
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            // 不正な場合はデフォルトの昇順に戻す
            $sortDirection = 'asc';
        }

        // クエリにソートを適用 (使用するカラム名と方向は変更なし)
        $query->orderBy($sortBy, $sortDirection);
        // ====================================================


        // ページネーションを適用
        // $perPage = $request->query('per_page', 10); // デフォルト10件に変更
        $perPage = $request->query('pp', 10); // パラメータ名を 'pp' に変更し、デフォルト10件 <--- ここを修正
        $services = $query->paginate($perPage);


        // 取得したサービスデータとページネーション情報をJSON形式で返す
        return response()->json($services, 200);
    }

    // ... 他のメソッド (store, show, update, destroy) は変更なし ...

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:contract,trial',
            'notification_date' => 'required|date',
            'notification_timing' => 'nullable|integer',
            'memo' => 'nullable|string|max:1000',
            'category_icon' => 'nullable|string|max:50',
        ]);

        try {
            $service = Service::create([
                'user_id' => Auth::id(),
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
                'notification_date' => $validatedData['notification_date'],
                'notification_timing' => $validatedData['notification_timing'] ?? 0, // 通知タイミングのデフォルト値
                'memo' => $validatedData['memo'] ?? null,
                'category_icon' => $validatedData['category_icon'] ?? 'fas fa-question-circle', // デフォルトアイコン
            ]);

            return response()->json([
                'service' => $service,
                'message' => 'サービスを正常に登録しました。',
            ], 201);

        } catch (\Exception $e) {
            \Log::error('サービスの登録中にエラーが発生しました: ' . $e->getMessage());
            return response()->json([
                'message' => 'サービスの登録に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        if (Auth::id() !== $service->user_id) {
            \Log::warning('Unauthorized attempt to show service.', [
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'service_owner_id' => $service->user_id,
            ]);
            return response()->json(['message' => 'このサービスへのアクセス権限がありません。'], 403);
        }

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
        if ($request->user()->id !== $service->user_id) {
            \Log::warning('Unauthorized attempt to update service.', [
                'user_id' => $request->user()->id,
                'service_id' => $service->id,
                'service_owner_id' => $service->user_id,
            ]);
            return response()->json(['message' => 'このサービスへのアクセス権限がありません。'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['contract', 'trial'])],
            'notification_date' => 'required|date',
            'notification_timing' => 'nullable|integer',
            'memo' => 'nullable|string|max:1000',
            'category_icon' => 'nullable|string|max:50',
        ]);

        try {
            $service->update($validatedData);

            return response()->json([
                'service' => $service,
                'message' => 'サービスを正常に更新しました。',
            ], 200);

        } catch (\Exception $e) {
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
        if (Auth::id() !== $service->user_id) {
            \Log::warning('Unauthorized attempt to delete service.', [
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'service_owner_id' => $service->user_id,
            ]);
            return response()->json(['message' => 'このサービスへのアクセス権限がありません。'], 403);
        }

        try {
            $service->delete();

            return response()->json([
                'message' => 'サービスを正常に削除しました。',
            ], 200); // または 204 (No Content)

        } catch (\Exception $e) {
            \Log::error('サービスの削除中にエラーが発生しました: ' . $e->getMessage());
            return response()->json([
                'message' => 'サービスの削除に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
