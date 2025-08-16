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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('display_name')->nullable(); // 表示名
            $table->text('bio')->nullable(); // 自己紹介
            $table->string('pronouns')->nullable(); // 代名詞
            $table->string('theme_color')->default('#3B82F6'); // テーマカラー
            $table->string('custom_avatar')->nullable(); // カスタムアバター
            $table->boolean('is_public')->default(false); // 公開設定
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
