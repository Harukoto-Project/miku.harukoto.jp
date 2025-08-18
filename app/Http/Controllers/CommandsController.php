<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MikuApiService;

class CommandsController extends Controller
{
    protected $mikuApiService;

    public function __construct(MikuApiService $mikuApiService)
    {
        $this->mikuApiService = $mikuApiService;
    }

    /**
     * コマンド一覧ページを表示
     */
    public function index()
    {
        return view('commands');
    }

    /**
     * APIからコマンド一覧を取得
     */
    public function getCommands(Request $request)
    {
        try {
            Log::info('CommandsController: Starting to fetch commands');

            $commands = $this->mikuApiService->getCommands();

            Log::info('CommandsController: Response from MikuApiService', [
                'commands_type' => gettype($commands),
                'commands_null' => $commands === null,
                'commands_count' => is_array($commands)
                    ? count($commands)
                    : 'N/A',
                'commands_structure' => is_array($commands)
                    ? array_keys($commands)
                    : 'N/A',
            ]);

            if ($commands === null) {
                Log::warning('CommandsController: No commands found');
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'No commands found',
                        'message' => 'コマンドが見つかりませんでした',
                    ],
                    404,
                );
            }

            Log::info('CommandsController: Returning commands data', [
                'success' => $commands['success'] ?? 'N/A',
                'count' => $commands['count'] ?? 'N/A',
                'categories_count' => count($commands['categories'] ?? []),
                'commands_count' => count($commands['commands'] ?? []),
            ]);

            return response()->json($commands);
        } catch (\Exception $e) {
            Log::error('Commands API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Unexpected error',
                    'message' => '予期しないエラーが発生しました。',
                ],
                500,
            );
        }
    }

    /**
     * コマンド一覧のキャッシュをクリア
     */
    public function clearCache()
    {
        try {
            // キャッシュをクリア（必要に応じて実装）
            // Cache::forget('commands_list');

            Log::info('Commands cache cleared');

            return response()->json([
                'success' => true,
                'message' => 'キャッシュがクリアされました',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear commands cache', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Cache clear failed',
                    'message' => 'キャッシュのクリアに失敗しました',
                ],
                500,
            );
        }
    }
}
