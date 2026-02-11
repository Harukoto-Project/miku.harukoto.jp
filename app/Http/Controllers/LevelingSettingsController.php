<?php

namespace App\Http\Controllers;

use App\Services\DiscordService;
use App\Services\MikuApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelingSettingsController extends Controller
{
    protected $mikuApiService;
    protected $discordService;

    public function __construct(MikuApiService $mikuApiService, DiscordService $discordService)
    {
        $this->mikuApiService = $mikuApiService;
        $this->discordService = $discordService;
    }

    public function getRewards(string $guildId)
    {
        $rewards = $this->mikuApiService->getLevelingRewards($guildId);

        if ($rewards === null) {
            $rewards = [];
        }

        return response()->json([
            'guildId' => $guildId,
            'rewards' => $rewards,
        ]);
    }

    public function updateRewards(Request $request, string $guildId)
    {
        $validator = Validator::make($request->all(), [
            '*.level' => 'required|integer|min:1',
            '*.roleId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                400,
            );
        }

        $rewards = $request->all();

        $levels = array_map(
            function ($reward) {
                return $reward['level'];
            },
            $rewards,
        );

        if (count($levels) !== count(array_unique($levels))) {
            return response()->json(
                [
                    'success' => false,
                    'message' => '同じレベルに複数の報酬を設定することはできません。',
                ],
                400,
            );
        }

        $success = $this->mikuApiService->updateLevelingRewards($guildId, $rewards);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'レベル報酬の設定を更新しました',
            ]);
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'レベル報酬の設定の更新に失敗しました',
            ],
            500,
        );
    }

    public function getRoles(string $guildId)
    {
        $roles = $this->discordService->getGuildRoles($guildId);

        return response()->json([
            'roles' => $roles,
        ]);
    }
}

