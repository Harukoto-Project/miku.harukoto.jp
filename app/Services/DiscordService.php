<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    public function findOrCreateUser(array $discordUser): User
    {
        try {
            Log::info('Finding or creating user', [
                'discord_id' => $discordUser['id'],
            ]);

            $user = User::where('discord_id', $discordUser['id'])->first();

            if (!$user) {
                Log::info('Creating new user');

                $user = User::create([
                    'name' =>
                        $discordUser['display_name'] ??
                        ($discordUser['global_name'] ??
                            $discordUser['username']),
                    'discord_id' => $discordUser['id'],
                    'discord_username' => $discordUser['username'],
                    'discord_display_name' =>
                        $discordUser['display_name'] ??
                        ($discordUser['global_name'] ?? null),
                    'email' => $discordUser['email'] ?? null,
                    'avatar_url' => $this->getAvatarUrl($discordUser),
                ]);

                Log::info('New user created', ['user_id' => $user->id]);
            } else {
                Log::info('Updating existing user', ['user_id' => $user->id]);

                // 既存ユーザーの情報を更新
                $user->update([
                    'name' =>
                        $discordUser['display_name'] ??
                        ($discordUser['global_name'] ??
                            $discordUser['username']),
                    'discord_username' => $discordUser['username'],
                    'discord_display_name' =>
                        $discordUser['display_name'] ??
                        ($discordUser['global_name'] ?? null),
                    'avatar_url' => $this->getAvatarUrl($discordUser),
                ]);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('Error in findOrCreateUser', [
                'message' => $e->getMessage(),
                'discord_user' => $discordUser,
            ]);
            throw $e;
        }
    }

    public function getAvatarUrl(array $discordUser): string
    {
        if (empty($discordUser['avatar'])) {
            // デフォルトアバターの場合（新しいDiscord APIに対応）
            if (
                isset($discordUser['discriminator']) &&
                $discordUser['discriminator'] !== '0'
            ) {
                // 旧形式のdiscriminator
                $defaultAvatarIndex = intval($discordUser['discriminator']) % 5;
            } else {
                // 新形式（username system）
                $defaultAvatarIndex = (intval($discordUser['id']) >> 22) % 6;
            }
            return "https://cdn.discordapp.com/embed/avatars/{$defaultAvatarIndex}.png";
        }

        // カスタムアバターの場合
        $format = str_starts_with($discordUser['avatar'], 'a_') ? 'gif' : 'png';
        return "https://cdn.discordapp.com/avatars/{$discordUser['id']}/{$discordUser['avatar']}.{$format}?size=256";
    }

    public function getUserInfo(string $discordId): ?array
    {
        $user = User::where('discord_id', $discordId)->first();

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->discord_id,
            'username' => $user->discord_username,
            'avatar_url' => $user->avatar_url,
        ];
    }

    /**
     * ユーザーが参加しているサーバーを取得
     */
    public function getUserGuilds(string $accessToken): array
    {
        try {
            $httpOptions = [
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ],
            ];

            $response = Http::withOptions($httpOptions)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'User-Agent' => 'DiscordBot (Laravel App, 1.0)',
                ])
                ->get('https://discord.com/api/users/@me/guilds');

            if (!$response->successful()) {
                Log::error('Failed to fetch user guilds', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching user guilds', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Botが参加しているサーバーを取得
     */
    public function getBotGuilds(): array
    {
        try {
            $botToken = config('services.discord.bot_token');

            if (empty($botToken)) {
                Log::error('Bot token not configured');
                return [];
            }

            $httpOptions = [
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ],
            ];

            $response = Http::withOptions($httpOptions)
                ->withHeaders([
                    'Authorization' => 'Bot ' . $botToken,
                    'User-Agent' => 'DiscordBot (Laravel App, 1.0)',
                ])
                ->get('https://discord.com/api/users/@me/guilds');

            if (!$response->successful()) {
                Log::error('Failed to fetch bot guilds', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching bot guilds', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * ギルドのチャンネル一覧を取得（Botトークン使用）
     */
    public function getGuildChannels(string $guildId): array
    {
        try {
            $botToken = config('services.discord.bot_token');

            if (empty($botToken)) {
                Log::error('Bot token not configured');
                return [];
            }

            $httpOptions = [
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ],
            ];

            $response = Http::withOptions($httpOptions)
                ->withHeaders([
                    'Authorization' => 'Bot ' . $botToken,
                    'User-Agent' => 'DiscordBot (Laravel App, 1.0)',
                ])
                ->get("https://discord.com/api/guilds/{$guildId}/channels");

            if (!$response->successful()) {
                Log::error('Failed to fetch guild channels', [
                    'guild_id' => $guildId,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching guild channels', [
                'guild_id' => $guildId,
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * サーバーアイコンのURLを生成
     */
    public function getGuildIconUrl(array $guild): string
    {
        if (empty($guild['icon'])) {
            // デフォルトアイコンの場合（Discordの文字アイコン）
            return 'https://cdn.discordapp.com/embed/avatars/' .
                (intval($guild['id']) >> 22) % 6 .
                '.png';
        }

        // カスタムアイコンの場合
        $format = str_starts_with($guild['icon'], 'a_') ? 'gif' : 'png';
        return "https://cdn.discordapp.com/icons/{$guild['id']}/{$guild['icon']}.{$format}?size=128";
    }

    /**
     * 共通のサーバーを取得し、Botの導入状況を判定
     */
    public function getCommonGuildsWithBotStatus(
        array $userGuilds,
        array $botGuilds,
    ): array {
        $botGuildIds = array_column($botGuilds, 'id');
        $commonGuilds = [];

        foreach ($userGuilds as $guild) {
            // ユーザーが管理権限を持っているサーバーのみを対象とする
            if (($guild['permissions'] & 0x8) === 0x8) {
                // ADMINISTRATOR権限
                $guild['has_miku'] = in_array($guild['id'], $botGuildIds);
                $guild['icon_url'] = $this->getGuildIconUrl($guild);
                $commonGuilds[] = $guild;
            }
        }

        return $commonGuilds;
    }
}
