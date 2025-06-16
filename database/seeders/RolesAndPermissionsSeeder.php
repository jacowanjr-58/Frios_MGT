<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate roles and permissions tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Roles
        $corporateAdmin = Role::create(['name' => 'corporate_admin']);
        $franchiseAdmin = Role::create(['name' => 'franchise_admin']);
        $franchiseManager = Role::create(['name' => 'franchise_manager']);
        $franchiseStaff = Role::create(['name' => 'franchise_staff']);

        // Define all permissions based on system modules
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Role Management (for corporate_admin to assign permissions)
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.assign',
            'permissions.view',

            // Franchises Management (Corporate Admin)
            'franchises.view',
            'franchises.create',
            'franchises.edit',
            'franchises.delete',
            'franchises.list',

            // Franchisee/Owner Management (Corporate Admin)
            'owners.view',
            'owners.create',
            'owners.edit',
            'owners.delete',
            'owners.list',

            // Frios Flavors Management (Corporate Admin)
            'frios_flavors.view',
            'frios_flavors.create',
            'frios_flavors.edit',
            'frios_flavors.delete',
            'frios_flavors.list',
            'frios_flavors.availability',
            'frios_flavors.categories',

            // Franchise Orders Management (Corporate Admin)
            'franchise_orders.view',
            'franchise_orders.create',
            'franchise_orders.edit',
            'franchise_orders.delete',
            'franchise_orders.list',
            'franchise_orders.edit_charges',

            // Payments Management (Corporate Admin)
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.by_franchisee',

            // Expenses Management (Corporate Admin & Franchise Admin/Manager)
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.list',
            'expenses.by_category',
            'expenses.by_franchisee',
            'expenses.categories',

            // Customers Management (All roles)
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.list',
            'customers.by_franchisee',

            // Events Management (All roles)
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'events.list',
            'events.calendar',
            'events.report',

            // Inventory Management (Franchise Admin & Manager)
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.list',
            'inventory.bulk_adjust',
            'inventory.bulk_price_adjust',
            'inventory.allocate',
            'inventory.locations',

            // Orders Management (Franchise Admin & Manager)
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.list',
            'orders.pops',

            // Get Paid/Invoices (Franchise Admin)
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.list',

            // Transactions (Franchise Admin)
            'transactions.view',
            'transactions.create',
            'transactions.edit',
            'transactions.delete',
            'transactions.list',

            // POS System (Franchise Staff)
            'pos.view',
            'pos.create',
            'pos.edit',
            'pos.delete',
            'pos.access',

            // Sales Management (Franchise Staff)
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.list',

            // Flavors (Franchise Staff)
            'flavors.view',
            'flavors.list',

            // Staff Management (Franchise Admin & Manager)
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'staff.list',

            // Locations (Franchise Admin & Manager)
            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',
            'locations.list',


            // users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.list',

            // Sidebar-specific permissions for permission-based access control
            'manage-users',
            'manage-franchises', 
            'manage-flavors',
            'manage-franchise-orders',
            'view-payments',
            'view-expenses',
            'view-customers', 
            'view-events',
            'manage-inventory',
            'manage-orders',
            'manage-invoices',
            'manage-expenses',
            'manage-customers',
            'manage-events',
            'view-inventory',
            'view-orders',
            'view-sales',
            'use-pos',
            'view-flavors',

        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign ALL permissions to corporate_admin
        $corporateAdmin->givePermissionTo(Permission::all());

        // Assign permissions to franchise_admin
        $franchiseAdminPermissions = [
            'dashboard.view',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete', 'inventory.list',
            'inventory.bulk_adjust', 'inventory.bulk_price_adjust', 'inventory.allocate', 'inventory.locations',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.list', 'orders.pops',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.list',
            'transactions.view', 'transactions.create', 'transactions.edit', 'transactions.delete', 'transactions.list',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.list', 'expenses.categories',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.list',
            'events.view', 'events.create', 'events.edit', 'events.delete', 'events.list', 'events.calendar', 'events.report',
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete', 'staff.list',
            'locations.view', 'locations.create', 'locations.edit', 'locations.delete', 'locations.list',
            // Sidebar permissions for franchise admin
            'manage-inventory', 'manage-orders', 'manage-invoices', 'manage-expenses', 'manage-customers', 'manage-events',
        ];
        $franchiseAdmin->givePermissionTo($franchiseAdminPermissions);

        // Assign permissions to franchise_manager
        $franchiseManagerPermissions = [
            'dashboard.view',
            'inventory.view', 'inventory.edit', 'inventory.list', 'inventory.locations',
            'locations.view', 'locations.create', 'locations.edit', 'locations.delete', 'locations.list',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.list', 'orders.pops',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.list', 'expenses.categories',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.list',
            'events.view', 'events.create', 'events.edit', 'events.delete', 'events.list', 'events.calendar', 'events.report',
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete', 'staff.list',
            // Sidebar permissions for franchise manager (view-only access)
            'view-inventory', 'view-orders', 'view-expenses', 'view-customers', 'view-events',
        ];
        $franchiseManager->givePermissionTo($franchiseManagerPermissions);

        // Assign permissions to franchise_staff (mostly view permissions)
        $franchiseStaffPermissions = [
            'dashboard.view',
            'pos.view', 'pos.create', 'pos.edit', 'pos.delete', 'pos.access',
            'flavors.view', 'flavors.list',
            'customers.view', 'customers.create', 'customers.edit', 'customers.list',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.list',
            'events.view', 'events.calendar', 'events.report',
            // Sidebar permissions for franchise staff
            'use-pos', 'view-flavors', 'manage-customers', 'view-sales', 'view-events',
        ];
        $franchiseStaff->givePermissionTo($franchiseStaffPermissions);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Corporate Admin: ' . $corporateAdmin->permissions->count() . ' permissions');
        $this->command->info('Franchise Admin: ' . $franchiseAdmin->permissions->count() . ' permissions');
        $this->command->info('Franchise Manager: ' . $franchiseManager->permissions->count() . ' permissions');
        $this->command->info('Franchise Staff: ' . $franchiseStaff->permissions->count() . ' permissions');
    }
} 