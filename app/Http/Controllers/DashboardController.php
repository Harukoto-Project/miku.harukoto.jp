<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DiscordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    /**
     * ダッシュボード画面を表示
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // セッションからDiscordアクセストークンを取得
        $discordToken = Session::get('discord_access_token');

        // DiscordServiceから管理可能なサーバー一覧を取得
        $guilds = [];
        if ($user && $discordToken) {
            $allGuilds = $this->discordService->getUserGuilds($discordToken);
            $botGuilds = $this->discordService->getBotGuilds();

            // Botが参加しているサーバーIDの配列
            $botGuildIds = array_column($botGuilds, 'id');

            // サーバーごとに必要な情報を整形
            foreach ($allGuilds as $guild) {
                // 管理者権限を持つサーバーのみ
                if (
                    isset($guild['permissions']) &&
                    ($guild['permissions'] & 0x20) === 0x20
                ) {
                    $guilds[] = [
                        'id' => $guild['id'],
                        'name' => $guild['name'],
                        'icon_url' => !empty($guild['icon'])
                            ? "https://cdn.discordapp.com/icons/{$guild['id']}/{$guild['icon']}.png?size=128"
                            : 'https://cdn.discordapp.com/embed/avatars/0.png',
                        'has_miku' => in_array($guild['id'], $botGuildIds),
                    ];
                }
            }
        }

        return view('dashboard', [
            'user' => $user,
            'guilds' => $guilds,
        ]);
    }

    public function inviteBot(Request $request)
    {
        $guildId = $request->input('guild_id');
        $user = Auth::user();

        // セッションからDiscordアクセストークンを取得
        $discordToken = Session::get('discord_access_token');

        if (!$discordToken) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Discordトークンが見つかりません。再度ログインしてください。',
                );
        }

        $discordService = new DiscordService();

        $inviteUrl = $discordService->getInviteUrl($guildId, $discordToken);

        return redirect($inviteUrl);
    }
}
