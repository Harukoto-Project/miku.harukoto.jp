<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 64)->nullable()->unique()->after('avatar_url');
            $table->timestamp('api_token_expires_at')->nullable()->after('api_token');
            $table->json('api_permissions')->nullable()->after('api_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['api_token', 'api_token_expires_at', 'api_permissions']);
        });
    }
};
