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
        // First, let's find and fix duplicate phone numbers
        $duplicates = DB::table('users')
            ->select('phone', DB::raw('COUNT(*) as count'))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all users with this phone number
            $users = DB::table('users')
                ->where('phone', $duplicate->phone)
                ->orderBy('id')
                ->get();

            // Keep the first user's phone number, set others to null
            $firstUser = $users->first();
            $otherUsers = $users->skip(1);

            foreach ($otherUsers as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['phone' => null]);
            }
        }

        // Now we can safely add the unique constraint, but only if it doesn't exist
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique();
            });
        } else {
            // If the column exists, just add the unique constraint
            Schema::table('users', function (Blueprint $table) {
                $table->unique('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });
    }
};
