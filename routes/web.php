<?php

use Illuminate\Support\Facades\Route;

// ServiceController が存在する場合
use App\Http\Controllers\ServiceController;

Route::middleware('web')->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    Route::prefix('api')->group(function () {
        // 認証済みのユーザーのみアクセス可能なAPIルートのグループ
        // TODO: 認証機能実装後に、上記の /services ルートもこのグループ内に移動する
        Route::middleware('auth:sanctum')->group(function () {

            // サービス一覧の取得
            Route::get('/services', [ServiceController::class, 'index']);
            // サービス追加
            Route::post('/services', [ServiceController::class, 'store']);
            // サービス更新
            Route::put('/services/{service}', [ServiceController::class, 'update']);
            // サービス削除
            Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

            // TODO: 今後追加するAPI
            // Route::get('/services/{service}', [ServiceController::class, 'show']); // サービス詳細取得

            // 認証済みのユーザー情報を取得するAPIルート (Sanctumデフォルト)
            // このルートも必要であれば web ミドルウェア内で定義します
            Route::get('/user', function (Request $request) {
                return $request->user();
            });
        });
    });
});
