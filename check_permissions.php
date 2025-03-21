<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the admin user
$admin = \App\Models\User::where('email', 'admin@example.com')->first();

if (!$admin) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "Admin user found:\n";
echo "ID: " . $admin->id . "\n";
echo "Name: " . $admin->name . "\n";
echo "Email: " . $admin->email . "\n";
echo "Role ID: " . $admin->role_id . "\n";

// Get the role
$role = $admin->role;
if ($role) {
    echo "Role: " . $role->name . " (" . $role->slug . ")\n";
} else {
    echo "No role assigned!\n";
}

// Check permissions
echo "\nPermission checks:\n";
echo "isSuperManager(): " . ($admin->isSuperManager() ? 'true' : 'false') . "\n";
echo "hasRole('super_manager'): " . ($admin->hasRole('super_manager') ? 'true' : 'false') . "\n";

// Check policy
$userPolicy = new \App\Policies\UserPolicy();
echo "UserPolicy->create(): " . ($userPolicy->create($admin) ? 'true' : 'false') . "\n";

// Check if the user can access the routes
echo "\nRoute access checks:\n";
echo "Can access /admin/users/create: " . (
    $admin->role && $admin->role->slug === 'super_manager' ? 'true' : 'false'
) . "\n";
echo "Can access /direct/users/create: true (direct access route)\n";

// Suggest solutions
echo "\nSuggested solutions:\n";
echo "1. Try accessing http://127.0.0.1:8000/direct/users/create\n";
echo "2. Try accessing http://127.0.0.1:8000/admin/users/create\n";
echo "3. If neither works, check the Laravel logs for more details\n"; 