<?php

namespace App\Http\Controllers;

use App\Services\DiscordService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    public function redirectToDiscord()
    {
        $clientId = config('services.discord.client_id');
        $redirectUri = config('services.discord.redirect_uri');

        // 設定値のログ出力（デバッグ用）
        Log::info('Discord OAuth Config', [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
        ]);

        if (empty($clientId) || empty($redirectUri)) {
            return redirect()
                ->route('home')
                ->with('error', 'Discord設定が不完全です。');
        }

        $scope = 'identify email guilds'; // guildsスコープを追加
        $state = Str::random(40);

        session(['discord_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ]);

        return redirect('https://discord.com/api/oauth2/authorize?' . $query);
    }

    public function handleDiscordCallback(Request $request)
    {
        $state = $request->get('state');
        $code = $request->get('code');
        $error = $request->get('error');

        // エラーパラメータがある場合
        if ($error) {
            Log::error('Discord OAuth Error', ['error' => $error]);
            return redirect()
                ->route('home')
                ->with('error', 'Discord認証がキャンセルされました。');
        }

        // コードが存在しない場合
        if (empty($code)) {
            Log::error('Discord OAuth: No authorization code received');
            return redirect()
                ->route('home')
                ->with('error', '認証コードが取得できませんでした。');
        }

        // ステート検証
        if (empty($state) || $state !== session('discord_state')) {
            Log::error('Discord OAuth: State mismatch', [
                'received_state' => $state,
                'session_state' => session('discord_state'),
            ]);
            return redirect()
                ->route('home')
                ->with('error', '認証状態が一致しません。');
        }

        try {
            // SSL証明書検証を強制的に無効化
            $httpOptions = [
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_CONNECTTIMEOUT => 30,
                ],
            ];

            Log::info('HTTP Client Options', $httpOptions);

            // トークン交換
            $tokenResponse = Http::withOptions($httpOptions)
                ->asForm()
                ->post('https://discord.com/api/oauth2/token', [
                    'client_id' => config('services.discord.client_id'),
                    'client_secret' => config('services.discord.client_secret'),
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => config('services.discord.redirect_uri'),
                ]);

            Log::info('Token Response Status', [
                'status' => $tokenResponse->status(),
            ]);

            if (!$tokenResponse->successful()) {
                Log::error('Discord Token Exchange Failed', [
                    'status' => $tokenResponse->status(),
                    'response' => $tokenResponse->body(),
                    'headers' => $tokenResponse->headers(),
                ]);
                throw new \Exception(
                    'Token exchange failed: ' .
                        $tokenResponse->status() .
                        ' - ' .
                        $tokenResponse->body(),
                );
            }

            $tokenData = $tokenResponse->json();

            if (!isset($tokenData['access_token'])) {
                Log::error('No access token in response', [
                    'response' => $tokenData,
                ]);
                throw new \Exception('No access token received');
            }

            $accessToken = $tokenData['access_token'];

            // アクセストークンをセッションに保存
            Session::put('discord_access_token', $accessToken);

            Log::info('Discord Token Exchange Successful');

            // ユーザー情報取得（同じSSL設定を使用）
            $userResponse = Http::withOptions($httpOptions)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'User-Agent' => 'DiscordBot (Laravel App, 1.0)',
                ])
                ->get('https://discord.com/api/users/@me');

            Log::info('User Response Status', [
                'status' => $userResponse->status(),
            ]);

            if (!$userResponse->successful()) {
                Log::error('Discord User Data Fetch Failed', [
                    'status' => $userResponse->status(),
                    'response' => $userResponse->body(),
                    'headers' => $userResponse->headers(),
                ]);
                throw new \Exception(
                    'Failed to fetch user data: ' .
                        $userResponse->status() .
                        ' - ' .
                        $userResponse->body(),
                );
            }

            $discordUser = $userResponse->json();

            if (!isset($discordUser['id'])) {
                Log::error('Invalid user data received', [
                    'response' => $discordUser,
                ]);
                throw new \Exception('Invalid user data received');
            }

            Log::info('Discord User Data Retrieved', [
                'discord_id' => $discordUser['id'],
                'username' => $discordUser['username'],
            ]);

            // ユーザー作成または更新
            $user = $this->discordService->findOrCreateUser($discordUser);

            Log::info('User created/updated', ['user_id' => $user->id]);

            // ログイン実行
            Auth::login($user, true);

            Log::info('User logged in successfully', ['user_id' => $user->id]);

            // セッションから不要な情報を削除
            session()->forget(['url.intended', 'discord_state']);

            // ログイン後のリダイレクト（明示的にダッシュボードに設定）
            return redirect()->intended(route('dashboard'))
                ->with('success', 'ログインしました！');
        } catch (\Exception $e) {
            Log::error('Discord OAuth Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route('home')
                ->with('error', 'ログインに失敗しました: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'ログアウトしました。');
    }
}
