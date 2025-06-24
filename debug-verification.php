<?php
// Debug Email Verification System
// Access this via: https://yoursite.com/debug-verification.php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "<h1>Email Verification Debug Tool</h1>";

// Check if we have test parameters
$action = $_GET['action'] ?? 'info';
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if ($action === 'info') {
    echo "<h2>System Information</h2>";
    
    // Check routes
    echo "<h3>Verification Routes</h3>";
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $verificationRoutes = [];
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && str_contains($name, 'verification')) {
                $verificationRoutes[] = [
                    'name' => $name,
                    'uri' => $route->uri(),
                    'methods' => implode(', ', $route->methods()),
                ];
            }
        }
        
        echo "<table border='1'>";
        echo "<tr><th>Route Name</th><th>URI</th><th>Methods</th></tr>";
        foreach ($verificationRoutes as $route) {
            echo "<tr><td>{$route['name']}</td><td>{$route['uri']}</td><td>{$route['methods']}</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p>Error loading routes: " . $e->getMessage() . "</p>";
    }
    
    // Check recent users with verification tokens
    echo "<h3>Recent Users with Verification Tokens</h3>";
    try {
        $users = User::whereNotNull('verification_token')
            ->whereNotNull('verification_token_expires_at')
            ->where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        if ($users->count() > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Email</th><th>Token (first 10 chars)</th><th>Expires At</th><th>Is Active</th><th>Test Link</th></tr>";
            foreach ($users as $user) {
                $tokenPreview = substr($user->verification_token, 0, 10) . '...';
                $testUrl = route('verification.verify', ['token' => $user->verification_token]) . '?email=' . urlencode($user->email);
                echo "<tr>";
                echo "<td>{$user->id}</td>";
                echo "<td>{$user->email}</td>";
                echo "<td>{$tokenPreview}</td>";
                echo "<td>{$user->verification_token_expires_at}</td>";
                echo "<td>" . ($user->is_active ? 'Yes' : 'No') . "</td>";
                echo "<td><a href='{$testUrl}' target='_blank'>Test</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No users found with pending verification tokens.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error loading users: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Test Verification</h3>";
    echo "<form method='GET'>";
    echo "<input type='hidden' name='action' value='test'>";
    echo "<p>Email: <input type='email' name='email' value='' placeholder='user@example.com' required></p>";
    echo "<p>Token: <input type='text' name='token' value='' placeholder='verification_token' required></p>";
    echo "<p><button type='submit'>Test Verification</button></p>";
    echo "</form>";
    
} elseif ($action === 'test' && $email && $token) {
    echo "<h2>Testing Verification</h2>";
    echo "<p><strong>Email:</strong> {$email}</p>";
    echo "<p><strong>Token:</strong> " . substr($token, 0, 20) . "...</p>";
    
    try {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            echo "<p style='color: red;'>❌ User not found with email: {$email}</p>";
        } else {
            echo "<h3>User Information</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            echo "<tr><td>ID</td><td>{$user->id}</td></tr>";
            echo "<tr><td>Name</td><td>{$user->name}</td></tr>";
            echo "<tr><td>Email</td><td>{$user->email}</td></tr>";
            echo "<tr><td>Is Active</td><td>" . ($user->is_active ? 'Yes' : 'No') . "</td></tr>";
            echo "<tr><td>Stored Token</td><td>" . substr($user->verification_token ?? 'NULL', 0, 20) . "...</td></tr>";
            echo "<tr><td>Token Expires</td><td>{$user->verification_token_expires_at}</td></tr>";
            echo "<tr><td>Email Verified At</td><td>{$user->email_verified_at}</td></tr>";
            echo "</table>";
            
            echo "<h3>Verification Test</h3>";
            
            if ($user->is_active) {
                echo "<p style='color: orange;'>⚠️ User is already active</p>";
            } else {
                $isValid = $user->isValidVerificationToken($token);
                $tokenMatches = $user->verification_token === $token;
                $tokenNotExpired = $user->verification_token_expires_at && $user->verification_token_expires_at->isFuture();
                
                echo "<table border='1'>";
                echo "<tr><th>Check</th><th>Result</th></tr>";
                echo "<tr><td>Token Matches</td><td>" . ($tokenMatches ? '✅ Yes' : '❌ No') . "</td></tr>";
                echo "<tr><td>Token Not Expired</td><td>" . ($tokenNotExpired ? '✅ Yes' : '❌ No') . "</td></tr>";
                echo "<tr><td>Overall Valid</td><td>" . ($isValid ? '✅ Yes' : '❌ No') . "</td></tr>";
                echo "</table>";
                
                if ($isValid) {
                    echo "<p style='color: green;'>✅ Verification should work!</p>";
                    echo "<p><a href='" . route('verification.verify', ['token' => $token]) . "?email=" . urlencode($email) . "' target='_blank'>Click here to verify</a></p>";
                } else {
                    echo "<p style='color: red;'>❌ Verification will fail</p>";
                    if (!$tokenMatches) {
                        echo "<p>Provided token: " . substr($token, 0, 30) . "...</p>";
                        echo "<p>Stored token: " . substr($user->verification_token ?? 'NULL', 0, 30) . "...</p>";
                    }
                    if (!$tokenNotExpired) {
                        echo "<p>Token expires: {$user->verification_token_expires_at}</p>";
                        echo "<p>Current time: " . now() . "</p>";
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
    
    echo "<p><a href='?action=info'>← Back to Info</a></p>";
}

echo "<hr>";
echo "<p><small>Debug page - Remove this file in production</small></p>";
?> 