<?php

namespace App\Http\Controllers;

use App\Models\Service;

// Service モデルが存在する場合
use Illuminate\Http\Request;

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
        // TODO: サービスの新規登録処理
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // TODO: サービス詳細の取得処理
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // TODO: サービスの更新処理
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // TODO: サービスの削除処理
    }
}
