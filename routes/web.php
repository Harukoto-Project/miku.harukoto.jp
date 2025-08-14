<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
