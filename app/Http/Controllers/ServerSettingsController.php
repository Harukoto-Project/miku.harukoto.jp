<?php

namespace App\Http\Controllers;

use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ServerSettingsController extends Controller
{
    protected $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    /**
     * サーバー設定画面を表示
     */
    public function show(string $guildId)
    {
        $user = Auth::user();
        $discordToken = Session::get('discord_access_token');

        if (!$discordToken) {
            return redirect()->route('dashboard')
                ->with('error', 'Discordトークンが見つかりません。再度ログインしてください。');
        }

        // ユーザーが参加しているサーバー一覧を取得
        $allGuilds = $this->discordService->getUserGuilds($discordToken);
        
        // 指定されたサーバーを検索
        $targetGuild = null;
        foreach ($allGuilds as $guild) {
            if ($guild['id'] === $guildId) {
                // 管理者権限を持つサーバーのみ
                if (isset($guild['permissions']) && ($guild['permissions'] & 0x20) === 0x20) {
                    $targetGuild = $guild;
                }
                break;
            }
        }

        if (!$targetGuild) {
            return redirect()->route('dashboard')
                ->with('error', '指定されたサーバーが見つからないか、管理者権限がありません。');
        }

        // サーバー情報を整形
        $guild = [
            'id' => $targetGuild['id'],
            'name' => $targetGuild['name'],
            'icon_url' => !empty($targetGuild['icon'])
                ? "https://cdn.discordapp.com/icons/{$targetGuild['id']}/{$targetGuild['icon']}.png?size=128"
                : 'https://cdn.discordapp.com/embed/avatars/0.png',
        ];

        return view('server-settings', [
            'user' => $user,
            'guild' => $guild,
        ]);
    }
}
