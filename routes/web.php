<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\ServerSettingsController;
use App\Http\Controllers\WelcomerSettingsController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// プロフィール表示用のルート（認証不要）
Route::get('/profile/{discordId}', [
    ProfileController::class,
    'showPublic',
])->name('profile.show');
Route::get('/p/{customUrl}', [
    ProfileController::class,
    'showByCustomUrl',
])->name('profile.custom');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name(
        'dashboard',
    );
    Route::post('/dashboard/invite-bot', [
        DashboardController::class,
        'inviteBot',
    ])->name('dashboard.invite-bot');
    Route::get('/user-settings', [
        UserSettingsController::class,
        'index',
    ])->name('user.settings');
    Route::get('/server-settings', [
        ServerSettingsController::class,
        'index',
    ])->name('server.settings');
    Route::get('/welcomer-settings', [
        WelcomerSettingsController::class,
        'index',
    ])->name('welcomer.settings');
});

require __DIR__ . '/auth.php';
