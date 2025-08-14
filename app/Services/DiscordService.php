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
                    'name' => $discordUser['username'],
                    'discord_id' => $discordUser['id'],
                    'discord_username' => $discordUser['username'],
                    'email' => $discordUser['email'] ?? null,
                    'avatar_url' => $this->getAvatarUrl($discordUser),
                ]);

                Log::info('New user created', ['user_id' => $user->id]);
            } else {
                Log::info('Updating existing user', ['user_id' => $user->id]);

                // 既存ユーザーの情報を更新
                $user->update([
                    'name' => $discordUser['username'],
                    'discord_username' => $discordUser['username'],
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
}
