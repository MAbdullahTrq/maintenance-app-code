<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum directly with a DB statement
        DB::statement("ALTER TABLE maintenance_requests MODIFY status ENUM('pending', 'accepted', 'assigned', 'started', 'completed', 'declined') NOT NULL DEFAULT 'pending'");
        
        // Update any existing 'approved' statuses to 'accepted'
        DB::statement("UPDATE maintenance_requests SET status = 'accepted' WHERE status = 'approved'");
        
        // Update any existing 'in_progress' statuses to 'started'
        DB::statement("UPDATE maintenance_requests SET status = 'started' WHERE status = 'in_progress'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE maintenance_requests MODIFY status ENUM('pending', 'approved', 'in_progress', 'completed', 'declined') NOT NULL DEFAULT 'pending'");
        
        // Convert back from 'accepted' to 'approved'
        DB::statement("UPDATE maintenance_requests SET status = 'approved' WHERE status = 'accepted'");
        
        // Convert back from 'started' to 'in_progress'
        DB::statement("UPDATE maintenance_requests SET status = 'in_progress' WHERE status = 'started'");
        
        // 'assigned' doesn't have a direct mapping in the old schema, so they'll need to be handled manually
    }
};
