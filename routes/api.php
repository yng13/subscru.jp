<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// 認証関連のAPI (必要に応じて)
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
