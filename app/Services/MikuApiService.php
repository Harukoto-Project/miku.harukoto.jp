<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MikuApiService
{
    protected $baseUrl;
    protected $user;

    public function __construct(?User $user = null)
    {
        $this->baseUrl = config('services.miku.api_url');
        $this->user = $user;
    }

    /**
     * ユーザーのAPIトークンが有効かチェック
     */
    protected function validateUserToken(): bool
    {
        if (!$this->user) {
            Log::error('User not provided for API call');
            return false;
        }

        if (!$this->user->api_token || !$this->user->api_token_expires_at) {
            Log::error('User API token not found or expired', ['user_id' => $this->user->id]);
            return false;
        }

        if (now()->isAfter($this->user->api_token_expires_at)) {
            Log::error('User API token has expired', ['user_id' => $this->user->id]);
            return false;
        }

        return true;
    }

    /**
     * ユーザーが指定された権限を持っているかチェック
     */
    protected function hasPermission(string $permission): bool
    {
        if (!$this->validateUserToken()) {
            return false;
        }

        $permissions = $this->user->api_permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Welcomer設定を取得
     */
    public function getWelcomerSettings(string $guildId): ?array
    {
        if (!$this->hasPermission('welcomer.read')) {
            Log::error('User does not have permission to read welcomer settings', [
                'user_id' => $this->user?->id,
                'guild_id' => $guildId,
            ]);
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->user->api_token,
                'X-User-ID' => $this->user->id,
            ])->get("{$this->baseUrl}/guild/{$guildId}/settings/welcomer");

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                return null; // 設定が存在しない
            }

            Log::error('Failed to fetch welcomer settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching welcomer settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 参加時のWelcomer設定を更新
     */
    public function updateJoinWelcomer(string $guildId, array $data): bool
    {
        if (!$this->hasPermission('welcomer.write')) {
            Log::error('User does not have permission to update welcomer settings', [
                'user_id' => $this->user?->id,
                'guild_id' => $guildId,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->user->api_token,
                'X-User-ID' => $this->user->id,
                'Content-Type' => 'application/json',
            ])->put(
                "{$this->baseUrl}/guild/{$guildId}/settings/welcomer/join",
                $data,
            );

            if ($response->successful()) {
                Log::info('Welcomer join settings updated successfully', [
                    'user_id' => $this->user->id,
                    'guild_id' => $guildId,
                    'data' => $data,
                ]);
                return true;
            }

            Log::error('Failed to update welcomer join settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error updating welcomer join settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * 退出時のWelcomer設定を更新
     */
    public function updateLeaveWelcomer(string $guildId, array $data): bool
    {
        if (!$this->hasPermission('welcomer.write')) {
            Log::error('User does not have permission to update welcomer settings', [
                'user_id' => $this->user?->id,
                'guild_id' => $guildId,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->user->api_token,
                'Content-Type' => 'application/json',
            ])->put(
                "{$this->baseUrl}/guild/{$guildId}/settings/welcomer/leave",
                $data,
            );

            if ($response->successful()) {
                Log::info('Welcomer leave settings updated successfully', [
                    'user_id' => $this->user->id,
                    'guild_id' => $guildId,
                    'data' => $data,
                ]);
                return true;
            }

            Log::error('Failed to update welcomer leave settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error updating welcomer leave settings', [
                'user_id' => $this->user->id,
                'guild_id' => $guildId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * ユーザーのAPIトークンを生成
     */
    public static function generateApiToken(User $user, array $permissions = [], int $expiryHours = 24): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addHours($expiryHours);

        $user->update([
            'api_token' => $token,
            'api_token_expires_at' => $expiresAt,
            'api_permissions' => $permissions,
        ]);

        return $token;
    }

    /**
     * ユーザーのAPIトークンを無効化
     */
    public static function revokeApiToken(User $user): bool
    {
        return $user->update([
            'api_token' => null,
            'api_token_expires_at' => null,
            'api_permissions' => null,
        ]);
    }
}
