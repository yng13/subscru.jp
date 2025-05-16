<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ServiceController が存在する場合
use App\Http\Controllers\IcalFeedController;

//use App\Http\Controllers\ServiceController;

Route::middleware('web')->group(function () {
    // ランディングページ (認証なしでアクセス可能)
    Route::get('/', function () {
        return view('welcome'); // welcome ビューを表示するように変更
    })->name('welcome');

    // プライバシーポリシー (認証なしでアクセス可能)
    Route::get('/privacy', function () {
        return view('privacy'); // privacy ビューを表示するように変更
    })->name('privacy');

    // 利用規約 (認証なしでアクセス可能)
    Route::get('/terms', function () {
        return view('terms'); // terms ビューを表示するように変更
    })->name('terms');

    // 問い合わせフォーム (認証なしでアクセス可能)
    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    Route::get('/my', function () {
        return view('index');
    })->middleware('auth')->name('my');

    // --- iCalフィード用のルートを追加 ---
    // ユニークなトークンを含むURLで、認証なしでアクセスできるようにします。
    // トークン自体がユーザーを識別し、不正なアクセスを防ぎます。
    Route::get('/feed/ical/{token}.ics', [IcalFeedController::class, 'show'])
        ->name('ical.feed');
    // ---------------------------------
});
