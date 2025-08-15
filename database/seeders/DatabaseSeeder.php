<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\ProfileLink;
use App\Models\ProfileTheme;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // テストユーザーの作成
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'discord_id' => '123456789012345678',
            'discord_username' => 'testuser',
            'discord_display_name' => 'テストユーザー',
        ]);

        // テストプロフィールの作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'display_name' => 'テストユーザー',
            'bio' =>
                'これはテスト用のプロフィールです。\n自己紹介文の例として使用されています。',
            'pronouns' => 'he/him',
            'location' => '東京, Japan',
            'custom_url' => 'test-user',
        ]);

        // テストリンクの作成
        ProfileLink::create([
            'profile_id' => $profile->id,
            'button_id' => 'github',
            'url' => 'https://github.com/testuser',
            'display_name' => 'GitHub',
            'sort_order' => 1,
        ]);

        ProfileLink::create([
            'profile_id' => $profile->id,
            'button_id' => 'twitter',
            'url' => 'https://twitter.com/testuser',
            'display_name' => 'Twitter',
            'sort_order' => 2,
        ]);

        ProfileLink::create([
            'profile_id' => $profile->id,
            'button_id' => 'website',
            'url' => 'https://example.com',
            'display_name' => '公式サイト',
            'custom_color' => '#ff6b6b',
            'sort_order' => 3,
        ]);

        // テストテーマの作成
        ProfileTheme::create([
            'profile_id' => $profile->id,
            'background_color_type' => 'solid',
            'background_color_solid' => '#6366f1',
            'display_name_color_type' => 'solid',
            'display_name_color_solid' => '#ffffff',
            'pronouns_color_type' => 'solid',
            'pronouns_color_solid' => '#e2e8f0',
            'location_color_type' => 'solid',
            'location_color_solid' => '#f1f5f9',
            'bio_text_color_type' => 'solid',
            'bio_text_color_solid' => '#ffffff',
            'bio_background_color_type' => 'solid',
            'bio_background_color_solid' => '#ffffff',
        ]);
    }
}
