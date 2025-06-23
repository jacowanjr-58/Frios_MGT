<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            FgpOrderSeeder::class,
        ]);

        // Define users to create/update
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@friospops.com',
                'password' => 'password',
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

                ];
                $user->givePermissionTo($superAdminPermissions);
                break;

            case 'corporate_admin':
                // Corporate Admin gets view-only access to specified modules
                $corporateAdminPermissions = [
                    'dashboard.view',
                    
                    // User & Role Management (view-only by default, Super Admin has full access)
                    'roles.view',
                    'permissions.view',
                    'users.view',

                    // Franchises Management (view-only by default, Super Admin has full access)
                    'franchises.view',
                    'owners.view',

                    // Frios Flavors Management (view-only by default)
                    'frios_flavors.view',
                    'frios_flavors.categories',
                    
                    // Frios Availability Management (view-only by default)
                    'frios_availability.view',
                    
                    // Flavor Category Management (view-only by default)
                    'flavor_category.view',
                    
                    // Franchise Orders Management (view-only by default)
                    'franchise_orders.view',
                    
                    // Payments Management (view-only by default)
                    'payments.view',
                    'payments.by_franchisee',
                    
                    // Expense Categories Management (view-only by default)
                    'expense_categories.view',
                    
                    // Additional Charges Management (view-only by default)
                    'additional_charges.view',

                    // View-only access to specified modules
                    'customers.view',
                    'customers.by_franchisee',
                    'events.view',
                    'events.calendar',
                    'events.report',
                    'orders.view',
                    'flavors.view',
                    'inventory.view',
                    'expenses.view',
                    'expenses.by_franchisee',

                    // Sidebar permissions (view-only)
                    'manage-franchises',
                    'manage-flavors',
                    'manage-frios-availability',
                    'manage-flavor-categories',
                    'manage-franchise-orders',
                    // 'view-payments',
                    'view-customers',
                    'view-events',
                    'view-inventory',
                    'view-orders',
                    'view-expenses',
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
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'orders.delete',
                    // 'orders.pops',
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
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'orders.delete',
                    // 'orders.pops',
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
