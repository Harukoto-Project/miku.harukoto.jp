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
        Schema::create('profile_themes', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('profile_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('プロフィールID');

            // 背景色設定
            $table
                ->enum('background_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('背景色タイプ');
            $table
                ->string('background_color_solid', 7)
                ->nullable()
                ->comment('単色（HEX）');
            $table
                ->json('background_color_gradient_stops')
                ->nullable()
                ->comment('グラデーション停止点');
            $table
                ->integer('background_color_gradient_direction')
                ->default(135)
                ->comment('グラデーション角度');

            // 表示名色設定
            $table
                ->enum('display_name_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('表示名色タイプ');
            $table
                ->string('display_name_color_solid', 7)
                ->nullable()
                ->comment('表示名単色（HEX）');
            $table
                ->json('display_name_color_gradient_stops')
                ->nullable()
                ->comment('表示名グラデーション停止点');
            $table
                ->integer('display_name_color_gradient_direction')
                ->default(0)
                ->comment('表示名グラデーション角度');

            // 代名詞色設定
            $table
                ->enum('pronouns_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('代名詞色タイプ');
            $table
                ->string('pronouns_color_solid', 7)
                ->nullable()
                ->comment('代名詞単色（HEX）');
            $table
                ->json('pronouns_color_gradient_stops')
                ->nullable()
                ->comment('代名詞グラデーション停止点');
            $table
                ->integer('pronouns_color_gradient_direction')
                ->default(0)
                ->comment('代名詞グラデーション角度');

            // 場所色設定
            $table
                ->enum('location_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('場所色タイプ');
            $table
                ->string('location_color_solid', 7)
                ->nullable()
                ->comment('場所単色（HEX）');
            $table
                ->json('location_color_gradient_stops')
                ->nullable()
                ->comment('場所グラデーション停止点');
            $table
                ->integer('location_color_gradient_direction')
                ->default(0)
                ->comment('場所グラデーション角度');

            // 自己紹介テキスト色設定
            $table
                ->enum('bio_text_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('自己紹介テキスト色タイプ');
            $table
                ->string('bio_text_color_solid', 7)
                ->nullable()
                ->comment('自己紹介テキスト単色（HEX）');
            $table
                ->json('bio_text_color_gradient_stops')
                ->nullable()
                ->comment('自己紹介テキストグラデーション停止点');
            $table
                ->integer('bio_text_color_gradient_direction')
                ->default(0)
                ->comment('自己紹介テキストグラデーション角度');

            // 自己紹介背景色設定
            $table
                ->enum('bio_background_color_type', ['solid', 'gradient'])
                ->default('solid')
                ->comment('自己紹介背景色タイプ');
            $table
                ->string('bio_background_color_solid', 7)
                ->nullable()
                ->comment('自己紹介背景単色（HEX）');
            $table
                ->json('bio_background_color_gradient_stops')
                ->nullable()
                ->comment('自己紹介背景グラデーション停止点');
            $table
                ->integer('bio_background_color_gradient_direction')
                ->default(0)
                ->comment('自己紹介背景グラデーション角度');

            $table->timestamps();

            $table->unique('profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_themes');
    }
};
