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
        Schema::table('properties', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropConstrainedForeignId('owner_id');
            
            // Add the foreign key constraint with RESTRICT instead of CASCADE
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop the RESTRICT constraint
            $table->dropConstrainedForeignId('owner_id');
            
            // Restore the original CASCADE constraint
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
        });
    }
};
