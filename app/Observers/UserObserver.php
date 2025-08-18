<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // APIトークンの自動生成はUserモデルのbootメソッドで処理
        // ここでは追加の処理を行う
        
        if (config('api-tokens.security.logging.enabled', true)) {
            Log::info('User created with API token', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_token' => !empty($user->api_token),
                'permissions' => $user->api_permissions,
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // APIトークン関連の変更をログに記録
        if ($user->wasChanged(['api_token', 'api_token_expires_at', 'api_permissions'])) {
            if (config('api-tokens.security.logging.enabled', true)) {
                Log::info('User API token updated', [
                    'user_id' => $user->id,
                    'changes' => [
                        'api_token' => $user->wasChanged('api_token'),
                        'api_token_expires_at' => $user->wasChanged('api_token_expires_at'),
                        'api_permissions' => $user->wasChanged('api_permissions'),
                    ],
                    'new_permissions' => $user->api_permissions,
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (config('api-tokens.security.logging.enabled', true)) {
            Log::info('User deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'had_token' => !empty($user->api_token),
            ]);
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // 復元されたユーザーに新しいAPIトークンを生成
        if (config('api-tokens.token.auto_generate', true)) {
            $user->generateApiToken();
            
            if (config('api-tokens.security.logging.enabled', true)) {
                Log::info('User restored with new API token', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        if (config('api-tokens.security.logging.enabled', true)) {
            Log::info('User force deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
    }
}
