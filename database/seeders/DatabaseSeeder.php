<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Run roles and permissions seeder first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Define users to create/update
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@friospops.com',
                'password' => 'FriosAdmin456$',
                'role' => 'super_admin'
            ],
            [
                'name' => 'Corporate Admin',
                'email' => 'corporateadmin@friospops.com',
                'password' => 'password',
                'role' => 'corporate_admin'
            ],
            [
                'name' => 'Franchise Admin',
                'email' => 'franchiseadmin@friospops.com',
                'password' => 'password',
                'role' => 'franchise_admin'
            ],
            [
                'name' => 'Franchise Manager',
                'email' => 'franchisemanager@friospops.com',
                'password' => 'password',
                'role' => 'franchise_manager'
            ],
            [
                'name' => 'Franchise Staff',
                'email' => 'franchisestaff@friospops.com',
                'password' => 'password',
                'role' => 'franchise_staff'
            ],
        ];

        // Create or update users and assign roles/permissions
        foreach ($users as $userData) {
            // Check if user exists by email, create if not
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt($userData['password']),
                    'role' => $userData['role']
                ]
            );

            // Update role if user already exists
            $user->update(['role' => $userData['role']]);

            // Remove all existing roles and permissions
            $user->syncRoles([]);
            $user->syncPermissions([]);

            // Assign role
            $user->assignRole($userData['role']);

            // Assign permissions based on role (matching RolesAndPermissionsSeeder.php)
            $this->assignPermissionsByRole($user, $userData['role']);

            $this->command->info("User {$userData['email']} processed with role {$userData['role']}");
        }

        // Run other seeders after users are created
        $this->call([
            FranchiseSeeder::class,
            UserFranchiseSeeder::class,
            CustomerSeeder::class,
            FgpCategoriesTableSeeder::class,
            FgpItemsTableSeeder::class,
            FgpCategoryFgpItemTableSeeder::class,

            ExpenseCategorySeeder::class,
            FgpOrderSeeder::class,
            FgpOrderItemSeeder::class,
        ]);
    }

    /**
     * Assign permissions to user based on their role
     */
    private function assignPermissionsByRole(User $user, string $role)
    {
        switch ($role) {
            case 'super_admin':
                $superAdminPermissions = [
                    'dashboard.view',

                    // User Management - Full Access
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',

                    // Role & Permission Management - Full Access
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'permissions.assign',
                    'permissions.view',

                    // Franchise List - Full Access
                    'franchises.view',
                    'franchises.create',
                    'franchises.edit',
                    'franchises.delete',

                    // Franchise Owners - Full Access
                    'owners.view',
                    'owners.create',
                    'owners.edit',
                    'owners.delete',

                    // by_franchisee
                    'customers.by_franchisee',

                    //orders
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'orders.delete',


                ];
                $user->givePermissionTo($superAdminPermissions);
                break;

            case 'corporate_admin':
                // Corporate Admin gets view-only access to specified modules
                $corporateAdminPermissions = [
                    'dashboard.view',


                ];
                $user->givePermissionTo($corporateAdminPermissions);
                break;

            case 'franchise_admin':
                $franchiseAdminPermissions = [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.create',
                    'inventory.edit',
                    'inventory.delete',
                    'inventory.bulk_adjust',
                    'inventory.bulk_price_adjust',
                    'inventory.allocate',
                    'inventory.locations',

                    // Orders
                    'orders.view',
                    'orders.create',



                    'invoices.view',
                    'invoices.create',
                    'invoices.edit',
                    'invoices.delete',
                    'transactions.view',
                    'transactions.create',
                    'transactions.edit',
                    'transactions.delete',
                    'expenses.view',
                    'expenses.create',
                    'expenses.edit',
                    'expenses.delete',
                    'expenses.categories',
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                    'customers.delete',
                    'events.view',
                    'events.create',
                    'events.edit',
                    'events.delete',
                    'events.calendar',
                    'events.report',
                    'staff.view',
                    'staff.create',
                    'staff.edit',
                    'staff.delete',
                    'locations.view',
                    'locations.create',
                    'locations.edit',
                    'locations.delete',
                    'manage-inventory',
                    'manage-orders',
                    'manage-invoices',
                    'manage-expenses',
                    'manage-customers',
                    'manage-events',
                ];
                $user->givePermissionTo($franchiseAdminPermissions);
                break;

            case 'franchise_manager':
                $franchiseManagerPermissions = [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.edit',
                    'inventory.locations',
                    'locations.view',
                    'locations.create',
                    'locations.edit',
                    'locations.delete',

                    // Orders
                    'orders.view',
                    'orders.create',


                    'expenses.view',
                    'expenses.create',
                    'expenses.edit',
                    'expenses.delete',
                    'expenses.categories',
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                    'customers.delete',
                    'events.view',
                    'events.create',
                    'events.edit',
                    'events.delete',
                    'events.calendar',
                    'events.report',
                    'staff.view',
                    'staff.create',
                    'staff.edit',
                    'staff.delete',
                    'view-inventory',
                    'view-orders',
                    'view-expenses',
                    'view-customers',
                    'view-events',
                ];
                $user->givePermissionTo($franchiseManagerPermissions);
                break;

            case 'franchise_staff':
                $franchiseStaffPermissions = [
                    'dashboard.view',
                    'pos.view',
                    'pos.create',
                    'pos.edit',
                    'pos.delete',
                    'pos.access',
                    'flavors.view',
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                    'sales.view',
                    'sales.create',
                    'sales.edit',
                    'sales.delete',
                    'events.view',
                    'events.calendar',
                    'events.report',
                    'use-pos',
                    'view-flavors',
                    'manage-customers',
                    'view-sales',
                    'view-events',
                ];
                $user->givePermissionTo($franchiseStaffPermissions);
                break;
        }
    }
}
