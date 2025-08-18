<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'discord_id',
        'discord_username',
        'discord_display_name',
        'avatar_url',
        'api_token',
        'api_token_expires_at',
        'api_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'api_token_expires_at' => 'datetime',
            'api_permissions' => 'array',
        ];
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate API token when user is created
        static::created(function ($user) {
            if (config('api-tokens.token.auto_generate', true)) {
                $user->generateApiToken();
            }
        });
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Generate a new API token for the user
     *
     * @param array|null $permissions
     * @param int|null $expirationHours
     * @return string
     */
    public function generateApiToken(?array $permissions = null, ?int $expirationHours = null): string
    {
        // Use default permissions if none specified
        if ($permissions === null) {
            $permissions = config('api-tokens.default_permissions', ['welcomer.read']);
        }

        // Use default expiration if none specified
        if ($expirationHours === null) {
            $expirationHours = config('api-tokens.token.default_expiration_hours', 24);
        }

        // Generate token
        $token = bin2hex(random_bytes(config('api-tokens.token.length_bytes', 32)));
        $expiresAt = now()->addHours($expirationHours);

        // Update user with new token
        $this->update([
            'api_token' => $token,
            'api_token_expires_at' => $expiresAt,
            'api_permissions' => $permissions,
        ]);

        // Log token generation
        if (config('api-tokens.security.logging.enabled', true)) {
            Log::info('API token generated for user', [
                'user_id' => $this->id,
                'permissions' => $permissions,
                'expires_at' => $expiresAt,
            ]);
        }

        return $token;
    }

    /**
     * Revoke the user's API token
     *
     * @return bool
     */
    public function revokeApiToken(): bool
    {
        $result = $this->update([
            'api_token' => null,
            'api_token_expires_at' => null,
            'api_permissions' => null,
        ]);

        if ($result && config('api-tokens.security.logging.enabled', true)) {
            Log::info('API token revoked for user', [
                'user_id' => $this->id,
            ]);
        }

        return $result;
    }

    /**
     * Check if the user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasApiPermission(string $permission): bool
    {
        $permissions = $this->api_permissions ?? [];
        return in_array('*', $permissions) || in_array($permission, $permissions);
    }

    /**
     * Check if the user has any of the specified permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyApiPermission(array $permissions): bool
    {
        $userPermissions = $this->api_permissions ?? [];
        
        // Check for wildcard permission
        if (in_array('*', $userPermissions)) {
            return true;
        }

        // Check for any of the required permissions
        return !empty(array_intersect($permissions, $userPermissions));
    }

    /**
     * Check if the user has all of the specified permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllApiPermissions(array $permissions): bool
    {
        $userPermissions = $this->api_permissions ?? [];
        
        // Check for wildcard permission
        if (in_array('*', $userPermissions)) {
            return true;
        }

        // Check for all required permissions
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Check if the user's API token is expired
     *
     * @return bool
     */
    public function isApiTokenExpired(): bool
    {
        if (!$this->api_token_expires_at) {
            return true;
        }

        return now()->isAfter($this->api_token_expires_at);
    }

    /**
     * Check if the user has a valid API token
     *
     * @return bool
     */
    public function hasValidApiToken(): bool
    {
        return !empty($this->api_token) && !$this->isApiTokenExpired();
    }

    /**
     * Renew the API token if it's expired
     *
     * @param int|null $expirationHours
     * @return bool
     */
    public function renewApiTokenIfExpired(?int $expirationHours = null): bool
    {
        if (!$this->isApiTokenExpired()) {
            return false; // Token is still valid
        }

        $this->generateApiToken(null, $expirationHours);
        return true;
    }

    /**
     * Get the user's API token information
     *
     * @return array
     */
    public function getApiTokenInfo(): array
    {
        return [
            'has_token' => !empty($this->api_token),
            'is_expired' => $this->isApiTokenExpired(),
            'expires_at' => $this->api_token_expires_at?->toISOString(),
            'permissions' => $this->api_permissions ?? [],
            'can_renew' => config('api-tokens.token.auto_renew', false),
        ];
    }
}
