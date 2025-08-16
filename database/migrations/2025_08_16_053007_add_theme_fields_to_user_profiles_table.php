<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table
                ->string('card_background_color')
                ->default('#3B82F6')
                ->after('theme_color');
            $table
                ->string('page_background_color')
                ->default('#F9FAFB')
                ->after('card_background_color');
            $table
                ->string('profile_name_color')
                ->default('#FFFFFF')
                ->after('page_background_color');
            $table
                ->string('pronouns_color')
                ->default('#E2E8F0')
                ->after('profile_name_color');
            $table
                ->string('location_color')
                ->default('#F1F5F9')
                ->after('pronouns_color');
            $table
                ->string('discord_color')
                ->default('#F1F5F9')
                ->after('location_color');
            $table
                ->string('bio_card_color')
                ->default('rgba(255, 255, 255, 0.1)')
                ->after('discord_color');
            $table
                ->string('bio_text_color')
                ->default('#FFFFFF')
                ->after('bio_card_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'card_background_color',
                'page_background_color',
                'profile_name_color',
                'pronouns_color',
                'location_color',
                'discord_color',
                'bio_card_color',
                'bio_text_color',
            ]);
        });
    }
};
