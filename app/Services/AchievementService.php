<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    protected $mikuApiService;
    protected $baseUrl;

    public function __construct(MikuApiService $mikuApiService)
    {
        $this->mikuApiService = $mikuApiService;
        $this->baseUrl = config(
            'services.miku.base_url',
            'http://localhost:3000',
        );
    }

    /**
     * ユーザーのアチーブメント設定を更新
     */
    public function updateUserSettings(
        string $userId,
        bool $achievementsEnabled,
        bool $notificationsEnabled,
    ): array {
        try {
            $response = $this->mikuApiService->makeRequest(
                'PUT',
                "/user/{$userId}/settings/achievements",
                [
                    'achievementsEnabled' => $achievementsEnabled,
                    'notificationsEnabled' => $notificationsEnabled,
                ],
            );

            return $response;
        } catch (\Exception $e) {
            Log::error('アチーブメント設定の更新に失敗しました', [
                'userId' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ユーザーのアチーブメント設定を取得
     */
    public function getUserSettings(string $userId): array
    {
        try {
            $response = $this->mikuApiService->makeRequest(
                'GET',
                "/user/{$userId}/settings/achievements",
            );
            return $response;
        } catch (\Exception $e) {
            Log::error('アチーブメント設定の取得に失敗しました', [
                'userId' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ユーザーの獲得済みアチーブメント一覧を取得
     */
    public function getUnlockedAchievements(string $userId): array
    {
        try {
            $response = $this->mikuApiService->makeRequest(
                'GET',
                "/user/{$userId}/achievements/unlocked",
            );
            return $response;
        } catch (\Exception $e) {
            Log::error('獲得済みアチーブメントの取得に失敗しました', [
                'userId' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ユーザーの未獲得アチーブメント一覧を取得
     */
    public function getLockedAchievements(string $userId): array
    {
        try {
            $response = $this->mikuApiService->makeRequest(
                'GET',
                "/user/{$userId}/achievements/locked",
            );
            return $response;
        } catch (\Exception $e) {
            Log::error('未獲得アチーブメントの取得に失敗しました', [
                'userId' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ユーザーの全アチーブメント一覧を取得
     */
    public function getAllAchievements(string $userId): array
    {
        try {
            $response = $this->mikuApiService->makeRequest(
                'GET',
                "/user/{$userId}/achievements",
            );
            return $response;
        } catch (\Exception $e) {
            Log::error('全アチーブメントの取得に失敗しました', [
                'userId' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * アチーブメント設定のデフォルト値を取得
     */
    public function getDefaultSettings(): array
    {
        return [
            'achievementsEnabled' => true,
            'notificationsEnabled' => true,
        ];
    }
}
