<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicProfileController extends Controller
{
    /**
     * ユーザーIDでプロフィールを表示
     */
    public function show(string $userId): JsonResponse
    {
        $profile = Profile::with(['links', 'theme'])
            ->byUserId($userId)
            ->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    /**
     * カスタムURLでプロフィールを表示
     */
    public function showByCustomUrl(string $url): JsonResponse
    {
        $profile = Profile::with(['links', 'theme'])
            ->byCustomUrl($url)
            ->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    /**
     * アバターハッシュでプロフィール情報を取得
     */
    public function showByAvatarHash(string $hash): JsonResponse
    {
        $profile = Profile::select([
            'avatar_hash',
            'avatar_url',
            'display_name',
        ])
            ->byAvatarHash($hash)
            ->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'avatarHash' => $profile->avatar_hash,
            'avatarUrl' => $profile->avatar_url,
            'displayName' => $profile->display_name,
        ]);
    }
}
