<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileTheme extends Model
{
    protected $fillable = [
        'profile_id',
        'background_color_type',
        'background_color_solid',
        'background_color_gradient_stops',
        'background_color_gradient_direction',
        'display_name_color_type',
        'display_name_color_solid',
        'display_name_color_gradient_stops',
        'display_name_color_gradient_direction',
        'pronouns_color_type',
        'pronouns_color_solid',
        'pronouns_color_gradient_stops',
        'pronouns_color_gradient_direction',
        'location_color_type',
        'location_color_solid',
        'location_color_gradient_stops',
        'location_color_gradient_direction',
        'bio_text_color_type',
        'bio_text_color_solid',
        'bio_text_color_gradient_stops',
        'bio_text_color_gradient_direction',
        'bio_background_color_type',
        'bio_background_color_solid',
        'bio_background_color_gradient_stops',
        'bio_background_color_gradient_direction',
    ];

    protected $casts = [
        'background_color_gradient_stops' => 'array',
        'background_color_gradient_direction' => 'integer',
        'display_name_color_gradient_stops' => 'array',
        'display_name_color_gradient_direction' => 'integer',
        'pronouns_color_gradient_stops' => 'array',
        'pronouns_color_gradient_direction' => 'integer',
        'location_color_gradient_stops' => 'array',
        'location_color_gradient_direction' => 'integer',
        'bio_text_color_gradient_stops' => 'array',
        'bio_text_color_gradient_direction' => 'integer',
        'bio_background_color_gradient_stops' => 'array',
        'bio_background_color_gradient_direction' => 'integer',
    ];

    /**
     * プロフィールとのリレーション
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * 背景色のCSSスタイルを取得
     */
    public function getBackgroundColorStyle(): string
    {
        if (
            $this->background_color_type === 'gradient' &&
            $this->background_color_gradient_stops
        ) {
            $stops = collect($this->background_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->background_color_gradient_direction}deg, {$stops})";
        }

        return $this->background_color_solid ?: '#ffffff';
    }

    /**
     * 表示名色のCSSスタイルを取得
     */
    public function getDisplayNameColorStyle(): string
    {
        if (
            $this->display_name_color_type === 'gradient' &&
            $this->display_name_color_gradient_stops
        ) {
            $stops = collect($this->display_name_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->display_name_color_gradient_direction}deg, {$stops})";
        }

        return $this->display_name_color_solid ?: '#1f2937';
    }

    /**
     * 代名詞色のCSSスタイルを取得
     */
    public function getPronounsColorStyle(): string
    {
        if (
            $this->pronouns_color_type === 'gradient' &&
            $this->pronouns_color_gradient_stops
        ) {
            $stops = collect($this->pronouns_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->pronouns_color_gradient_direction}deg, {$stops})";
        }

        return $this->pronouns_color_solid ?: '#7c3aed';
    }

    /**
     * 場所色のCSSスタイルを取得
     */
    public function getLocationColorStyle(): string
    {
        if (
            $this->location_color_type === 'gradient' &&
            $this->location_color_gradient_stops
        ) {
            $stops = collect($this->location_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->location_color_gradient_direction}deg, {$stops})";
        }

        return $this->location_color_solid ?: '#6b7280';
    }

    /**
     * 自己紹介テキスト色のCSSスタイルを取得
     */
    public function getBioTextColorStyle(): string
    {
        if (
            $this->bio_text_color_type === 'gradient' &&
            $this->bio_text_color_gradient_stops
        ) {
            $stops = collect($this->bio_text_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->bio_text_color_gradient_direction}deg, {$stops})";
        }

        return $this->bio_text_color_solid ?: '#ffffff';
    }

    /**
     * 自己紹介背景色のCSSスタイルを取得
     */
    public function getBioBackgroundColorStyle(): string
    {
        if (
            $this->bio_background_color_type === 'gradient' &&
            $this->bio_background_color_gradient_stops
        ) {
            $stops = collect($this->bio_background_color_gradient_stops)
                ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                ->join(', ');
            return "linear-gradient({$this->bio_background_color_gradient_direction}deg, {$stops})";
        }

        return $this->bio_background_color_solid ?: 'rgba(0, 0, 0, 0.1)';
    }
}
