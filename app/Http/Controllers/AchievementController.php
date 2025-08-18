<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AchievementController extends Controller
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
        $this->middleware('auth');
    }

    /**
     * ユーザーのアチーブメント設定を取得
     */
    public function getUserSettings(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->discord_id ?? $user->id;

            $settings = $this->achievementService->getUserSettings($userId);

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            Log::error('アチーブメント設定の取得に失敗しました', [
                'userId' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => '設定の取得に失敗しました',
                ],
                500,
            );
        }
    }

    /**
     * ユーザーのアチーブメント設定を更新
     */
    public function updateUserSettings(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'achievementsEnabled' => 'required|boolean',
                'notificationsEnabled' => 'required|boolean',
            ]);

            $user = Auth::user();
            $userId = $user->discord_id ?? $user->id;

            $settings = $this->achievementService->updateUserSettings(
                $userId,
                $request->achievementsEnabled,
                $request->notificationsEnabled,
            );

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => '設定が更新されました',
            ]);
        } catch (\Exception $e) {
            Log::error('アチーブメント設定の更新に失敗しました', [
                'userId' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => '設定の更新に失敗しました',
                ],
                500,
            );
        }
    }

    /**
     * ユーザーの獲得済みアチーブメント一覧を取得
     */
    public function getUnlockedAchievements(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->discord_id ?? $user->id;

            $achievements = $this->achievementService->getUnlockedAchievements(
                $userId,
            );

            return response()->json([
                'success' => true,
                'data' => $achievements,
            ]);
        } catch (\Exception $e) {
            Log::error('獲得済みアチーブメントの取得に失敗しました', [
                'userId' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'アチーブメントの取得に失敗しました',
                ],
                500,
            );
        }
    }

    /**
     * ユーザーの未獲得アチーブメント一覧を取得
     */
    public function getLockedAchievements(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->discord_id ?? $user->id;

            $achievements = $this->achievementService->getLockedAchievements(
                $userId,
            );

            return response()->json([
                'success' => true,
                'data' => $achievements,
            ]);
        } catch (\Exception $e) {
            Log::error('未獲得アチーブメントの取得に失敗しました', [
                'userId' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'アチーブメントの取得に失敗しました',
                ],
                500,
            );
        }
    }

    /**
     * ユーザーの全アチーブメント一覧を取得
     */
    public function getAllAchievements(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->discord_id ?? $user->id;

            $achievements = $this->achievementService->getAllAchievements(
                $userId,
            );

            return response()->json([
                'success' => true,
                'data' => $achievements,
            ]);
        } catch (\Exception $e) {
            Log::error('全アチーブメントの取得に失敗しました', [
                'userId' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'アチーブメントの取得に失敗しました',
                ],
                500,
            );
        }
    }
}
