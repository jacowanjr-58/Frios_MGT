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
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Roles using firstOrCreate to handle existing roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $corporateAdmin = Role::firstOrCreate(['name' => 'corporate_admin']);
        $franchiseAdmin = Role::firstOrCreate(['name' => 'franchise_admin']);
        $franchiseManager = Role::firstOrCreate(['name' => 'franchise_manager']);
        $franchiseStaff = Role::firstOrCreate(['name' => 'franchise_staff']);

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

            // User Management (for corporate_admin to assign permissions)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Franchises Management (Corporate Admin)
            'franchises.view',
            'franchises.create',
            'franchises.edit',
            'franchises.delete',

            // Franchisee/Owner Management (Corporate Admin)
            'owners.view',
            'owners.create',
            'owners.edit',
            'owners.delete',

            // Flavor Category Management (Corporate Admin)
            'flavor_category.view',
            'flavor_category.create',
            'flavor_category.edit',
            'flavor_category.delete',

            // Frios Flavors Management (Corporate Admin)
            'frios_flavors.view',
            'frios_flavors.create',
            'frios_flavors.edit',
            'frios_flavors.delete',

            // Frios Availability Management (Corporate Admin)
            'frios_availability.view',
            'frios_availability.create',
            'frios_availability.edit',
            'frios_availability.delete',

            // Orders  (Corporate Admin)
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.ups_generate_label',


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
            'expenses.by_category',
            'expenses.by_franchisee',
            'expenses.categories',

            // Expense Categories Management (Corporate Admin)
            'expense_categories.view',
            'expense_categories.create',
            'expense_categories.edit',
            'expense_categories.delete',

            // Customers Management (All roles)
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.by_franchisee',

            // Events Management (All roles)
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'events.calendar',
            'events.report',

            // Inventory Management (Franchise Admin & Manager)
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.bulk_adjust',
            'inventory.bulk_price_adjust',
            'inventory.allocate',
            'inventory.locations',

            // Orders Management (Franchise Admin & Manager)
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.add_discrepancy',
            'orders.view_discrepancy',
            'orders.ups_view_label',



            // Get Paid/Invoices (Franchise Admin)
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',

            // Transactions (Franchise Admin)
            'transactions.view',
            'transactions.create',
            'transactions.edit',
            'transactions.delete',

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

            // Flavors (Franchise Staff)
            'flavors.view',

            // Staff Management (Franchise Admin & Manager)
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',

            // Locations (Franchise Admin & Manager)
            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',

            // Accounts Management (Franchise Admin)
            'accounts.view',
            'accounts.create',
            'accounts.edit',
            'accounts.delete',

            // Additional Charges Management (Corporate Admin)
            'additional_charges.view',
            'additional_charges.create',
            'additional_charges.edit',
            'additional_charges.delete',

            // Sidebar-specific permissions for permission-based access control
            'manage-franchises',
            'manage-flavors',
            'manage-frios-availability',
            'manage-flavor-categories',

            'view-payments',
            'view-expenses',
            'view-customers',
            'view-events',
            'manage-inventory',
            'manage-orders',
            'manage-invoices',
            'manage-expenses',
            'manage-events',
            'view-inventory',
            'view-orders',
            'view-sales',
            'use-pos',
            'view-flavors',
            'manage-customers',


        ];

        // Create all permissions using firstOrCreate to handle existing permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Clear existing permissions for all roles to ensure clean state
        $superAdmin->syncPermissions([]);
        $corporateAdmin->syncPermissions([]);
        $franchiseAdmin->syncPermissions([]);
        $franchiseManager->syncPermissions([]);
        $franchiseStaff->syncPermissions([]);

        // Assign ALL permissions to super_admin
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

            //expenses
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.categories',
            'expenses.by_category',
            'expenses.by_franchisee',

            //availability
            'frios_availability.view',
            'frios_availability.create',
            'frios_availability.edit',
            'frios_availability.delete',

            //flavors
            'frios_flavors.view',
            'frios_flavors.create',
            'frios_flavors.edit',
            'frios_flavors.delete',

            //flavor_category
            'flavor_category.view',
            'flavor_category.create',
            'flavor_category.edit',
            'flavor_category.delete',

            //orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.view_discrepancy',
            'orders.add_discrepancy',
            'orders.ups_view_label',
            'orders.ups_generate_label',

            // Additional Charges Management (Corporate Admin)
            'additional_charges.view',
            'additional_charges.create',
            'additional_charges.edit',
            'additional_charges.delete',

            //customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.by_franchisee',

            //expense_categories
            'expense_categories.view',
            'expense_categories.create',
            'expense_categories.edit',
            'expense_categories.delete',
        ];
        $superAdmin->givePermissionTo($superAdminPermissions);
        // $superAdmin->givePermissionTo(Permission::all());

        // Assign specific permissions to corporate_admin (view-only for specified modules)
        $corporateAdminPermissions = [
            'dashboard.view',

            // Role Management (for corporate_admin to assign permissions)
            'roles.view',
            // 'permissions.assign',
            'permissions.view',

            // Staff Management (for corporate_admin to assign permissions)
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',

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

            //flavor_category
            'flavor_category.view',
            'flavor_category.create',
            'flavor_category.edit',
            'flavor_category.delete',

            // Frios Flavors Management (Corporate Admin)
            'frios_flavors.view',
            'frios_flavors.create',
            'frios_flavors.edit',
            'frios_flavors.delete',

            //availability
            'frios_availability.view',
            'frios_availability.create',
            'frios_availability.edit',
            'frios_availability.delete',

            //orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.view_discrepancy',
            'orders.ups_generate_label',
            'orders.ups_view_label',

            // Additional Charges Management (Corporate Admin)
            'additional_charges.view',
            'additional_charges.create',
            'additional_charges.edit',
            'additional_charges.delete',

            //expenses
            'expenses.view',
            'expenses.categories',
            'expenses.by_category',
            'expenses.by_franchisee',

            //expense_categories
            'expense_categories.view',
            'expense_categories.create',
            'expense_categories.edit',
            'expense_categories.delete',

            'inventory.view',
            'inventory.locations',

            // Events Management (All roles)
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'events.calendar',
            'events.report',

            // Get Paid/Invoices (Franchise Admin)
            'invoices.view',

            // POS System (Franchise Staff)
            'pos.view',
            'pos.create',
            'pos.edit',
            'pos.delete',
            'pos.access',

            // Sales Management (Franchise Staff)
            'sales.view',

            // Transactions (Franchise Admin)
            'transactions.view',
            'transactions.edit',

            //customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.by_franchisee',
        ];

        $corporateAdmin->givePermissionTo($corporateAdminPermissions);

        // Assign permissions to franchise_admin
        $franchiseAdminPermissions = [

            'dashboard.view',

            'roles.view',
            'permissions.view',

            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',

            'franchises.view',

            'frios_flavors.view',
            'frios_availability.view',
            'flavor_category.view',

            //orders
            'orders.view',
            'orders.create',
            'orders.ups_view_label',
            'orders.view_discrepancy',
            'orders.add_discrepancy',

            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.bulk_adjust',
            'inventory.bulk_price_adjust',
            'inventory.allocate',
            'inventory.locations',

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

            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',
            'accounts.view',
            'accounts.create',
            'accounts.edit',
            'accounts.delete',
            // Sidebar permissions for franchise admin
            'manage-inventory',
            'manage-orders',
            'manage-invoices',
            'manage-expenses',
            'manage-customers',
            'manage-events',
        ];
        $franchiseAdmin->givePermissionTo($franchiseAdminPermissions);

        // Assign permissions to franchise_manager
        $franchiseManagerPermissions = [
            'dashboard.view',
            'roles.view',
            'permissions.view',

            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',

            'franchises.view',

            //orders
            'orders.view',
            'orders.create',
            'orders.ups_view_label',
            'orders.view_discrepancy',
            'orders.add_discrepancy',

            'inventory.view',
            'inventory.edit',
            'inventory.locations',
            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',

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

            // Sidebar permissions for franchise manager (view-only access)
            'view-inventory',
            'view-orders',
            'view-expenses',
            'view-customers',
            'view-events',
        ];
        $franchiseManager->givePermissionTo($franchiseManagerPermissions);

        // Assign permissions to franchise_staff (mostly view permissions)
        $franchiseStaffPermissions = [
            'dashboard.view',

            'roles.view',
            'permissions.view',

            'staff.view',

            'frios_flavors.view',
            'frios_availability.view',
            'flavor_category.view',

            //orders
            'orders.view',
            'orders.create',

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
            // Sidebar permissions for franchise staff
            'use-pos',
            'view-flavors',
            'manage-customers',
            'view-sales',
            'view-events',
        ];
        $franchiseStaff->givePermissionTo($franchiseStaffPermissions);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Super Admin: ' . $superAdmin->permissions->count() . ' permissions');
        $this->command->info('Corporate Admin: ' . $corporateAdmin->permissions->count() . ' permissions');
        $this->command->info('Franchise Admin: ' . $franchiseAdmin->permissions->count() . ' permissions');
        $this->command->info('Franchise Manager: ' . $franchiseManager->permissions->count() . ' permissions');
        $this->command->info('Franchise Staff: ' . $franchiseStaff->permissions->count() . ' permissions');
    }
}
