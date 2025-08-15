<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServerSettingsController;
use App\Http\Controllers\WelcomerSettingsController;
use App\Http\Controllers\UserSettingsController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Discord OAuth routes
Route::get('/auth/discord', [AuthController::class, 'redirectToDiscord'])->name(
    'discord.login',
);
Route::get('/auth/discord/callback', [
    AuthController::class,
    'handleDiscordCallback',
])->name('discord.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 従来のloginルートをDiscordログインにリダイレクト
Route::get('/login', function () {
    return redirect()->route('discord.login');
})->name('login');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name(
        'dashboard',
    );
    Route::post('/dashboard/invite-bot', [
        DashboardController::class,
        'inviteBot',
    ])->name('dashboard.invite-bot');

    // サーバー設定関連のルート
    Route::get('/server/{guildId}/settings', [ServerSettingsController::class, 'show'])->name('server.settings');
    
    // Welcomer設定のAPIルート
    Route::prefix('api/guild/{guildId}/settings/welcomer')->group(function () {
        Route::get('/', [WelcomerSettingsController::class, 'getSettings']);
        Route::put('/join', [WelcomerSettingsController::class, 'updateJoinSettings']);
        Route::put('/leave', [WelcomerSettingsController::class, 'updateLeaveSettings']);
        Route::get('/channels', [WelcomerSettingsController::class, 'getChannels']);
    });

    Route::get('/features', function () {
        return view('features');
    })->name('features');

    Route::get('/commands', function () {
        return view('commands');
    })->name('commands');

    Route::get('/terms', function () {
        return view('terms');
    })->name('terms');

    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy');
});
