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
            $table->timestamp('trial_started_at')->nullable()->after('is_active');
            $table->timestamp('trial_expires_at')->nullable()->after('trial_started_at');
            $table->boolean('trial_extended')->default(false)->after('trial_expires_at');
            $table->timestamp('account_locked_at')->nullable()->after('trial_extended');
            $table->timestamp('data_deletion_at')->nullable()->after('account_locked_at');
            $table->integer('reminder_emails_sent')->default(0)->after('data_deletion_at');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_emails_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'trial_started_at',
                'trial_expires_at',
                'trial_extended',
                'account_locked_at',
                'data_deletion_at',
                'reminder_emails_sent',
                'last_reminder_sent_at'
            ]);
        });
    }
};
