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
            // カード背景色のグラデーション設定
            $table
                ->boolean('card_gradient_enabled')
                ->default(false)
                ->after('bio_text_color');
            $table
                ->text('card_gradient_stops')
                ->nullable()
                ->after('card_gradient_enabled');
            $table
                ->string('card_gradient_direction')
                ->default('45deg')
                ->after('card_gradient_stops');

            // ページ背景色のグラデーション設定
            $table
                ->boolean('page_gradient_enabled')
                ->default(false)
                ->after('card_gradient_direction');
            $table
                ->text('page_gradient_stops')
                ->nullable()
                ->after('page_gradient_enabled');
            $table
                ->string('page_gradient_direction')
                ->default('180deg')
                ->after('page_gradient_stops');

            // 自己紹介カード色のグラデーション設定
            $table
                ->boolean('bio_card_gradient_enabled')
                ->default(false)
                ->after('page_gradient_direction');
            $table
                ->text('bio_card_gradient_stops')
                ->nullable()
                ->after('bio_card_gradient_enabled');
            $table
                ->string('bio_card_gradient_direction')
                ->default('90deg')
                ->after('bio_card_gradient_stops');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'card_gradient_enabled',
                'card_gradient_stops',
                'card_gradient_direction',
                'page_gradient_enabled',
                'page_gradient_stops',
                'page_gradient_direction',
                'bio_card_gradient_enabled',
                'bio_card_gradient_stops',
                'bio_card_gradient_direction',
            ]);
        });
    }
};
