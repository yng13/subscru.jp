<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ServiceController が存在する場合
use App\Http\Controllers\IcalFeedController;
use App\Http\Controllers\ServiceController;

Route::middleware('web')->group(function () {

    Route::get('/', function () {
        return view('index');
    })->middleware('auth');

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
            Route::get('/user', function (Request $request) {
                $user = $request->user();
                // 認証済みユーザーの場合、iCalフィードURLを追加して返す
                if ($user) {
                    // ルート名 'ical.feed' とユーザーのical_tokenを使用してURLを生成
                    $icalFeedUrl = route('ical.feed', ['token' => $user->ical_token], true); // 絶対URLを生成 (true)
                    // webcal スキームに置換
                    $icalFeedUrl = str_replace('https://', 'webcal://', str_replace('http://', 'webcal://', $icalFeedUrl));


                    return response()->json([
                        'user' => $user,
                        'ical_feed_url' => $icalFeedUrl,
                    ]);
                }
                // 認証されていない場合はnullまたはエラーを返す（auth:sanctumミドルウェアで基本ガードされますが念のため）
                return response()->json(null, 401);
            });
        });
    });

    // --- iCalフィード用のルートを追加 ---
    // ユニークなトークンを含むURLで、認証なしでアクセスできるようにします。
    // トークン自体がユーザーを識別し、不正なアクセスを防ぎます。
    Route::get('/feed/ical/{token}.ics', [IcalFeedController::class, 'show'])
        ->name('ical.feed');
    // ---------------------------------
});
