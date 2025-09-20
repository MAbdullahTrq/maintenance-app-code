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
        Schema::table('checklist_items', function (Blueprint $table) {
            // Add new columns for multiple attachments
            $table->json('attachments')->nullable()->after('attachment_path');
            $table->renameColumn('attachment_path', 'attachment_path_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn('attachments');
            $table->renameColumn('attachment_path_old', 'attachment_path');
        });
    }
};
