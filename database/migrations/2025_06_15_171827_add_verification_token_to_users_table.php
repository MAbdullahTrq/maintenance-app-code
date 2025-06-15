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
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('users', 'verification_token')) {
                $table->string('verification_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'verification_token_expires_at')) {
                $table->timestamp('verification_token_expires_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('users', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            if (Schema::hasColumn('users', 'verification_token_expires_at')) {
                $table->dropColumn('verification_token_expires_at');
            }
        });
    }
};
