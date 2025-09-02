<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_user_roles',
            'view_drivers',
            'create_drivers',
            'edit_drivers',
            'delete_drivers',
            'verify_drivers',
            'view_driver_availability',
            'manage_driver_availability',
            'view_countries',
            'create_countries',
            'edit_countries',
            'delete_countries',
            'view_cities',
            'create_cities',
            'edit_cities',
            'delete_cities',
            'view_districts',
            'create_districts',
            'edit_districts',
            'delete_districts',
            'view_vehicle_types',
            'create_vehicle_types',
            'edit_vehicle_types',
            'delete_vehicle_types',
            'view_vehicles',
            'edit_vehicles',
            'view_trips',
            'edit_trips',
            'delete_trips',
            'manage_trip_status',
            'view_contacts',
            'delete_contacts',
            'respond_contacts',
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'delete_tickets',
            'reply_tickets',
            'assign_tickets',
            'manage_ticket_status',
            'manage_ticket_priority',
            'manage_ticket_category',
            'view_internal_ticket_replies',
            'create_internal_ticket_replies',
            'view_ticket_categories',
            'create_ticket_categories',
            'edit_ticket_categories',
            'delete_ticket_categories',
            'view_ticket_stats',
            'view_wallets',
            'edit_wallets',
            'view_transactions',
            'manage_payments',
            'view_currencies',
            'create_currencies',
            'edit_currencies',
            'delete_currencies',
            'manage_country_currencies',
            'view_coupons',
            'create_coupons',
            'edit_coupons',
            'delete_coupons',
            'view_notifications',
            'send_notifications',
            'manage_notification_settings',
            'view_audit_logs',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            'assign_roles',
            'assign_permissions',
            'manage_system_settings',
            'view_system_stats',
            'export_data',
            'import_data',
            'manage_wallet_codes',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'admin' => [
                'description' => 'Full system access',
                'permissions' => $permissions
            ],
            'manager' => [
                'description' => 'Management level access',
                'permissions' => [
                    'view_dashboard',
                    'view_users',
                    'edit_users',
                    'view_drivers',
                    'edit_drivers',
                    'verify_drivers',
                    'view_driver_availability',
                    'view_countries',
                    'view_cities',
                    'view_districts',
                    'view_vehicle_types',
                    'view_vehicles',
                    'view_trips',
                    'edit_trips',
                    'manage_trip_status',
                    'view_contacts',
                    'respond_contacts',
                    'view_tickets',
                    'edit_tickets',
                    'reply_tickets',
                    'assign_tickets',
                    'manage_ticket_status',
                    'manage_ticket_priority',
                    'manage_ticket_category',
                    'view_internal_ticket_replies',
                    'create_internal_ticket_replies',
                    'view_ticket_stats',
                    'view_wallets',
                    'view_transactions',
                    'view_currencies',
                    'view_coupons',
                    'create_coupons',
                    'edit_coupons',
                    'view_notifications',
                    'send_notifications',
                    'view_system_stats',
                ]
            ],
            'support' => [
                'description' => 'Customer support access',
                'permissions' => [
                    'view_dashboard',
                    'view_users',
                    'edit_users',
                    'view_drivers',
                    'edit_drivers',
                    'view_trips',
                    'edit_trips',
                    'view_contacts',
                    'respond_contacts',
                    'delete_contacts',
                    'view_tickets',
                    'reply_tickets',
                    'assign_tickets',
                    'manage_ticket_status',
                    'view_ticket_stats',
                    'view_wallets',
                    'view_transactions',
                    'view_coupons',
                    'view_notifications',
                    'send_notifications',
                ]
            ],
            'editor' => [
                'description' => 'Content management access',
                'permissions' => [
                    'view_dashboard',
                    'view_countries',
                    'create_countries',
                    'edit_countries',
                    'view_cities',
                    'create_cities',
                    'edit_cities',
                    'view_districts',
                    'create_districts',
                    'edit_districts',
                    'view_vehicle_types',
                    'create_vehicle_types',
                    'edit_vehicle_types',
                    'view_currencies',
                    'create_currencies',
                    'edit_currencies',
                    'view_coupons',
                    'create_coupons',
                    'edit_coupons',
                    'view_notifications',
                    'send_notifications',
                    'view_ticket_categories',
                    'create_ticket_categories',
                    'edit_ticket_categories',
                ]
            ],
            'viewer' => [
                'description' => 'Read-only access',
                'permissions' => [
                    'view_dashboard',
                    'view_users',
                    'view_drivers',
                    'view_countries',
                    'view_cities',
                    'view_districts',
                    'view_vehicle_types',
                    'view_vehicles',
                    'view_trips',
                    'view_contacts',
                    'view_tickets',
                    'view_ticket_stats',
                    'view_wallets',
                    'view_transactions',
                    'view_currencies',
                    'view_coupons',
                    'view_notifications',
                    'view_system_stats',
                ]
            ],
            'driver' => [
                'description' => 'Driver mobile app access',
                'permissions' => [
                    'view_trips',
                    'edit_trips',
                    'view_wallets',
                    'view_notifications',
                ]
            ],
            'user' => [
                'description' => 'Customer mobile app access',
                'permissions' => [
                    'view_trips',
                    'view_wallets',
                    'view_notifications',
                ]
            ]
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($roleData['permissions']);
        }

        $adminUser = User::first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
            $this->command->info("Assigned admin role to user: {$adminUser->email}");
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created ' . count($permissions) . ' permissions and ' . count($roles) . ' roles.');
    }
}


