<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Starting duplicate phone number fix...\n";

try {
    // Find duplicate phone numbers
    $duplicates = DB::table('users')
        ->select('phone', DB::raw('COUNT(*) as count'))
        ->whereNotNull('phone')
        ->where('phone', '!=', '')
        ->groupBy('phone')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->count() > 0) {
        echo "Found {$duplicates->count()} duplicate phone numbers. Fixing...\n";

        foreach ($duplicates as $duplicate) {
            echo "Processing duplicate phone: {$duplicate->phone}\n";

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
                
                echo "  - Set phone to null for user ID: {$user->id}\n";
            }
        }

        echo "Duplicate phone numbers fixed!\n";
    } else {
        echo "No duplicate phone numbers found.\n";
    }

    echo "Fix completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 