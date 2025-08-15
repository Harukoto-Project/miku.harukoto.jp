<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileLink;
use App\Models\ProfileTheme;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    public function __construct(private MinioService $minioService) {}

    /**
     * ユーザーIDでプロフィールを取得
     */
    public function getProfile(string $userId): ?Profile
    {
        return Profile::with(['links', 'theme'])
            ->byUserId($userId)
            ->first();
    }

    /**
     * プロフィールの完全性をチェック
     */
    public function isProfileComplete(Profile $profile): bool
    {
        // 必須フィールドのチェック
        $requiredFields = [
            'display_name' => $profile->display_name,
            'bio' => $profile->bio,
        ];

        foreach ($requiredFields as $field => $value) {
            if (empty($value) || $value === null) {
                return false;
            }
        }

        // アバターが設定されている場合は、avatar_hashも必要
        if (!empty($profile->avatar_url) && empty($profile->avatar_hash)) {
            return false;
        }

        return true;
    }

    /**
     * 不完全なプロフィールをクリーンアップ
     */
    public function cleanupIncompleteProfile(string $userId): void
    {
        try {
            $profile = Profile::byUserId($userId)->first();

            if (!$profile) {
                return;
            }

            // 不完全なプロフィールの場合、関連データを削除
            if (!$this->isProfileComplete($profile)) {
                DB::transaction(function () use ($profile) {
                    // 関連するリンクとテーマを削除
                    $profile->links()->delete();
                    $profile->theme()->delete();

                    // アバターが存在する場合は削除
                    if ($profile->avatar_url) {
                        $this->deleteOldAvatar($profile->avatar_url);
                    }

                    // プロフィール自体を削除
                    $profile->delete();
                });

                Log::info('Incomplete profile cleaned up', [
                    'user_id' => $userId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup incomplete profile', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * プロフィールを作成
     */
    public function createProfile(string $userId, array $data): Profile
    {
        try {
            $existingProfile = Profile::byUserId($userId)->first();

            if ($existingProfile) {
                throw new \Exception('Profile already exists for this user');
            }

            return DB::transaction(function () use ($userId, $data) {
                $profile = Profile::create([
                    'user_id' => $userId,
                    'display_name' => $data['display_name'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'pronouns' => $data['pronouns'] ?? null,
                    'location' => $data['location'] ?? null,
                    'custom_url' => $data['custom_url'] ?? null,
                ]);

                if (isset($data['links'])) {
                    $this->updateLinks($profile, $data['links']);
                }

                if (isset($data['theme'])) {
                    $this->updateTheme($profile, $data['theme']);
                }

                return $profile->fresh(['links', 'theme']);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create profile', [
                'user_id' => $userId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(string $userId, array $data): Profile
    {
        try {
            $profile = Profile::byUserId($userId)->first();

            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            return DB::transaction(function () use ($profile, $data) {
                // カスタムURLの重複チェック
                if (
                    isset($data['custom_url']) &&
                    $data['custom_url'] !== $profile->custom_url
                ) {
                    if ($data['custom_url']) {
                        $existingProfile = Profile::byCustomUrl(
                            $data['custom_url'],
                        )->first();
                        if (
                            $existingProfile &&
                            $existingProfile->id !== $profile->id
                        ) {
                            throw new \Exception('Custom URL already exists');
                        }
                    }
                }

                $profile->update([
                    'display_name' =>
                        $data['display_name'] ?? $profile->display_name,
                    'bio' => $data['bio'] ?? $profile->bio,
                    'pronouns' => $data['pronouns'] ?? $profile->pronouns,
                    'location' => $data['location'] ?? $profile->location,
                    'custom_url' => $data['custom_url'] ?? $profile->custom_url,
                ]);

                if (isset($data['links'])) {
                    $this->updateLinks($profile, $data['links']);
                }

                if (isset($data['theme'])) {
                    $this->updateTheme($profile, $data['theme']);
                }

                return $profile->fresh(['links', 'theme']);
            });
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'user_id' => $userId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * カスタムURLを更新
     */
    public function updateCustomUrl(string $userId, string $customUrl): Profile
    {
        try {
            $profile = Profile::byUserId($userId)->first();

            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            // カスタムURLの重複チェック
            if ($customUrl && $customUrl !== $profile->custom_url) {
                $existingProfile = Profile::byCustomUrl($customUrl)->first();
                if ($existingProfile) {
                    throw new \Exception('Custom URL already exists');
                }
            }

            $profile->update(['custom_url' => $customUrl ?: null]);

            return $profile->fresh(['links', 'theme']);
        } catch (\Exception $e) {
            Log::error('Failed to update custom URL', [
                'user_id' => $userId,
                'custom_url' => $customUrl,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * カスタムURLの可用性をチェック
     */
    public function isCustomUrlAvailable(
        string $customUrl,
        ?string $excludeUserId = null,
    ): bool {
        $query = Profile::byCustomUrl($customUrl);

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        return !$query->exists();
    }

    /**
     * アバターを更新
     */
    public function updateAvatar(
        string $userId,
        string $avatarUrl,
        string $avatarHash,
    ): Profile {
        $profile = Profile::byUserId($userId)->first();

        if (!$profile) {
            throw new \Exception('Profile not found');
        }

        // 古いアバターを削除
        if ($profile->avatar_url && $profile->avatar_url !== $avatarUrl) {
            $this->deleteOldAvatar($profile->avatar_url);
        }

        $profile->update([
            'avatar_url' => $avatarUrl,
            'avatar_hash' => $avatarHash,
        ]);

        return $profile->fresh(['links', 'theme']);
    }

    /**
     * アバターを削除
     */
    public function removeAvatar(string $userId): Profile
    {
        $profile = Profile::byUserId($userId)->first();

        if (!$profile) {
            throw new \Exception('Profile not found');
        }

        if ($profile->avatar_url) {
            $this->deleteOldAvatar($profile->avatar_url);
        }

        $profile->update([
            'avatar_url' => null,
            'avatar_hash' => null,
        ]);

        return $profile->fresh(['links', 'theme']);
    }

    /**
     * アバターアップロード準備
     */
    public function prepareAvatarUpload(string $userId, array $fileInfo): array
    {
        $profile = Profile::byUserId($userId)->first();

        if (!$profile) {
            throw new \Exception('Profile not found');
        }

        $avatarHash = $this->generateUniqueAvatarHash($userId);
        $fileName = "{$avatarHash}.{$this->getFileExtension(
            $fileInfo['fileType'],
        )}";

        $presignedUrl = $this->minioService->generatePresignedUrl(
            $fileName,
            $fileInfo['fileType'],
        );

        return [
            'signedUrl' => $presignedUrl['signedUrl'],
            'fileName' => $fileName,
            'avatarUrl' => $presignedUrl['publicUrl'],
            'avatarHash' => $avatarHash,
            'oldAvatarUrl' => $profile->avatar_url,
        ];
    }

    /**
     * リンクを更新
     */
    public function updateLinks(Profile $profile, array $links): void
    {
        $profile->links()->delete();

        foreach ($links as $index => $linkData) {
            $profile->links()->create([
                'button_id' => $linkData['button_id'],
                'url' => $linkData['url'],
                'display_name' => $linkData['display_name'],
                'custom_color' => $linkData['custom_color'] ?? null,
                'sort_order' => $linkData['sort_order'] ?? $index,
            ]);
        }
    }

    /**
     * テーマを更新
     */
    private function updateTheme(Profile $profile, array $themeData): void
    {
        $profile
            ->theme()
            ->updateOrCreate(['profile_id' => $profile->id], $themeData);
    }

    /**
     * 古いアバターを削除
     */
    private function deleteOldAvatar(string $avatarUrl): void
    {
        try {
            // URLからファイルパスを抽出
            $path = parse_url($avatarUrl, PHP_URL_PATH);
            if ($path) {
                $filePath = ltrim($path, '/');
                $this->minioService->deleteFile($filePath);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete old avatar', [
                'avatar_url' => $avatarUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 一意のアバターハッシュを生成
     */
    public function generateUniqueAvatarHash(
        string $userId,
        int $maxRetries = 10,
    ): string {
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $hash = $this->generateUserBasedHash($userId);

            $existingProfile = Profile::byAvatarHash($hash)->first();

            if (!$existingProfile) {
                return $hash;
            }
        }

        $fallbackHash = Str::random(32);
        $existingFallback = Profile::byAvatarHash($fallbackHash)->first();

        if ($existingFallback) {
            throw new \Exception(
                'Failed to generate unique avatar hash after maximum retries',
            );
        }

        return $fallbackHash;
    }

    /**
     * ユーザーベースのハッシュを生成
     */
    private function generateUserBasedHash(string $userId): string
    {
        $timestamp = time();
        $randomBytes = Str::random(16);
        $combinedString = "{$userId}-{$timestamp}-{$randomBytes}";

        return substr(hash('sha256', $combinedString), 0, 32);
    }

    /**
     * ファイル拡張子を取得
     */
    private function getFileExtension(string $mimeType): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    /**
     * リンクを更新
     */
    public function updateLinksByUserId(string $userId, array $links): Profile
    {
        try {
            $profile = $this->getProfile($userId);

            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            DB::transaction(function () use ($profile, $links) {
                // 既存のリンクを削除
                $profile->links()->delete();

                // 新しいリンクを作成
                foreach ($links as $index => $linkData) {
                    $profile->links()->create([
                        'button_id' => $linkData['button_id'],
                        'url' => $linkData['url'],
                        'display_name' => $linkData['display_name'],
                        'custom_color' =>
                            $linkData['custom_color'] ?? '#6B7280',
                        'sort_order' => $linkData['sort_order'] ?? $index,
                    ]);
                }
            });

            // 更新されたプロフィールを返す
            return $this->getProfile($userId);
        } catch (\Exception $e) {
            Log::error('Failed to update links', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * リンクを削除
     */
    public function deleteLink(string $userId, $linkId): Profile
    {
        try {
            $profile = $this->getProfile($userId);

            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            // 指定されたリンクを削除
            $link = $profile->links()->find($linkId);

            if (!$link) {
                throw new \Exception('Link not found');
            }

            $link->delete();

            // 更新されたプロフィールを返す
            return $this->getProfile($userId);
        } catch (\Exception $e) {
            Log::error('Failed to delete link', [
                'user_id' => $userId,
                'link_id' => $linkId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * プロフィールを削除
     */
    public function deleteProfile(string $userId): void
    {
        try {
            $profile = Profile::byUserId($userId)->first();

            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            DB::transaction(function () use ($profile) {
                // 関連するリンクとテーマを削除
                $profile->links()->delete();
                $profile->theme()->delete();

                // アバターが存在する場合は削除
                if ($profile->avatar_url) {
                    $this->deleteOldAvatar($profile->avatar_url);
                }

                // プロフィール自体を削除
                $profile->delete();
            });

            Log::info('Profile deleted successfully', [
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete profile', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
