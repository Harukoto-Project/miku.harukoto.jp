<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiTokenValidationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API Token Validation Routes
|--------------------------------------------------------------------------
|
| これらのルートは、APIトークンの検証と権限チェックを行います。
| 外部APIからのトークン検証リクエストを受け付けます。
|
*/

// APIトークンの検証
Route::post('/validate-token', [ApiTokenValidationController::class, 'validateToken']);

// 特定の権限のチェック
Route::post('/check-permission', [ApiTokenValidationController::class, 'checkPermission']);
