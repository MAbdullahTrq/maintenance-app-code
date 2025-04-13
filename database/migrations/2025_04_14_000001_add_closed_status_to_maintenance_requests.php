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
        // Modify the enum to include 'closed'
        DB::statement("ALTER TABLE maintenance_requests MODIFY status ENUM('pending', 'accepted', 'assigned', 'acknowledged', 'started', 'completed', 'declined', 'closed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous enum values
        DB::statement("ALTER TABLE maintenance_requests MODIFY status ENUM('pending', 'accepted', 'assigned', 'acknowledged', 'started', 'completed', 'declined') NOT NULL DEFAULT 'pending'");
        
        // Convert any 'closed' statuses back to 'completed' for safety
        DB::statement("UPDATE maintenance_requests SET status = 'completed' WHERE status = 'closed'");
    }
}; 