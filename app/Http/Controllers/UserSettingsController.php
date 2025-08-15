<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSettingsController extends Controller
{
    /**
     * ユーザー設定ページを表示
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // プロフィール情報を取得（存在しない場合はnull）
        $profile = Profile::where('user_id', $user->id)->first();

        // プロフィールが存在する場合は完全性をチェック
        if ($profile) {
            $profileService = app(\App\Services\ProfileService::class);

            // 不完全なプロフィールの場合はクリーンアップ
            if (!$profileService->isProfileComplete($profile)) {
                $profileService->cleanupIncompleteProfile($user->id);
                $profile = null; // クリーンアップ後はnullに設定
            }
        }

        return view('user-settings', compact('user', 'profile'));
    }
}
