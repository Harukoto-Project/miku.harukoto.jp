<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiTokenValidationController extends Controller
{
    /**
     * APIトークンの検証
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            // リクエストのバリデーション
            $validated = $request->validate([
                'token' => 'required|string|max:64',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $token = $validated['token'];
            $userId = $validated['user_id'];

            // ユーザーを取得
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'valid' => false,
                    'error' => 'User not found',
                    'message' => '指定されたユーザーが見つかりません',
                    'code' => 404
                ], 404);
            }

            // トークンの検証
            $validationResult = $this->validateUserToken($user, $token);

            if ($validationResult['valid']) {
                return response()->json([
                    'valid' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'discord_username' => $user->discord_username,
                        'discord_display_name' => $user->discord_display_name,
                    ],
                    'permissions' => $user->api_permissions ?? [],
                    'expires_at' => $user->api_token_expires_at?->toISOString(),
                    'message' => 'トークンが有効です'
                ], 200);
            } else {
                return response()->json([
                    'valid' => false,
                    'error' => $validationResult['error'],
                    'message' => $validationResult['message'],
                    'code' => $validationResult['code']
                ], $validationResult['code']);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'valid' => false,
                'error' => 'Validation failed',
                'message' => 'リクエストの形式が正しくありません',
                'details' => $e->errors(),
                'code' => 400
            ], 400);

        } catch (\Exception $e) {
            Log::error('API token validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'valid' => false,
                'error' => 'Internal server error',
                'message' => 'サーバー内部エラーが発生しました',
                'code' => 500
            ], 500);
        }
    }

    /**
     * ユーザーのAPIトークンを検証
     *
     * @param User $user
     * @param string $token
     * @return array
     */
    private function validateUserToken(User $user, string $token): array
    {
        // トークンが存在するかチェック
        if (!$user->api_token) {
            return [
                'valid' => false,
                'error' => 'Token not found',
                'message' => 'APIトークンが設定されていません',
                'code' => 401
            ];
        }

        // トークンが一致するかチェック
        if ($user->api_token !== $token) {
            Log::warning('Invalid API token provided', [
                'user_id' => $user->id,
                'provided_token' => $token,
                'expected_token' => $user->api_token
            ]);

            return [
                'valid' => false,
                'error' => 'Invalid token',
                'message' => '無効なAPIトークンです',
                'code' => 401
            ];
        }

        // トークンの有効期限をチェック
        if (!$user->api_token_expires_at) {
            return [
                'valid' => false,
                'error' => 'Token expiration not set',
                'message' => 'APIトークンの有効期限が設定されていません',
                'code' => 401
            ];
        }

        if (now()->isAfter($user->api_token_expires_at)) {
            Log::info('API token expired', [
                'user_id' => $user->id,
                'expired_at' => $user->api_token_expires_at
            ]);

            return [
                'valid' => false,
                'error' => 'Token expired',
                'message' => 'APIトークンの有効期限が切れています',
                'code' => 401
            ];
        }

        // トークンが有効
        Log::info('API token validated successfully', [
            'user_id' => $user->id,
            'permissions' => $user->api_permissions
        ]);

        return [
            'valid' => true,
            'error' => null,
            'message' => null,
            'code' => 200
        ];
    }

    /**
     * トークンの権限チェック
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPermission(Request $request): JsonResponse
    {
        try {
            // リクエストのバリデーション
            $validated = $request->validate([
                'token' => 'required|string|max:64',
                'user_id' => 'required|integer|exists:users,id',
                'permission' => 'required|string'
            ]);

            $token = $validated['token'];
            $userId = $validated['user_id'];
            $requiredPermission = $validated['permission'];

            // ユーザーを取得
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'has_permission' => false,
                    'error' => 'User not found',
                    'message' => '指定されたユーザーが見つかりません',
                    'code' => 404
                ], 404);
            }

            // トークンの検証
            $tokenValidation = $this->validateUserToken($user, $token);

            if (!$tokenValidation['valid']) {
                return response()->json([
                    'has_permission' => false,
                    'error' => $tokenValidation['error'],
                    'message' => $tokenValidation['message'],
                    'code' => $tokenValidation['code']
                ], $tokenValidation['code']);
            }

            // 権限チェック
            $permissions = $user->api_permissions ?? [];
            $hasPermission = in_array($requiredPermission, $permissions) || in_array('*', $permissions);

            return response()->json([
                'has_permission' => $hasPermission,
                'user_id' => $user->id,
                'required_permission' => $requiredPermission,
                'user_permissions' => $permissions,
                'message' => $hasPermission ? '権限があります' : '権限が不足しています'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'has_permission' => false,
                'error' => 'Validation failed',
                'message' => 'リクエストの形式が正しくありません',
                'details' => $e->errors(),
                'code' => 400
            ], 400);

        } catch (\Exception $e) {
            Log::error('API permission check error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'has_permission' => false,
                'error' => 'Internal server error',
                'message' => 'サーバー内部エラーが発生しました',
                'code' => 500
            ], 500);
        }
    }
}
