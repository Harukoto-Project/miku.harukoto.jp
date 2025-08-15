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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique()->comment('DiscordユーザーID');
            $table->string('display_name', 50)->nullable()->comment('表示名');
            $table->text('bio')->nullable()->comment('自己紹介（最大500文字）');
            $table->string('pronouns', 32)->nullable()->comment('代名詞');
            $table->string('location', 32)->nullable()->comment('所在地');
            $table->text('avatar_url')->nullable()->comment('アバター画像URL');
            $table
                ->string('avatar_hash')
                ->unique()
                ->nullable()
                ->comment('アバターハッシュ');
            $table
                ->string('custom_url', 30)
                ->unique()
                ->nullable()
                ->comment('カスタムURL');
            $table->timestamps();

            $table->index('user_id');
            $table->index('avatar_hash');
            $table->index('custom_url');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
