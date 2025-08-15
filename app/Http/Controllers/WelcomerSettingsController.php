<?php

namespace App\Http\Controllers;

use App\Services\MikuApiService;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WelcomerSettingsController extends Controller
{
    protected $mikuApiService;
    protected $discordService;

    public function __construct(MikuApiService $mikuApiService, DiscordService $discordService)
    {
        $this->mikuApiService = $mikuApiService;
        $this->discordService = $discordService;
    }

    /**
     * Welcomer設定を取得
     */
    public function getSettings(string $guildId)
    {
        $settings = $this->mikuApiService->getWelcomerSettings($guildId);

        if ($settings === null) {
            // デフォルト設定を返す
            $settings = [
                'guildId' => $guildId,
                'onJoin' => [
                    'channelId' => '',
                    'message' => 'ようこそ {user} さん！',
                    'isEnabled' => false,
                ],
                'onLeave' => [
                    'channelId' => '',
                    'message' => '{user} さんが退出しました。また来てね！',
                    'isEnabled' => false,
                ],
            ];
        }

        return response()->json($settings);
    }

    /**
     * 参加時のWelcomer設定を更新
     */
    public function updateJoinSettings(Request $request, string $guildId)
    {
        $validator = Validator::make($request->all(), [
            'channelId' => 'required|string',
            'message' => 'required|string|max:2000',
            'isEnabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $request->only(['channelId', 'message', 'isEnabled']);

        $success = $this->mikuApiService->updateJoinWelcomer($guildId, $data);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => '参加時のWelcomer設定を更新しました',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '設定の更新に失敗しました',
        ], 500);
    }

    /**
     * 退出時のWelcomer設定を更新
     */
    public function updateLeaveSettings(Request $request, string $guildId)
    {
        $validator = Validator::make($request->all(), [
            'channelId' => 'required|string',
            'message' => 'required|string|max:2000',
            'isEnabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $request->only(['channelId', 'message', 'isEnabled']);

        $success = $this->mikuApiService->updateLeaveWelcomer($guildId, $data);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => '退出時のWelcomer設定を更新しました',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '設定の更新に失敗しました',
        ], 500);
    }

    /**
     * ギルドのチャンネル一覧を取得（Botトークン使用・サーバーサイド）
     */
    public function getChannels(string $guildId)
    {
        $channels = $this->discordService->getGuildChannels($guildId);
        // テキストチャンネルのみ、最低限の情報で返却
        $filtered = array_values(array_map(function ($ch) {
            return [
                'id' => $ch['id'],
                'name' => $ch['name'],
                'type' => $ch['type'],
            ];
        }, array_filter($channels, function ($ch) {
            // Discordのテキストチャンネル type=0
            return isset($ch['type']) && $ch['type'] === 0;
        })));

        return response()->json([
            'channels' => $filtered,
        ]);
    }
}
