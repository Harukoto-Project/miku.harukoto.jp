<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;

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

Route::middleware('auth:web')->get('/user', function (Request $request) {
    return $request->user();
});

// プロフィール管理API（認証必須）
Route::middleware('auth:web')->group(function () {
    // プロフィール管理
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::post('/profile/create', [ProfileController::class, 'store']); // 後方互換性のため残す

    // リンク管理
    Route::put('/profile/links', [ProfileController::class, 'updateLinks']);
    Route::delete('/profile/links/{linkId}', [
        ProfileController::class,
        'deleteLink',
    ]);

    // アバター管理
    Route::post('/profile/avatar/upload', [
        ProfileController::class,
        'prepareAvatarUpload',
    ]);
    Route::put('/profile/avatar/apply', [
        ProfileController::class,
        'applyAvatar',
    ]);
    Route::delete('/profile/avatar/remove', [
        ProfileController::class,
        'removeAvatar',
    ]);

    // カスタムURL
    Route::patch('/profile/custom-url', [
        ProfileController::class,
        'updateCustomUrl',
    ]);
    Route::get('/profile/custom-url/check', [
        ProfileController::class,
        'checkCustomUrl',
    ]);
});

// 公開API（認証不要）
Route::get('/profile/{userId}', [PublicProfileController::class, 'show']);
Route::get('/profile/avatar/by-hash/{hash}', [
    PublicProfileController::class,
    'showByAvatarHash',
]);

// テスト用：認証状態確認API（一時的に認証不要）
Route::get('/auth/status', function () {
    $request = request();

    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user()
            ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
            ]
            : null,
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'cookies' => $request->cookies->all(),
        'headers' => $request->headers->all(),
        'auth_guard' => config('auth.defaults.guard'),
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
    ]);
});
