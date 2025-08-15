<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 認証関連ルート
|--------------------------------------------------------------------------
|
| ここでは認証に関連するルートを定義します。
| Discord OAuth、ログイン、ログアウトなどが含まれます。
|
*/

// 標準的な認証ルート
Route::get('/login', function () {
    return redirect('/discord/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/discord/login');
})->name('register');

// Discord OAuth関連のルート
Route::prefix('auth/discord')
    ->name('auth.discord.')
    ->group(function () {
        // Discord認証開始
        Route::get('/', [
            App\Http\Controllers\AuthController::class,
            'redirectToDiscord',
        ])->name('redirect');

        // Discord認証コールバック
        Route::get('/callback', [
            App\Http\Controllers\AuthController::class,
            'handleDiscordCallback',
        ])->name('callback');

        // Discord認証解除
        Route::post('/logout', [
            App\Http\Controllers\AuthController::class,
            'logout',
        ])->name('logout');
    });

// 後方互換性のためのエイリアス
Route::get('/discord/login', [
    App\Http\Controllers\AuthController::class,
    'redirectToDiscord',
])->name('discord.login');

// ログアウト
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

// 認証が必要なルートは routes/web.php で定義済み
