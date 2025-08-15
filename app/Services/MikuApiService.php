<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MikuApiService
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('services.miku.api_url');
        $this->apiToken = config('services.miku.api_token');
    }

    /**
     * Welcomer設定を取得
     */
    public function getWelcomerSettings(string $guildId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->get("{$this->baseUrl}/guild/{$guildId}/settings/welcomer");

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                return null; // 設定が存在しない
            }

            Log::error('Failed to fetch welcomer settings', [
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching welcomer settings', [
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
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
                'Content-Type' => 'application/json',
            ])->put("{$this->baseUrl}/guild/{$guildId}/settings/welcomer/join", $data);

            if ($response->successful()) {
                Log::info('Welcomer join settings updated successfully', [
                    'guild_id' => $guildId,
                    'data' => $data,
                ]);
                return true;
            }

            Log::error('Failed to update welcomer join settings', [
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error updating welcomer join settings', [
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
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
                'Content-Type' => 'application/json',
            ])->put("{$this->baseUrl}/guild/{$guildId}/settings/welcomer/leave", $data);

            if ($response->successful()) {
                Log::info('Welcomer leave settings updated successfully', [
                    'guild_id' => $guildId,
                    'data' => $data,
                ]);
                return true;
            }

            Log::error('Failed to update welcomer leave settings', [
                'guild_id' => $guildId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error updating welcomer leave settings', [
                'guild_id' => $guildId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
