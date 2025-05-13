<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;

// ServiceController が存在する場合

// サービス一覧の取得
Route::get('/services', [ServiceController::class, 'index']);
// サービス追加
Route::post('/services', [ServiceController::class, 'store']);

// 認証済みのユーザーのみアクセス可能なAPIルートのグループ
Route::middleware('auth:sanctum')->group(function () {

    // TODO: 今後追加するAPI
    // Route::get('/services/{service}', [ServiceController::class, 'show']); // サービス詳細取得
    // Route::put('/services/{service}', [ServiceController::class, 'update']); // サービス更新
    // Route::delete('/services/{service}', [ServiceController::class, 'destroy']); // サービス削除

    // 例: ログアウトAPI (SPAの場合)
    // Route::post('/logout', [AuthController::class, 'logout']);
});

// 認証関連のAPI (必要に応じて)
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
