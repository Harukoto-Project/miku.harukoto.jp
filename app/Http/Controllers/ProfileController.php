<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    /**
     * 公開プロフィールを表示（DiscordID指定）
     */
    public function showPublic(string $discordId): View
    {
        $user = User::where('discord_id', $discordId)->first();

        if (!$user) {
            abort(404, 'ユーザーが見つかりません');
        }

        $profile = $this->profileService->getProfile($user->id);

        if (!$profile) {
            abort(404, 'プロフィールが見つかりません');
        }

        $buttonTypes = $this->getButtonTypes();
        return view('profile.show', compact('profile', 'user', 'buttonTypes'));
    }

    /**
     * カスタムURLによるプロフィール表示
     */
    public function showByCustomUrl(string $customUrl): View
    {
        $profile = Profile::where('custom_url', $customUrl)->first();

        if (!$profile) {
            abort(404, 'プロフィールが見つかりません');
        }

        $user = User::find($profile->user_id);

        if (!$user) {
            abort(404, 'ユーザーが見つかりません');
        }

        $buttonTypes = $this->getButtonTypes();
        return view('profile.show', compact('profile', 'user', 'buttonTypes'));
    }

    /**
     * ボタンタイプの定義を取得
     */
    private function getButtonTypes(): array
    {
        return [
            'website' => [
                'label' => 'ウェブサイト',
                'icon' => 'fas fa-globe',
                'defaultColor' => '#2563eb',
            ],
            'twitter' => [
                'label' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'defaultColor' => '#0ea5e9',
            ],
            'github' => [
                'label' => 'GitHub',
                'icon' => 'fab fa-github',
                'defaultColor' => '#1f2937',
            ],
            'youtube' => [
                'label' => 'YouTube',
                'icon' => 'fab fa-youtube',
                'defaultColor' => '#dc2626',
            ],
            'instagram' => [
                'label' => 'Instagram',
                'icon' => 'fab fa-instagram',
                'defaultColor' => '#db2777',
            ],
            'discord' => [
                'label' => 'Discord',
                'icon' => 'fab fa-discord',
                'defaultColor' => '#4f46e5',
            ],
            'twitch' => [
                'label' => 'Twitch',
                'icon' => 'fab fa-twitch',
                'defaultColor' => '#7c3aed',
            ],
            'linkedin' => [
                'label' => 'LinkedIn',
                'icon' => 'fab fa-linkedin',
                'defaultColor' => '#1d4ed8',
            ],
            'facebook' => [
                'label' => 'Facebook',
                'icon' => 'fab fa-facebook',
                'defaultColor' => '#1e40af',
            ],
            'tiktok' => [
                'label' => 'TikTok',
                'icon' => 'fab fa-tiktok',
                'defaultColor' => '#000000',
            ],
            'steam' => [
                'label' => 'Steam',
                'icon' => 'fab fa-steam',
                'defaultColor' => '#374151',
            ],
            'spotify' => [
                'label' => 'Spotify',
                'icon' => 'fab fa-spotify',
                'defaultColor' => '#16a34a',
            ],
            'paypal' => [
                'label' => 'PayPal',
                'icon' => 'fab fa-paypal',
                'defaultColor' => '#2563eb',
            ],
            'amazon' => [
                'label' => 'Amazon',
                'icon' => 'fas fa-shopping-cart',
                'defaultColor' => '#f97316',
            ],
            'email' => [
                'label' => 'メール',
                'icon' => 'fas fa-envelope',
                'defaultColor' => '#4b5563',
            ],
        ];
    }

    /**
     * プロフィールを取得
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $this->profileService->getProfile($user->id);

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    /**
     * プロフィールを作成
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
            'pronouns' => 'nullable|string|max:32',
            'location' => 'nullable|string|max:32',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $user = $request->user();
            $profile = $this->profileService->createProfile(
                $user->id,
                $request->all(),
            );

            return response()->json(
                [
                    'message' => 'Profile created successfully',
                    'profile' => $profile,
                ],
                201,
            );
        } catch (\Exception $e) {
            \Log::error('Profile creation failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to create profile',
                    'message' => $e->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * プロフィールを更新
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
            'pronouns' => 'nullable|string|max:32',
            'location' => 'nullable|string|max:32',
            'custom_url' =>
                'nullable|string|min:3|max:30|regex:/^[a-zA-Z0-9._]+$/',
            'links' => 'nullable|array|max:10',
            'links.*.button_id' => 'required|string|max:50',
            'links.*.url' => 'required|url|max:500',
            'links.*.display_name' => 'required|string|max:50',
            'links.*.custom_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'links.*.sort_order' => 'nullable|integer|min:0',
            'theme' => 'nullable|array',
            'theme.background_color_type' => 'nullable|in:solid,gradient',
            'theme.background_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.background_color_gradient_stops' => 'nullable|array',
            'theme.background_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
            'theme.display_name_color_type' => 'nullable|in:solid,gradient',
            'theme.display_name_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.display_name_color_gradient_stops' => 'nullable|array',
            'theme.display_name_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
            'theme.pronouns_color_type' => 'nullable|in:solid,gradient',
            'theme.pronouns_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.pronouns_color_gradient_stops' => 'nullable|array',
            'theme.pronouns_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
            'theme.location_color_type' => 'nullable|in:solid,gradient',
            'theme.location_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.location_color_gradient_stops' => 'nullable|array',
            'theme.location_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
            'theme.bio_text_color_type' => 'nullable|in:solid,gradient',
            'theme.bio_text_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.bio_text_color_gradient_stops' => 'nullable|array',
            'theme.bio_text_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
            'theme.bio_background_color_type' => 'nullable|in:solid,gradient',
            'theme.bio_background_color_solid' =>
                'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.bio_background_color_gradient_stops' => 'nullable|array',
            'theme.bio_background_color_gradient_direction' =>
                'nullable|integer|min:0|max:360',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $user = $request->user();
            $profile = $this->profileService->updateProfile(
                $user->id,
                $request->all(),
            );

            return response()->json([
                'message' => 'Profile updated successfully',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to update profile',
                    'message' => $e->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * カスタムURLを更新
     */
    public function updateCustomUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'custom_url' =>
                'nullable|string|min:3|max:30|regex:/^[a-zA-Z0-9._]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $request->user();
            $profile = $this->profileService->updateCustomUrl(
                $user->id,
                $request->input('custom_url'),
            );

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * カスタムURLの可用性をチェック
     */
    public function checkCustomUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9._]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $isAvailable = $this->profileService->isCustomUrlAvailable(
            $request->input('url'),
            $user->id,
        );

        return response()->json(['available' => $isAvailable]);
    }

    /**
     * アバターアップロード準備
     */
    public function prepareAvatarUpload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fileName' => 'required|string|max:255',
            'fileType' =>
                'required|string|in:image/jpeg,image/jpg,image/png,image/webp,image/gif',
            'fileSize' => 'required|integer|max:1048576', // 1MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $request->user();
            $result = $this->profileService->prepareAvatarUpload(
                $user->id,
                $request->all(),
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * アバターを適用
     */
    public function applyAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatarUrl' => 'required|url',
            'avatarHash' => 'required|string|max:255',
            'oldAvatarUrl' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $request->user();
            $profile = $this->profileService->updateAvatar(
                $user->id,
                $request->input('avatarUrl'),
                $request->input('avatarHash'),
            );

            return response()->json([
                'success' => true,
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * アバターを削除
     */
    public function removeAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = $this->profileService->removeAvatar($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Avatar removed successfully',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * リンクを更新
     */
    public function updateLinks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'links' => 'required|array|max:10',
            'links.*.button_id' => 'required|string|max:50',
            'links.*.url' => 'required|url|max:500',
            'links.*.display_name' => 'required|string|max:50',
            'links.*.custom_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'links.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $user = $request->user();
            $profile = $this->profileService->updateLinksByUserId(
                $user->id,
                $request->input('links'),
            );

            return response()->json([
                'message' => 'Links updated successfully',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            \Log::error('Links update failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to update links',
                    'message' => $e->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * リンクを削除
     */
    public function deleteLink(Request $request, $linkId): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = $this->profileService->deleteLink($user->id, $linkId);

            return response()->json([
                'success' => true,
                'message' => 'Link deleted successfully',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            \Log::error('Link deletion failed', [
                'user_id' => $user->id ?? 'unknown',
                'link_id' => $linkId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to delete link',
                    'message' => $e->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * プロフィールを削除
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = $this->profileService->deleteProfile($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile deletion failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to delete profile',
                    'message' => $e->getMessage(),
                ],
                400,
            );
        }
    }
}
