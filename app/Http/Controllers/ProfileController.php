<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        // デバッグ情報をログに出力
        \Log::info('Profile update request', [
            'allData' => $request->all(),
            'card_gradient_stops' => $request->input('card_gradient_stops'),
            'page_gradient_stops' => $request->input('page_gradient_stops'),
            'bio_card_gradient_stops' => $request->input(
                'bio_card_gradient_stops',
            ),
        ]);

        $request->validate([
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'pronouns' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'theme_color' => 'nullable|string|max:7',
            'card_background_color' => 'nullable|string|max:25',
            'page_background_color' => 'nullable|string|max:7',
            'profile_name_color' => 'nullable|string|max:7',
            'pronouns_color' => 'nullable|string|max:7',
            'location_color' => 'nullable|string|max:7',
            'discord_color' => 'nullable|string|max:7',
            'bio_card_color' => 'nullable|string|max:25',
            'bio_text_color' => 'nullable|string|max:7',
            'card_gradient_enabled' => 'boolean',
            'card_gradient_stops' => 'nullable|string',
            'card_gradient_direction' => 'nullable|string|max:20',
            'page_gradient_enabled' => 'boolean',
            'page_gradient_stops' => 'nullable|string',
            'page_gradient_direction' => 'nullable|string|max:20',
            'bio_card_gradient_enabled' => 'boolean',
            'bio_card_gradient_stops' => 'nullable|string',
            'bio_card_gradient_direction' => 'nullable|string|max:20',
            'is_public' => 'boolean',
        ]);

        $user = Auth::user();

        $profileData = $request->only([
            'display_name',
            'bio',
            'pronouns',
            'location',
            'theme_color',
            'card_background_color',
            'page_background_color',
            'profile_name_color',
            'pronouns_color',
            'location_color',
            'discord_color',
            'bio_card_color',
            'bio_text_color',
            'card_gradient_enabled',
            'card_gradient_stops',
            'card_gradient_direction',
            'page_gradient_enabled',
            'page_gradient_stops',
            'page_gradient_direction',
            'bio_card_gradient_enabled',
            'bio_card_gradient_stops',
            'bio_card_gradient_direction',
            'is_public',
        ]);

        // グラデーション設定のデータを検証
        foreach (
            [
                'card_gradient_stops',
                'page_gradient_stops',
                'bio_card_gradient_stops',
            ]
            as $field
        ) {
            if (
                isset($profileData[$field]) &&
                is_string($profileData[$field])
            ) {
                try {
                    $decoded = json_decode($profileData[$field], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $profileData[$field] = $profileData[$field]; // 有効なJSONとして保存
                    } else {
                        \Log::warning(
                            "Invalid JSON for {$field}: " .
                                json_last_error_msg(),
                        );
                        $profileData[$field] = null;
                    }
                } catch (Exception $e) {
                    \Log::warning(
                        "Error processing {$field}: " . $e->getMessage(),
                    );
                    $profileData[$field] = null;
                }
            }
        }

        // デバッグ情報をログに出力
        \Log::info('Profile data to save', [
            'profileData' => $profileData,
            'processedGradientStops' => [
                'card' => $profileData['card_gradient_stops'] ?? null,
                'page' => $profileData['page_gradient_stops'] ?? null,
                'bio_card' => $profileData['bio_card_gradient_stops'] ?? null,
            ],
        ]);

        $profile = $user
            ->profile()
            ->updateOrCreate(['user_id' => $user->id], $profileData);

        return response()->json([
            'message' => 'プロフィールが更新されました',
            'profile' => $profile,
        ]);
    }

    /**
     * Get the user's profile.
     */
    public function show($userId)
    {
        // デバッグ情報をログに出力
        \Log::info('Profile access attempt', [
            'userId' => $userId,
            'type' => gettype($userId),
        ]);

        // まずユーザーIDでユーザーを検索
        $user = User::where('id', $userId)
            ->orWhere('discord_id', $userId)
            ->first();

        \Log::info('User search result', [
            'userFound' => $user ? true : false,
            'userId' => $user ? $user->id : null,
            'discordId' => $user ? $user->discord_id : null,
        ]);

        if (!$user) {
            abort(404, 'ユーザーが見つかりません');
        }

        // ユーザーのプロフィールを取得
        $profile = $user->profile()->where('is_public', true)->first();

        \Log::info('Profile search result', [
            'profileFound' => $profile ? true : false,
            'isPublic' => $profile ? $profile->is_public : null,
            'gradientSettings' => [
                'card' => [
                    'enabled' => $profile
                        ? $profile->card_gradient_enabled
                        : null,
                    'stops' => $profile ? $profile->card_gradient_stops : null,
                    'direction' => $profile
                        ? $profile->card_gradient_direction
                        : null,
                    'stops_decoded' =>
                        $profile && $profile->card_gradient_stops
                            ? json_decode($profile->card_gradient_stops, true)
                            : null,
                ],
                'page' => [
                    'enabled' => $profile
                        ? $profile->page_gradient_enabled
                        : null,
                    'stops' => $profile ? $profile->page_gradient_stops : null,
                    'direction' => $profile
                        ? $profile->page_gradient_direction
                        : null,
                    'stops_decoded' =>
                        $profile && $profile->page_gradient_stops
                            ? json_decode($profile->page_gradient_stops, true)
                            : null,
                ],
                'bio_card' => [
                    'enabled' => $profile
                        ? $profile->bio_card_gradient_enabled
                        : null,
                    'stops' => $profile
                        ? $profile->bio_card_gradient_stops
                        : null,
                    'direction' => $profile
                        ? $profile->bio_card_gradient_direction
                        : null,
                    'stops_decoded' =>
                        $profile && $profile->bio_card_gradient_stops
                            ? json_decode($profile->page_gradient_stops, true)
                            : null,
                ],
            ],
        ]);

        if (!$profile) {
            abort(404, 'このプロフィールは公開されていません');
        }

        return view('profile.public', compact('profile'));
    }
}
