<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'pronouns',
        'location',
        'theme_color',
        'card_background_color',
        'page_background_color',
        'profile_name_color',
        'pronouns_color',
        'location_color',
        'discord_color',
        'bio_card_color',
        'bio_text_color',
        'card_gradient_enabled',
        'card_gradient_stops',
        'card_gradient_direction',
        'page_gradient_enabled',
        'page_gradient_stops',
        'page_gradient_direction',
        'bio_card_gradient_enabled',
        'bio_card_gradient_stops',
        'bio_card_gradient_direction',
        'custom_avatar',
        'avatar_path',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'card_gradient_enabled' => 'boolean',
        'page_gradient_enabled' => 'boolean',
        'bio_card_gradient_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
