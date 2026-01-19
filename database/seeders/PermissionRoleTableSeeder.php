<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
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

        // Super Admin - All permissions
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // User - Limited permissions for regular members
        $userPermissions = Permission::whereIn('title', [
            'user_view',
            'user_edit',
            'center_view',
            'event_view',
            'dhamma_talk_view',
            'meditation_view',
            'donation_view',
            'donation_create',
            'content_view',
            'dashboard_view',
            'support_view',
            'support_create',
            'notification_view',
        ])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        $this->command->info('Permissions assigned successfully:');
        $this->command->info('- Super Admin: All permissions');
        $this->command->info('- User: Limited member permissions');
    }
}
