<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileLink extends Model
{
    /**
     * 使用するテーブル名
     */
    protected $table = 'profile_links';

    /**
     * 外部キー制約の設定
     */
    protected $foreignKey = 'profile_id';

    protected $fillable = [
        'profile_id',
        'button_id',
        'url',
        'display_name',
        'custom_color',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * プロフィールとのリレーション
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * ボタンIDによるスコープ
     */
    public function scopeByButtonId($query, $buttonId)
    {
        return $query->where('button_id', $buttonId);
    }

    /**
     * ソート順によるスコープ
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * カスタムカラーの取得（デフォルト値付き）
     */
    public function getCustomColorAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // button_idが設定されている場合のみデフォルトカラーを取得
        if ($this->button_id) {
            return $this->getDefaultColor();
        }

        return '#6B7280';
    }

    /**
     * ボタンIDに応じたデフォルトカラーを取得
     */
    private function getDefaultColor(): string
    {
        $defaultColors = [
            'twitter' => '#1DA1F2',
            'github' => '#333333',
            'youtube' => '#FF0000',
            'discord' => '#5865F2',
            'instagram' => '#E4405F',
            'facebook' => '#1877F2',
            'linkedin' => '#0A66C2',
            'twitch' => '#9146FF',
            'reddit' => '#FF4500',
            'tiktok' => '#000000',
            'snapchat' => '#FFFC00',
            'pinterest' => '#BD081C',
            'telegram' => '#0088CC',
            'whatsapp' => '#25D366',
            'spotify' => '#1DB954',
            'steam' => '#171a21',
        ];

        return $defaultColors[$this->button_id] ?? '#6B7280';
    }

    /**
     * バリデーションルール
     */
    public static function rules(): array
    {
        return [
            'button_id' => 'required|string|max:50',
            'url' => 'required|url|max:2048',
            'display_name' => 'required|string|max:50',
            'custom_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
