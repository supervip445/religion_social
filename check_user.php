<?php

/**
 * User Check Script
 * 
 * This script allows you to check user information by user_name or phone
 * 
 * Usage:
 *   php check_user.php --user_name=superadmin
 *   php check_user.php --phone=09123456789
 *   php check_user.php --list-all
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Parse command line arguments
$options = getopt('', ['user_name:', 'phone:', 'list-all', 'help']);

// Show help
if (isset($options['help']) || (empty($options['user_name']) && empty($options['phone']) && !isset($options['list-all']))) {
    echo "\n";
    echo "=== User Check Script ===\n";
    echo "\n";
    echo "Usage:\n";
    echo "  php check_user.php --user_name=USERNAME\n";
    echo "  php check_user.php --phone=PHONE_NUMBER\n";
    echo "  php check_user.php --list-all\n";
    echo "  php check_user.php --help\n";
    echo "\n";
    echo "Examples:\n";
    echo "  php check_user.php --user_name=superadmin\n";
    echo "  php check_user.php --phone=09123456789\n";
    echo "  php check_user.php --list-all\n";
    echo "\n";
    exit(0);
}

// List all users
if (isset($options['list-all'])) {
    echo "\n";
    echo "=== All Users ===\n";
    echo "\n";
    
    $users = User::select('id', 'user_name', 'name', 'phone', 'email', 'type', 'status')
        ->orderBy('id')
        ->get();
    
    if ($users->isEmpty()) {
        echo "No users found.\n";
    } else {
        printf("%-5s %-20s %-30s %-15s %-30s %-15s %-10s\n", 
            'ID', 'User Name', 'Name', 'Phone', 'Email', 'Type', 'Status');
        echo str_repeat('-', 130) . "\n";
        
        foreach ($users as $user) {
            printf("%-5s %-20s %-30s %-15s %-30s %-15s %-10s\n",
                $user->id,
                $user->user_name ?? 'N/A',
                $user->name ?? 'N/A',
                $user->phone ?? 'N/A',
                $user->email ?? 'N/A',
                $user->type->value ?? 'N/A',
                $user->status == 1 ? 'Active' : 'Inactive'
            );
        }
    }
    echo "\n";
    exit(0);
}

// Check by user_name
if (isset($options['user_name'])) {
    $userName = $options['user_name'];
    
    echo "\n";
    echo "=== Searching by User Name: {$userName} ===\n";
    echo "\n";
    
    $user = User::where('user_name', $userName)->first();
    
    if (!$user) {
        echo "❌ User not found with user_name: {$userName}\n";
        echo "\n";
        exit(1);
    }
    
    displayUserInfo($user);
    exit(0);
}

// Check by phone
if (isset($options['phone'])) {
    $phone = $options['phone'];
    
    echo "\n";
    echo "=== Searching by Phone: {$phone} ===\n";
    echo "\n";
    
    $user = User::where('phone', $phone)->first();
    
    if (!$user) {
        echo "❌ User not found with phone: {$phone}\n";
        echo "\n";
        exit(1);
    }
    
    displayUserInfo($user);
    exit(0);
}

/**
 * Display user information in a formatted way
 */
function displayUserInfo(User $user)
{
    echo "✅ User Found!\n";
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  ID:                    {$user->id}\n";
    echo "  User Name:             " . ($user->user_name ?? 'N/A') . "\n";
    echo "  Name:                  " . ($user->name ?? 'N/A') . "\n";
    echo "  Phone:                 " . ($user->phone ?? 'N/A') . "\n";
    echo "  Email:                 " . ($user->email ?? 'N/A') . "\n";
    echo "  Type:                  " . ($user->type->value ?? 'N/A') . "\n";
    echo "  Status:                " . ($user->status == 1 ? '✅ Active' : '❌ Inactive') . "\n";
    echo "  Password Changed:      " . ($user->is_changed_password == 1 ? 'Yes' : 'No') . "\n";
    echo "  Email Verified:        " . ($user->email_verified_at ? '✅ ' . $user->email_verified_at->format('Y-m-d H:i:s') : '❌ Not verified') . "\n";
    echo "  Created At:            " . ($user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
    echo "  Updated At:            " . ($user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
    
    // Display roles
    $roles = $user->roles;
    if ($roles->isNotEmpty()) {
        echo "  Roles:                 ";
        $roleNames = $roles->pluck('title')->toArray();
        echo implode(', ', $roleNames) . "\n";
    } else {
        echo "  Roles:                 No roles assigned\n";
    }
    
    // Display permissions count
    $permissions = $user->getAllPermissions();
    if ($permissions->isNotEmpty()) {
        echo "  Permissions:            " . $permissions->count() . " permissions\n";
    } else {
        echo "  Permissions:            No permissions\n";
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
}

