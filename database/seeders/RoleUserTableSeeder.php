<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $superAdminRole = Role::where('title', 'Super Admin')->first();
        $userRole = Role::where('title', 'User')->first();

        if (!$superAdminRole || !$userRole) {
            $this->command->error('Roles not found. Please run RolesTableSeeder first.');
            return;
        }

        // Clear all existing role assignments
        \DB::table('role_user')->truncate();

        // Assign roles to existing users based on their type
        $users = User::all();
        $assignments = 0;

        foreach ($users as $user) {
            $this->command->line("Processing user: {$user->name} ({$user->user_name}) - Type: {$user->type->value}");
            
            // Assign role based on user type
            switch ($user->type->value) {
                case UserType::SuperAdmin->value:
                    $user->roles()->attach($superAdminRole->id);
                    $this->command->line("  -> Assigned Super Admin role");
                    $assignments++;
                    break;
                case UserType::User->value:
                    $user->roles()->attach($userRole->id);
                    $this->command->line("  -> Assigned User role");
                    $assignments++;
                    break;
                default:
                    $this->command->warn("  -> Unknown user type: {$user->type->value}");
                    break;
            }
        }

        $this->command->info("=== Assignment Complete ===");
        $this->command->info("Total role assignments made: {$assignments}");
        
        // Verify assignments
        $totalAssignments = \DB::table('role_user')->count();
        $this->command->info("Total role assignments in database: {$totalAssignments}");
        
        $this->command->info('Created accounts for each role type:');
        $this->command->info('- Super Admin: superadmin');
        $this->command->info('- User: user');
        $this->command->info('All accounts use password: delightmyanmar');
    }
}
