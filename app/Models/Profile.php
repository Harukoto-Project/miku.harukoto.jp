<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'pronouns',
        'location',
        'avatar_url',
        'avatar_hash',
        'custom_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * プロフィールリンクとのリレーション
     */
    public function links(): HasMany
    {
        return $this->hasMany(ProfileLink::class)->orderBy('sort_order');
    }

    /**
     * プロフィールテーマとのリレーション
     */
    public function theme(): HasOne
    {
        return $this->hasOne(ProfileTheme::class);
    }

    /**
     * ユーザーIDによるスコープ
     */
    public function scopeByUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * アバターハッシュによるスコープ
     */
    public function scopeByAvatarHash($query, $hash)
    {
        return $query->where('avatar_hash', $hash);
    }

    /**
     * カスタムURLによるスコープ
     */
    public function scopeByCustomUrl($query, $url)
    {
        return $query->where('custom_url', $url);
    }

    /**
     * 表示名の取得（フォールバック付き）
     */
    public function getDisplayNameAttribute($value)
    {
        return $value ?: 'Anonymous';
    }

    /**
     * 自己紹介の取得（改行対応）
     */
    public function getFormattedBioAttribute()
    {
        if (!$this->bio) {
            return '';
        }

        return nl2br(e($this->bio));
    }
}
