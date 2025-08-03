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
        Schema::create('checklist_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('checklist_item_id')->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->text('text_response')->nullable();
            $table->string('response_attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_responses');
    }
};
