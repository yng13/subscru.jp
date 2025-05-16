<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\IcalFeedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ★ ファイルのルート定義全体を api ミドルウェアグループで囲む ★
Route::middleware('api')->group(function () {

    // 認証関連のAPI (必要に応じて)
    // Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/register', [AuthController::class, 'register']);

    // 認証済みのユーザーのみアクセス可能なAPIルートのグループ
    // このグループは api ミドルウェアグループの中にあります
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
        // routes/web.php から移動した iCal URL を返す /user ルート
        Route::get('/user', function (Request $request) {
            $user = $request->user();
            // 認証済みユーザーの場合、iCalフィードURLを追加して返す
            if ($user) {
                // ルート名 'ical.feed' は routes/web.php に定義されているため、ここではそのまま参照します。
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

    // routes/web.php に残るiCalフィード用のルートは、ここでは定義しません。
    // Route::get('/feed/ical/{token}.ics', [IcalFeedController::class, 'show'])->name('ical.feed');

}); // ★ api ミドルウェアグループの閉じタグ ★
