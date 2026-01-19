<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management Permissions
            [
                'title' => 'user_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_activate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_deactivate',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Role & Permission Management
            [
                'title' => 'role_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'role_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'role_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'role_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'permission_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'permission_assign',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Dhamma Center Management
            [
                'title' => 'center_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'center_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'center_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'center_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Event Management
            [
                'title' => 'event_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'event_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'event_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'event_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'event_approve',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Dhamma Talk Management
            [
                'title' => 'dhamma_talk_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'dhamma_talk_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'dhamma_talk_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'dhamma_talk_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Meditation Session Management
            [
                'title' => 'meditation_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'meditation_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'meditation_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'meditation_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Donation Management
            [
                'title' => 'donation_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'donation_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'donation_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'donation_approve',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'donation_reject',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Content Management
            [
                'title' => 'content_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'content_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'content_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'content_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'content_publish',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Reports & Analytics
            [
                'title' => 'reports_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'reports_export',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'analytics_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'dashboard_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // System Settings
            [
                'title' => 'settings_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'settings_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'system_config_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'system_config_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Audit & Logs
            [
                'title' => 'audit_logs_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'user_logs_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'activity_logs_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Customer Support
            [
                'title' => 'support_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'support_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'support_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'support_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Notification Management
            [
                'title' => 'notification_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'notification_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'notification_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'notification_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Backup & Maintenance
            [
                'title' => 'backup_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'backup_restore',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'maintenance_mode',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Permission::insert($permissions);
    }
}

