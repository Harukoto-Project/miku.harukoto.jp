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
        Schema::create('profile_links', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('profile_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('プロフィールID');
            $table->string('button_id', 50)->comment('ボタンタイプID');
            $table->text('url')->comment('リンク先URL');
            $table->string('display_name', 50)->comment('表示名');
            $table
                ->string('custom_color', 7)
                ->nullable()
                ->comment('カスタム色（HEX）');
            $table->integer('sort_order')->default(0)->comment('表示順序');
            $table->timestamps();

            $table->index('profile_id');
            $table->index('button_id');
            $table->index('sort_order');
            $table->unique(['profile_id', 'button_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_links');
    }
};
